<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use Illuminate\Http\Request;

class TenantController extends Controller
{
    public function index()
    {
        $tenants = Tenant::all();

        return view('tenant.index', compact('tenants'));
    }

    public function create()
    {
        return view('tenant.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_rental' => 'required',
            'subdomain' => 'required|unique:tb_tenant,subdomain',
            'alamat' => 'nullable',
            'telepon' => 'nullable',
            'email' => 'nullable|email',
            'status' => 'required',
        ]);

        Tenant::create([
            'nama_rental' => $request->nama_rental,
            'subdomain' => $request->subdomain,
            'alamat' => $request->alamat,
            'telepon' => $request->telepon,
            'email' => $request->email,
            'status' => $request->status,
        ]);
            
        

        return redirect()
            ->route('tenant.index')
            ->with('success', 'Tenant berhasil ditambahkan.');
    }
      public function edit($id_tenant)
    {
         $tenant = Tenant::findOrFail($id_tenant);
         return view('tenant.edit', compact('tenant'));
    }
     public function update(Request $request, $id_tenant)
    {
        $tenant = Tenant::findOrFail($id_tenant);
     
        $request ->validate([
            'nama_rental' => 'required',
            'subdomain' => 'required|unique:tb_tenant,subdomain,' .$id_tenant . ',id_tenant',
            'alamat' => 'nullable',
             'email' => 'nullable|email',
            'status' => 'required',
  ]);
         $tenant->update([
            'nama_rental' => $request->nama_rental,
            'subdomain' => $request->subdomain,
            'alamat' => $request->alamat,
            'telepon' => $request->telepon,
            'email' => $request->email,
            'status' => $request->status,
        ]);

        return redirect()
            ->route('tenant.index')
            ->with('success', 'Tenant berhasil diperbarui.');
    }
    public function destroy($id_tenant)
{
    $tenant = Tenant::findOrFail($id_tenant);

    $tenant->delete();

    return redirect()
        ->route('tenant.index')
        ->with('success', 'Tenant berhasil dihapus.');
}
}