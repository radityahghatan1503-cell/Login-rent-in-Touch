<?php

namespace App\Http\Controllers;

use App\Models\Pemesanan;
use Illuminate\Http\Request;

/** Status pesanan & detail pemesanan milik pelanggan yang login. */
class PesananController extends Controller
{
    private const STATUS = ['menunggu', 'dikonfirmasi', 'berjalan', 'selesai', 'dibatalkan'];

    /** Satu akun bisa punya beberapa baris tb_pelanggan (satu per mitra). */
    private function milikSaya()
    {
        return Pemesanan::whereHas('pelanggan', fn ($q) => $q->where('id_user', auth()->id()));
    }

    public function index(Request $request)
    {
        $status = $request->query('status');

        $pesanan = $this->milikSaya()
            ->with(['mobil', 'tenant'])
            ->when(in_array($status, self::STATUS, true), fn ($q) => $q->where('status', $status))
            ->orderByDesc('tanggal_pesan')
            ->paginate(8)
            ->withQueryString();

        $hitung = $this->milikSaya()
            ->selectRaw('status, count(*) as n')
            ->groupBy('status')
            ->pluck('n', 'status');

        return view('pesanan.index', compact('pesanan', 'status', 'hitung'));
    }

    public function show($kode)
    {
        $p = $this->milikSaya()
            ->with(['mobil.kategori', 'tenant', 'supir', 'pelanggan', 'pembayaran', 'pengembalian'])
            ->where('kode_pemesanan', $kode)
            ->firstOrFail();

        return view('pesanan.show', compact('p'));
    }

    /** Pelanggan hanya boleh membatalkan pesanan yang masih menunggu konfirmasi. */
    public function batal($kode)
    {
        $p = $this->milikSaya()->where('kode_pemesanan', $kode)->firstOrFail();

        if ($p->status !== 'menunggu') {
            return back()->with('error', 'Pesanan ini sudah diproses mitra, tidak bisa dibatalkan dari sini. Hubungi mitra.');
        }

        $p->update(['status' => 'dibatalkan']);

        return redirect()->route('pesanan.show', $p->kode_pemesanan)->with('success', 'Pesanan dibatalkan.');
    }
}
