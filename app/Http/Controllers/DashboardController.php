<?php

namespace App\Http\Controllers;

use App\Models\Mobil;
use App\Models\Pemesanan;
use App\Models\Supir;
use App\Models\Tenant;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->hasRole('pelanggan')) {
            return redirect()->route('beranda');
        }

        if ($user->hasRole('admin')) {
            return view('dashboard', [
                'grup' => 'admin',
                'stat' => [
                    ['Mitra aktif', Tenant::where('status', 'aktif')->count() . ' / ' . Tenant::count()],
                    ['Total mobil', Mobil::count()],
                    ['Total pemesanan', Pemesanan::count()],
                    ['Akun pelanggan', User::where('role', 'pelanggan')->count()],
                ],
                'terbaru' => Pemesanan::with(['mobil', 'tenant', 'pelanggan'])->latest('tanggal_pesan')->limit(5)->get(),
            ]);
        }

        abort_if(! $user->id_tenant, 403, 'Akun ini belum terhubung ke mitra rental.');
        $t = $user->id_tenant;

        return view('dashboard', [
            'grup' => 'mitra',
            'stat' => [
                ['Mobil tersedia', Mobil::where('id_tenant', $t)->where('status', 'tersedia')->count()],
                ['Mobil disewa', Mobil::where('id_tenant', $t)->where('status', 'disewa')->count()],
                ['Supir tersedia', Supir::where('id_tenant', $t)->where('status', 'tersedia')->count()],
                ['Pesanan menunggu', Pemesanan::where('id_tenant', $t)->where('status', 'menunggu')->count()],
            ],
            'terbaru' => Pemesanan::with(['mobil', 'pelanggan'])->where('id_tenant', $t)->latest('tanggal_pesan')->limit(5)->get(),
        ]);
    }
}
