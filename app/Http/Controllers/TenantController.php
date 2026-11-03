<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Support\Validasi;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/** CRUD mitra rental (tabel tb_tenant), khusus super admin. */
class TenantController extends Controller
{
    private const ATRIBUT = [
        'nama_rental' => 'nama rental',
    ];

    public function index()
    {
        $tenants = Tenant::withCount(['mobil', 'supir'])->orderBy('nama_rental')->paginate(10);

        return view('tenant.index', compact('tenants'));
    }

    public function create()
    {
        return view('tenant.form', ['tenant' => null]);
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->aturan(null), Validasi::pesan(), self::ATRIBUT);

        $tenant = Tenant::create($data);

        return redirect()->route('tenant.show', $tenant->id_tenant)
            ->with('success', 'Mitra berhasil ditambahkan. Buat akun untuk mitra ini di bawah.');
    }

    public function show($id)
    {
        $tenant = Tenant::withCount(['mobil', 'supir', 'pemesanan'])->findOrFail($id);
        $akun = $tenant->users()->whereIn('role', ['admin_rental', 'staff'])->orderBy('nama')->get();

        return view('tenant.show', compact('tenant', 'akun'));
    }

    public function edit($id)
    {
        return view('tenant.form', ['tenant' => Tenant::findOrFail($id)]);
    }

    public function update(Request $request, $id)
    {
        $tenant = Tenant::findOrFail($id);
        $data = $request->validate($this->aturan($tenant), Validasi::pesan(), self::ATRIBUT);

        $tenant->update($data);

        return redirect()->route('tenant.show', $tenant->id_tenant)->with('success', 'Data mitra berhasil diperbarui.');
    }

    /** "Hapus" = nonaktifkan. Mobil mitra otomatis tidak tampil di beranda pelanggan. */
    public function destroy($id)
    {
        $tenant = Tenant::findOrFail($id);
        $tenant->update(['status' => 'nonaktif']);

        return redirect()->route('tenant.index')
            ->with('success', "Mitra {$tenant->nama_rental} dinonaktifkan. Data tetap tersimpan.");
    }

    private function aturan(?Tenant $tenant): array
    {
        return [
            'nama_rental' => ['required', 'string', 'max:100'],
            'subdomain' => ['required', 'string', 'max:50', 'regex:/^[a-z0-9]+(-[a-z0-9]+)*$/',
                Rule::unique('tb_tenant', 'subdomain')->ignore($tenant?->id_tenant, 'id_tenant')],
            'alamat' => ['nullable', 'string', 'max:1000'],
            'telepon' => ['nullable', 'regex:/^[0-9+\-\s]{8,20}$/'],
            'email' => ['nullable', 'email', 'max:100'],
            'status' => ['required', 'in:aktif,nonaktif,suspend'],
        ];
    }
}
