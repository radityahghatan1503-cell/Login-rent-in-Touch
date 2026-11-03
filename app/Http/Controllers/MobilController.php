<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\MilikTenant;
use App\Models\KategoriMobil;
use App\Models\Mobil;
use App\Support\Validasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

/** CRUD unit mobil milik mitra (tabel tb_mobil). */
class MobilController extends Controller
{
    use MilikTenant;

    private const STATUS = ['tersedia', 'disewa', 'perawatan', 'nonaktif'];

    private const ATRIBUT = [
        'id_kategori' => 'kategori',
        'plat_nomor' => 'plat nomor',
        'kapasitas_penumpang' => 'kapasitas',
        'harga_per_hari' => 'harga per hari',
        'bahan_bakar' => 'bahan bakar',
    ];

    public function index(Request $request)
    {
        $status = $request->query('status');
        $cari = trim((string) $request->query('q', ''));

        $mobil = Mobil::with('kategori')
            ->where('id_tenant', $this->idTenant())
            ->when(in_array($status, self::STATUS, true), fn ($q) => $q->where('status', $status))
            ->when($cari !== '', function ($q) use ($cari) {
                $like = '%' . addcslashes($cari, '%_\\') . '%';
                $q->where(fn ($w) => $w->where('plat_nomor', 'like', $like)
                    ->orWhere('merek', 'like', $like)
                    ->orWhere('model', 'like', $like));
            })
            ->orderBy('merek')->orderBy('model')
            ->paginate(10)
            ->withQueryString();

        return view('mitra.mobil.index', compact('mobil', 'status', 'cari'));
    }

    public function create()
    {
        $kategori = $this->daftarKategori();

        if ($kategori->isEmpty()) {
            return redirect()->route('mitra.kategori.create')
                ->with('error', 'Buat kategori mobil dulu sebelum menambah unit.');
        }

        return view('mitra.mobil.form', ['mobil' => null, 'kategori' => $kategori]);
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->aturan(null), Validasi::pesan(), self::ATRIBUT);

        unset($data['foto']);
        $data['id_tenant'] = $this->idTenant();
        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('mobil', 'public');
        }

        Mobil::create($data);

        return redirect()->route('mitra.mobil.index')->with('success', 'Mobil berhasil ditambahkan.');
    }

    public function edit($id)
    {
        return view('mitra.mobil.form', [
            'mobil' => $this->cari($id),
            'kategori' => $this->daftarKategori(),
        ]);
    }

    public function update(Request $request, $id)
    {
        $mobil = $this->cari($id);
        $data = $request->validate($this->aturan($mobil), Validasi::pesan(), self::ATRIBUT);

        unset($data['foto']);
        // Status "disewa" diatur otomatis oleh alur pemesanan, bukan lewat form.
        if ($mobil->status === 'disewa') {
            unset($data['status']);
        }
        if ($request->hasFile('foto')) {
            if ($mobil->foto) {
                Storage::disk('public')->delete($mobil->foto);
            }
            $data['foto'] = $request->file('foto')->store('mobil', 'public');
        }

        $mobil->update($data);

        return redirect()->route('mitra.mobil.index')->with('success', 'Data mobil berhasil diperbarui.');
    }

    /** "Hapus" = nonaktifkan. Data dan riwayat pemesanan tetap tersimpan. */
    public function destroy($id)
    {
        $mobil = $this->cari($id);

        if ($mobil->status === 'disewa') {
            return back()->with('error', 'Mobil sedang disewa, tidak bisa dinonaktifkan.');
        }

        $mobil->update(['status' => 'nonaktif']);

        return redirect()->route('mitra.mobil.index')
            ->with('success', 'Mobil dinonaktifkan. Untuk menampilkannya lagi, ubah statusnya menjadi Tersedia.');
    }

    private function cari($id): Mobil
    {
        return Mobil::where('id_tenant', $this->idTenant())->findOrFail($id);
    }

    private function daftarKategori()
    {
        return KategoriMobil::where('id_tenant', $this->idTenant())->orderBy('nama_kategori')->get();
    }

    private function aturan(?Mobil $mobil): array
    {
        return [
            'id_kategori' => ['required', Rule::exists('tb_kategori_mobil', 'id_kategori')->where('id_tenant', $this->idTenant())],
            'plat_nomor' => ['required', 'string', 'max:15', Rule::unique('tb_mobil', 'plat_nomor')->ignore($mobil?->id_mobil, 'id_mobil')],
            'merek' => ['required', 'string', 'max:50'],
            'model' => ['required', 'string', 'max:50'],
            'tahun' => ['required', 'integer', 'between:1990,' . (now()->year + 1)],
            'warna' => ['nullable', 'string', 'max:30'],
            'transmisi' => ['required', 'in:manual,matic'],
            'bahan_bakar' => ['required', 'in:bensin,diesel,listrik,hybrid'],
            'kapasitas_penumpang' => ['required', 'integer', 'between:1,20'],
            'harga_per_hari' => ['required', 'numeric', 'min:0', 'max:99999999'],
            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'status' => $mobil?->status === 'disewa'
                ? ['nullable']
                : ['required', 'in:tersedia,perawatan,nonaktif'],
        ];
    }
}
