<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\MilikTenant;
use App\Models\Pemesanan;
use Illuminate\Support\Facades\Storage;

class DokumenPelangganController extends Controller
{
    use MilikTenant;

    /**
     * Dokumen identitas hanya dapat dilihat staf mitra pemilik booking.
     * File tidak dipublikasikan melalui storage:link.
     */
    public function show(int $id, string $jenis)
    {
        abort_unless(in_array($jenis, ['ktp', 'sim'], true), 404);

        $pesanan = Pemesanan::with('pelanggan')
            ->where('id_tenant', $this->idTenant())
            ->findOrFail($id);

        $path = $jenis === 'ktp'
            ? $pesanan->pelanggan->foto_ktp
            : $pesanan->pelanggan->foto_sim;

        abort_unless($path && Storage::disk('local')->exists($path), 404);

        return Storage::disk('local')->response($path);
    }
}
