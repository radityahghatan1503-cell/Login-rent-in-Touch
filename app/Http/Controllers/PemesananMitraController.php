<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\MilikTenant;
use App\Models\Mobil;
use App\Models\Pemesanan;
use App\Models\Supir;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/** Pemesanan masuk untuk mitra: daftar, detail, dan perubahan status. */
class PemesananMitraController extends Controller
{
    use MilikTenant;

    private const STATUS = ['menunggu', 'dikonfirmasi', 'berjalan', 'selesai', 'dibatalkan'];

    /** Alur status yang diizinkan. */
    public const ALUR = [
        'menunggu' => ['dikonfirmasi', 'dibatalkan'],
        'dikonfirmasi' => ['berjalan', 'dibatalkan'],
        'berjalan' => ['selesai'],
        'selesai' => [],
        'dibatalkan' => [],
    ];

    public function index(Request $request)
    {
        $status = $request->query('status');
        $cari = trim((string) $request->query('q', ''));
        $t = $this->idTenant();

        $pesanan = Pemesanan::with(['mobil', 'pelanggan'])
            ->where('id_tenant', $t)
            ->when(in_array($status, self::STATUS, true), fn ($q) => $q->where('status', $status))
            ->when($cari !== '', function ($q) use ($cari) {
                $like = '%' . addcslashes($cari, '%_\\') . '%';
                $q->where(fn ($w) => $w->where('kode_pemesanan', 'like', $like)
                    ->orWhereHas('pelanggan', fn ($p) => $p->where('nama_lengkap', 'like', $like)));
            })
            ->orderByDesc('tanggal_pesan')
            ->paginate(10)
            ->withQueryString();

        $hitung = Pemesanan::where('id_tenant', $t)
            ->selectRaw('status, count(*) as n')
            ->groupBy('status')
            ->pluck('n', 'status');

        return view('mitra.pemesanan.index', compact('pesanan', 'status', 'cari', 'hitung'));
    }

    public function show($id)
    {
        $p = Pemesanan::with(['mobil.kategori', 'tenant', 'supir', 'pelanggan', 'pembayaran', 'pengembalian', 'staff'])
            ->where('id_tenant', $this->idTenant())
            ->findOrFail($id);

        return view('mitra.pemesanan.show', ['p' => $p, 'alur' => self::ALUR[$p->status]]);
    }

    public function status(Request $request, $id)
    {
        $p = Pemesanan::where('id_tenant', $this->idTenant())->findOrFail($id);
        $baru = $request->input('status');
        $lama = $p->status;

        if (! in_array($baru, self::ALUR[$lama] ?? [], true)) {
            return back()->with('error', 'Perubahan status tidak diizinkan dari "' . $lama . '".');
        }

        DB::transaction(function () use ($p, $baru, $lama) {
            $p->status = $baru;
            if ($baru === 'dikonfirmasi') {
                $p->id_staff = auth()->id();
            }
            $p->save();

            // Sinkronkan status unit & supir dengan jalannya sewa.
            if ($baru === 'berjalan') {
                Mobil::where('id_mobil', $p->id_mobil)->where('status', 'tersedia')->update(['status' => 'disewa']);
                if ($p->id_supir) {
                    Supir::where('id_supir', $p->id_supir)->where('status', 'tersedia')->update(['status' => 'bertugas']);
                }
            }

            if ($lama === 'berjalan' && in_array($baru, ['selesai', 'dibatalkan'], true)) {
                Mobil::where('id_mobil', $p->id_mobil)->where('status', 'disewa')->update(['status' => 'tersedia']);
                if ($p->id_supir) {
                    Supir::where('id_supir', $p->id_supir)->where('status', 'bertugas')->update(['status' => 'tersedia']);
                }
            }
        });

        return back()->with('success', 'Status pesanan diubah menjadi ' . $baru . '.');
    }
}
