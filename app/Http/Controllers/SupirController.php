<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\MilikTenant;
use App\Models\Supir;
use App\Support\Validasi;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/** CRUD supir milik mitra (tabel tb_supir). */
class SupirController extends Controller
{
    use MilikTenant;

    private const ATRIBUT = [
        'nama_supir' => 'nama supir',
        'no_sim' => 'nomor SIM',
        'tarif_per_hari' => 'tarif per hari',
    ];

    public function index()
    {
        $supir = Supir::where('id_tenant', $this->idTenant())->orderBy('nama_supir')->get();

        return view('mitra.supir.index', compact('supir'));
    }

    public function create()
    {
        return view('mitra.supir.form', ['supir' => null]);
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->aturan(null), Validasi::pesan(), self::ATRIBUT);
        $data['id_tenant'] = $this->idTenant();

        Supir::create($data);

        return redirect()->route('mitra.supir.index')->with('success', 'Supir berhasil ditambahkan.');
    }

    public function edit($id)
    {
        return view('mitra.supir.form', ['supir' => $this->cari($id)]);
    }

    public function update(Request $request, $id)
    {
        $supir = $this->cari($id);
        $data = $request->validate($this->aturan($supir), Validasi::pesan(), self::ATRIBUT);

        // Status "bertugas" diatur otomatis oleh alur pemesanan.
        if ($supir->status === 'bertugas') {
            unset($data['status']);
        }

        $supir->update($data);

        return redirect()->route('mitra.supir.index')->with('success', 'Data supir berhasil diperbarui.');
    }

    /** "Hapus" = nonaktifkan, riwayat pemesanan tetap utuh. */
    public function destroy($id)
    {
        $supir = $this->cari($id);

        if ($supir->status === 'bertugas') {
            return back()->with('error', 'Supir sedang bertugas, tidak bisa dinonaktifkan.');
        }

        $supir->update(['status' => 'nonaktif']);

        return redirect()->route('mitra.supir.index')
            ->with('success', 'Supir dinonaktifkan. Untuk mengaktifkan lagi, ubah statusnya menjadi Tersedia.');
    }

    private function cari($id): Supir
    {
        return Supir::where('id_tenant', $this->idTenant())->findOrFail($id);
    }

    private function aturan(?Supir $supir): array
    {
        return [
            'nama_supir' => ['required', 'string', 'max:100'],
            'telepon' => ['required', 'regex:/^[0-9+\-\s]{8,20}$/'],
            'no_sim' => ['required', 'string', 'max:30',
                Rule::unique('tb_supir', 'no_sim')
                    ->where('id_tenant', $this->idTenant())
                    ->ignore($supir?->id_supir, 'id_supir')],
            'tarif_per_hari' => ['required', 'numeric', 'min:0', 'max:99999999'],
            'status' => $supir?->status === 'bertugas'
                ? ['nullable']
                : ['required', 'in:tersedia,nonaktif'],
        ];
    }
}
