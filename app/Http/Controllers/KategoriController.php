<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\MilikTenant;
use App\Models\KategoriMobil;
use App\Support\Validasi;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/** CRUD kategori mobil milik mitra (tabel tb_kategori_mobil). */
class KategoriController extends Controller
{
    use MilikTenant;

    public function index()
    {
        $kategori = KategoriMobil::withCount('mobil')
            ->where('id_tenant', $this->idTenant())
            ->orderBy('nama_kategori')
            ->get();

        return view('mitra.kategori.index', compact('kategori'));
    }

    public function create()
    {
        return view('mitra.kategori.form', ['kategori' => null]);
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->aturan(null), Validasi::pesan(), ['nama_kategori' => 'nama kategori']);
        $data['id_tenant'] = $this->idTenant();

        KategoriMobil::create($data);

        return redirect()->route('mitra.kategori.index')->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function edit($id)
    {
        return view('mitra.kategori.form', ['kategori' => $this->cari($id)]);
    }

    public function update(Request $request, $id)
    {
        $kategori = $this->cari($id);
        $data = $request->validate($this->aturan($kategori), Validasi::pesan(), ['nama_kategori' => 'nama kategori']);

        $kategori->update($data);

        return redirect()->route('mitra.kategori.index')->with('success', 'Kategori berhasil diperbarui.');
    }

    /**
     * Tabel kategori tidak punya kolom status, jadi tidak bisa dinonaktifkan.
     * Kategori yang masih dipakai mobil tidak boleh dihapus.
     */
    public function destroy($id)
    {
        $kategori = $this->cari($id);

        if ($kategori->mobil()->exists()) {
            return back()->with('error', 'Kategori masih dipakai oleh mobil. Pindahkan atau nonaktifkan mobilnya dulu.');
        }

        $kategori->delete();

        return redirect()->route('mitra.kategori.index')->with('success', 'Kategori dihapus.');
    }

    private function cari($id): KategoriMobil
    {
        return KategoriMobil::where('id_tenant', $this->idTenant())->findOrFail($id);
    }

    private function aturan(?KategoriMobil $kategori): array
    {
        return [
            'nama_kategori' => ['required', 'string', 'max:50',
                Rule::unique('tb_kategori_mobil', 'nama_kategori')
                    ->where('id_tenant', $this->idTenant())
                    ->ignore($kategori?->id_kategori, 'id_kategori')],
            'deskripsi' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
