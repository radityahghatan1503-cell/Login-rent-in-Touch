<?php

namespace App\Http\Controllers;

use App\Models\Mobil;
use App\Models\Pelanggan;
use App\Models\Pemesanan;
use App\Models\Supir;
use App\Notifications\BookingReceived;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Throwable;

/**
 * Pengajuan booking pelanggan.
 *
 * Dokumen identitas disimpan pada disk private (storage/app), bukan public.
 * Pembayaran tidak dibuat pada tahap ini; booking menunggu keputusan mitra.
 */
class BookingController extends Controller
{
    private const STATUS_MENAHAN = ['menunggu', 'dikonfirmasi', 'berjalan'];

    public function store(Request $request, int $id)
    {
        $mobil = Mobil::with('tenant')
            ->whereIn('status', ['tersedia', 'disewa'])
            ->whereHas('tenant', fn ($q) => $q->where('status', 'aktif'))
            ->findOrFail($id);

        $pelangganLama = Pelanggan::where('id_tenant', $mobil->id_tenant)
            ->where('id_user', auth()->id())
            ->first();

        $rules = [
            'tanggal_mulai' => ['required', 'date_format:Y-m-d', 'after_or_equal:today'],
            'tanggal_selesai' => ['required', 'date_format:Y-m-d', 'after_or_equal:tanggal_mulai'],
            'lokasi_jemput' => ['nullable', 'string', 'max:255'],
            'lokasi_kembali' => ['nullable', 'string', 'max:255'],
            'id_supir' => ['nullable', 'integer'],
            'nik' => ['required', 'digits:16'],
            'nama_lengkap' => ['required', 'string', 'max:100'],
            'telepon' => ['required', 'string', 'max:20'],
            'alamat' => ['nullable', 'string', 'max:2000'],
            'no_sim' => ['required', 'string', 'max:30'],
            'foto_ktp' => [$pelangganLama?->foto_ktp ? 'nullable' : 'required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'foto_sim' => [$pelangganLama?->foto_sim ? 'nullable' : 'required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ];

        $data = $request->validate($rules, [
            'tanggal_mulai.after_or_equal' => 'Tanggal mulai tidak boleh sebelum hari ini.',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai harus sama atau setelah tanggal mulai.',
            'nik.digits' => 'NIK harus terdiri dari 16 angka.',
            'foto_ktp.required' => 'Silakan unggah foto atau scan KTP.',
            'foto_sim.required' => 'Silakan unggah foto atau scan SIM.',
            'foto_ktp.mimes' => 'File KTP harus berupa JPG, PNG, atau PDF.',
            'foto_sim.mimes' => 'File SIM harus berupa JPG, PNG, atau PDF.',
            'foto_ktp.max' => 'Ukuran file KTP maksimal 5 MB.',
            'foto_sim.max' => 'Ukuran file SIM maksimal 5 MB.',
        ]);

        if (! empty($data['id_supir'])) {
            $supirValid = Supir::where('id_supir', $data['id_supir'])
                ->where('id_tenant', $mobil->id_tenant)
                ->where('status', 'tersedia')
                ->exists();

            if (! $supirValid) {
                throw ValidationException::withMessages([
                    'id_supir' => 'Supir yang dipilih tidak tersedia untuk mobil ini.',
                ]);
            }
        }

        $mulai = Carbon::createFromFormat('Y-m-d', $data['tanggal_mulai'])->startOfDay();
        $selesai = Carbon::createFromFormat('Y-m-d', $data['tanggal_selesai'])->endOfDay();
        $lamaHari = max(1, $mulai->copy()->diffInDays($selesai->copy()->startOfDay()));

        $fileBaru = [];

        try {
            if ($request->hasFile('foto_ktp')) {
                $fileBaru['foto_ktp'] = $request->file('foto_ktp')->store('identitas/ktp', 'local');
            }
            if ($request->hasFile('foto_sim')) {
                $fileBaru['foto_sim'] = $request->file('foto_sim')->store('identitas/sim', 'local');
            }

            $pemesanan = DB::transaction(function () use (
                $data, $mobil, $pelangganLama, $mulai, $selesai, $lamaHari, $fileBaru
            ) {
                // Kunci baris mobil agar dua request bersamaan tidak dapat
                // meloloskan pemeriksaan ketersediaan untuk unit yang sama.
                $unit = Mobil::where('id_mobil', $mobil->id_mobil)
                    ->lockForUpdate()
                    ->firstOrFail();

                if (! in_array($unit->status, ['tersedia', 'disewa'], true)
                    || $unit->tenant()->where('status', 'aktif')->doesntExist()) {
                    throw ValidationException::withMessages([
                        'tanggal_mulai' => 'Mobil ini sedang tidak dapat dipesan.',
                    ]);
                }

                $bentrok = Pemesanan::where('id_mobil', $unit->id_mobil)
                    ->whereIn('status', self::STATUS_MENAHAN)
                    ->where('tanggal_mulai', '<=', $selesai)
                    ->where('tanggal_selesai', '>=', $mulai)
                    ->exists();

                if ($bentrok) {
                    throw ValidationException::withMessages([
                        'tanggal_mulai' => 'Mobil sudah memiliki pesanan pada rentang tanggal tersebut. Silakan pilih tanggal lain.',
                    ]);
                }

                $idSupir = $data['id_supir'] ?? null;
                $tarifSupir = 0;

                if ($idSupir) {
                    $supir = Supir::where('id_supir', $idSupir)
                        ->where('id_tenant', $unit->id_tenant)
                        ->lockForUpdate()
                        ->first();

                    if (! $supir || $supir->status !== 'tersedia') {
                        throw ValidationException::withMessages([
                            'id_supir' => 'Supir yang dipilih sudah tidak tersedia.',
                        ]);
                    }

                    $supirBentrok = Pemesanan::where('id_supir', $idSupir)
                        ->whereIn('status', self::STATUS_MENAHAN)
                        ->where('tanggal_mulai', '<=', $selesai)
                        ->where('tanggal_selesai', '>=', $mulai)
                        ->exists();

                    if ($supirBentrok) {
                        throw ValidationException::withMessages([
                            'id_supir' => 'Supir sudah memiliki jadwal pada tanggal tersebut. Pilih supir lain atau lepas kunci.',
                        ]);
                    }

                    $tarifSupir = (float) $supir->tarif_per_hari;
                }

                $pelanggan = Pelanggan::updateOrCreate(
                    [
                        'id_tenant' => $unit->id_tenant,
                        'id_user' => auth()->id(),
                    ],
                    [
                        'nik' => $data['nik'],
                        'nama_lengkap' => $data['nama_lengkap'],
                        'telepon' => $data['telepon'],
                        'alamat' => $data['alamat'] ?? null,
                        'no_sim' => $data['no_sim'],
                        'foto_ktp' => $fileBaru['foto_ktp'] ?? $pelangganLama?->foto_ktp,
                        'foto_sim' => $fileBaru['foto_sim'] ?? $pelangganLama?->foto_sim,
                    ]
                );

                $kode = 'PSN-' . now()->format('ymd') . '-' . Str::upper(Str::random(5));
                while (Pemesanan::where('kode_pemesanan', $kode)->exists()) {
                    $kode = 'PSN-' . now()->format('ymd') . '-' . Str::upper(Str::random(5));
                }

                return Pemesanan::create([
                    'id_tenant' => $unit->id_tenant,
                    'id_pelanggan' => $pelanggan->id_pelanggan,
                    'id_mobil' => $unit->id_mobil,
                    'id_supir' => $idSupir,
                    'id_staff' => null,
                    'kode_pemesanan' => $kode,
                    'tanggal_pesan' => now(),
                    'tanggal_mulai' => $mulai,
                    'tanggal_selesai' => $selesai,
                    'lokasi_jemput' => $data['lokasi_jemput'] ?? null,
                    'lokasi_kembali' => $data['lokasi_kembali'] ?? null,
                    'lama_sewa_hari' => $lamaHari,
                    'total_biaya' => ((float) $unit->harga_per_hari + $tarifSupir) * $lamaHari,
                    'status' => 'menunggu',
                ]);
            });
        } catch (Throwable $e) {
            foreach ($fileBaru as $path) {
                Storage::disk('local')->delete($path);
            }
            throw $e;
        }

        $penerima = $pemesanan->tenant->users()
            ->whereIn('role', ['admin_rental', 'staff'])
            ->where('is_active', true)
            ->get();

        if ($penerima->isNotEmpty()) {
            try {
                Notification::send($penerima, new BookingReceived($pemesanan));
            } catch (Throwable $e) {
                // Jangan menggagalkan booking yang sudah tersimpan jika kanal notifikasi bermasalah.
                Log::error('Gagal mengirim notifikasi booking kepada mitra.', [
                    'booking_id' => $pemesanan->id_pemesanan,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return redirect()
            ->route('pesanan.show', $pemesanan->kode_pemesanan)
            ->with('success', 'Pengajuan booking berhasil dikirim. Mitra akan meninjau pesanan Anda.');
    }
}
