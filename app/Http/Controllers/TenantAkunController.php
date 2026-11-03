<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\User;
use App\Support\Validasi;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;

/** Super admin membuat dan mengaktifkan/menonaktifkan akun mitra (admin_rental, staff). */
class TenantAkunController extends Controller
{
    public function store(Request $request, $tenant)
    {
        $tenant = Tenant::findOrFail($tenant);

        $data = $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:100', 'unique:tb_users,email'],
            'role' => ['required', 'in:admin_rental,staff'],
            'password' => ['required', Password::min(8)->letters()->numbers()],
        ], Validasi::pesan(), ['nama' => 'nama', 'password' => 'kata sandi']);

        User::create($data + ['id_tenant' => $tenant->id_tenant, 'is_active' => true]);

        return redirect()->route('tenant.show', $tenant->id_tenant)->with('success', 'Akun mitra dibuat.');
    }

    public function toggle($tenant, $user)
    {
        $akun = User::where('id_tenant', $tenant)
            ->whereIn('role', ['admin_rental', 'staff'])
            ->findOrFail($user);

        $akun->update(['is_active' => ! $akun->is_active]);

        return back()->with('success', $akun->is_active ? 'Akun diaktifkan.' : 'Akun dinonaktifkan.');
    }
}
