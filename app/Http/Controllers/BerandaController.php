<?php

namespace App\Http\Controllers;

use App\Models\KategoriMobil;
use App\Models\Mobil;
use App\Models\Supir;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BerandaController extends Controller
{
    /** Status pemesanan yang menahan unit (unit dianggap terpakai pada rentang tanggalnya). */
    private const STATUS_MENAHAN = ['menunggu', 'dikonfirmasi', 'berjalan'];

    public function index(Request $request)
    {
        $jenisList = KategoriMobil::query()
            ->orderBy('nama_kategori')
            ->pluck('nama_kategori')
            ->unique()
            ->values()
            ->prepend('Semua jenis')
            ->all();

        $request->validate([
            'lokasi' => ['nullable', 'string', 'max:100'],
            'mulai' => ['nullable', 'date', 'after_or_equal:today'],
            'selesai' => ['nullable', 'date', 'after_or_equal:mulai'],
            'jenis' => ['nullable', Rule::in($jenisList)],
        ], [
            'mulai.after_or_equal' => 'Tanggal mulai tidak boleh sebelum hari ini.',
            'selesai.after_or_equal' => 'Tanggal selesai tidak boleh sebelum tanggal mulai.',
            'jenis.in' => 'Jenis kendaraan tidak dikenal.',
        ]);

        $lokasi = trim((string) $request->input('lokasi', ''));
        $mulai = $request->input('mulai', now()->toDateString());
        $selesai = $request->input('selesai', now()->addDays(3)->toDateString());
        $jenis = $request->input('jenis', 'Semua jenis');

        $awal = Carbon::parse($mulai)->startOfDay();
        $akhir = Carbon::parse($selesai)->endOfDay();
        $lamaHari = max(1, $awal->diffInDays(Carbon::parse($selesai)->startOfDay()));

        // Unit yang secara umum bisa disewa: status mobil bukan perawatan/nonaktif, mitra aktif.
        $dasar = Mobil::query()
            ->whereIn('status', ['tersedia', 'disewa'])
            ->whereHas('tenant', fn ($q) => $q->where('status', 'aktif'));

        $jumlahUnit = (clone $dasar)->count();
        $jumlahMitra = (clone $dasar)->distinct()->count('id_tenant');

        // Lokasi dicocokkan ke alamat mitra (belum ada kolom kota di tb_tenant).
        $kota = trim(explode(',', $lokasi)[0]);

        $rupiah = fn ($n) => 'Rp ' . number_format($n, 0, ',', '.');

        $mobil = (clone $dasar)
            ->with([
                'kategori',
                'tenant' => fn ($q) => $q->withCount([
                    'supir as supir_tersedia_count' => fn ($s) => $s->where('status', 'tersedia'),
                ]),
            ])
            ->when($kota !== '', function ($q) use ($kota) {
                $like = '%' . addcslashes($kota, '%_\\') . '%';
                $q->whereHas('tenant', fn ($t) => $t->where('alamat', 'like', $like));
            })
            ->when($jenis !== 'Semua jenis', function ($q) use ($jenis) {
                $q->whereHas('kategori', fn ($k) => $k->where('nama_kategori', $jenis));
            })
            // Buang unit yang sudah dipesan pada rentang tanggal yang dipilih.
            ->whereDoesntHave('pemesanan', function ($q) use ($awal, $akhir) {
                $q->whereIn('status', self::STATUS_MENAHAN)
                    ->where('tanggal_mulai', '<=', $akhir)
                    ->where('tanggal_selesai', '>=', $awal);
            })
            ->orderBy('harga_per_hari')
            ->get()
            ->map(function (Mobil $m) use ($rupiah, $lamaHari, $mulai, $selesai) {
                $adaSopir = $m->tenant->supir_tersedia_count > 0;

                // Semua unit bisa disewa lepas kunci (id_supir di pemesanan boleh kosong).
                $tags = [$m->transmisi === 'matic' ? 'matic' : 'manual', 'lepas_kunci'];
                $layanan = ['Lepas kunci'];
                if ($adaSopir) {
                    $tags[] = 'sopir';
                    $layanan[] = 'Dengan sopir';
                }
                if ($m->kapasitas_penumpang >= 7) {
                    $tags[] = 'kursi7';
                }

                return [
                    'id' => $m->id_mobil,
                    'url' => route('mobil.detail', ['id' => $m->id_mobil, 'mulai' => $mulai, 'selesai' => $selesai]),
                    'mitra' => $m->tenant->nama_rental,
                    'nama' => $m->merek . ' ' . $m->model,
                    'foto' => $m->foto,
                    'spek' => implode(' · ', array_filter([
                        $m->kategori?->nama_kategori,
                        $m->tahun,
                        ucfirst($m->transmisi),
                        $m->kapasitas_penumpang . ' kursi',
                    ])),
                    'harga_fmt' => $rupiah($m->harga_per_hari),
                    'total_fmt' => $rupiah($m->harga_per_hari * $lamaHari),
                    'tags' => implode(' ', $tags),
                    'layanan' => $layanan,
                ];
            });

        return view('pelanggan.beranda', [
            'mobil' => $mobil,
            'jumlahUnit' => $jumlahUnit,
            'jumlahMitra' => $jumlahMitra,
            'jenisList' => $jenisList,
            'lokasi' => $lokasi,
            'mulai' => $mulai,
            'selesai' => $selesai,
            'jenis' => $jenis,
            'lamaHari' => $lamaHari,
        ]);
    }

    /** Halaman detail satu mobil. */
    public function show(Request $request, $id)
    {
        $mobil = Mobil::with(['kategori', 'tenant'])
            ->whereIn('status', ['tersedia', 'disewa'])
            ->whereHas('tenant', fn ($q) => $q->where('status', 'aktif'))
            ->findOrFail($id);

        [$mulai, $selesai] = $this->rentang($request);
        $awal = Carbon::parse($mulai)->startOfDay();
        $akhir = Carbon::parse($selesai)->endOfDay();
        $lamaHari = max(1, $awal->diffInDays(Carbon::parse($selesai)->startOfDay()));

        // Apakah unit sudah dipesan pada rentang tanggal ini?
        $bentrok = $mobil->pemesanan()
            ->whereIn('status', self::STATUS_MENAHAN)
            ->where('tanggal_mulai', '<=', $akhir)
            ->where('tanggal_selesai', '>=', $awal)
            ->exists();

        $supir = Supir::where('id_tenant', $mobil->id_tenant)
            ->where('status', 'tersedia')
            ->orderBy('tarif_per_hari')
            ->get();

        // Untuk menu sidebar "Detail & Pemesanan"
        session(['mobil_terakhir_url' => $request->fullUrl()]);

        return view('pelanggan.mobil-detail', compact('mobil', 'mulai', 'selesai', 'lamaHari', 'bentrok', 'supir'));
    }

    /** Ambil rentang tanggal dari query; kalau tidak valid pakai default 3 hari dari hari ini. */
    private function rentang(Request $request): array
    {
        $hariIni = Carbon::today();
        $mulai = $hariIni->copy();
        $selesai = $hariIni->copy()->addDays(3);

        try {
            if ($request->filled('mulai') && $request->filled('selesai')) {
                $m = Carbon::createFromFormat('Y-m-d', $request->query('mulai'))->startOfDay();
                $s = Carbon::createFromFormat('Y-m-d', $request->query('selesai'))->startOfDay();
                if ($m->gte($hariIni) && $s->gte($m)) {
                    [$mulai, $selesai] = [$m, $s];
                }
            }
        } catch (\Throwable $e) {
            // abaikan, pakai default
        }

        return [$mulai->toDateString(), $selesai->toDateString()];
    }
}
