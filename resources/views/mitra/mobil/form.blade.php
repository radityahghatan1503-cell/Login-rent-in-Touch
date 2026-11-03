@extends('layouts.app')

@php $edit = (bool) $mobil; @endphp

@section('title', $edit ? 'Ubah Mobil' : 'Tambah Mobil')

@section('content')
    <a class="back-link" href="{{ route('mitra.mobil.index') }}">← Kembali ke daftar mobil</a>
    <h1>{{ $edit ? 'Ubah mobil' : 'Tambah mobil' }}</h1>

    @if ($errors->any())
        <div class="alert alert-error" role="alert">Ada isian yang perlu diperbaiki. Periksa kolom bertanda merah.</div>
    @endif

    <form class="form-card" method="POST" enctype="multipart/form-data" novalidate
          action="{{ $edit ? route('mitra.mobil.update', $mobil->id_mobil) : route('mitra.mobil.store') }}">
        @csrf
        @if ($edit) @method('PUT') @endif

        <div class="form-grid">
            <div class="full">
                <x-select name="id_kategori" label="Kategori" placeholder="Pilih kategori"
                          :options="$kategori->pluck('nama_kategori', 'id_kategori')" :value="$mobil?->id_kategori" required />
            </div>
            <x-input name="merek" label="Merek" :value="$mobil?->merek" placeholder="Toyota" maxlength="50" required />
            <x-input name="model" label="Model" :value="$mobil?->model" placeholder="Avanza" maxlength="50" required />
            <x-input name="plat_nomor" label="Plat nomor" :value="$mobil?->plat_nomor" placeholder="B 1234 ABC" maxlength="15" required />
            <x-input name="tahun" label="Tahun" type="number" :value="$mobil?->tahun" min="1990" max="{{ now()->year + 1 }}" required />
            <x-input name="warna" label="Warna" :value="$mobil?->warna" maxlength="30" />
            <x-input name="kapasitas_penumpang" label="Kapasitas (kursi)" type="number" :value="$mobil?->kapasitas_penumpang" min="1" max="20" required />
            <x-select name="transmisi" label="Transmisi" :options="['matic' => 'Matic', 'manual' => 'Manual']" :value="$mobil?->transmisi ?? 'matic'" />
            <x-select name="bahan_bakar" label="Bahan bakar"
                      :options="['bensin' => 'Bensin', 'diesel' => 'Diesel', 'listrik' => 'Listrik', 'hybrid' => 'Hybrid']" :value="$mobil?->bahan_bakar ?? 'bensin'" />
            <x-input name="harga_per_hari" label="Harga sewa per hari (Rp)" type="number" :value="$mobil ? (int) $mobil->harga_per_hari : null" min="0" step="1000" required />

            @if ($edit && $mobil->status === 'disewa')
                <div class="field">
                    <label>Status</label>
                    <div class="note">Sedang disewa. Status berubah otomatis saat sewa selesai.</div>
                </div>
            @else
                <x-select name="status" label="Status"
                          :options="['tersedia' => 'Tersedia', 'perawatan' => 'Perawatan', 'nonaktif' => 'Nonaktif']" :value="$mobil?->status ?? 'tersedia'" />
            @endif

            <div class="full">
                @if ($edit && $mobil->foto)
                    <img class="preview-foto" src="{{ asset('storage/' . $mobil->foto) }}" alt="Foto {{ $mobil->merek }} {{ $mobil->model }}">
                @endif
                <x-input name="foto" label="{{ $edit && $mobil->foto ? 'Ganti foto' : 'Foto mobil' }}" type="file" accept="image/png,image/jpeg,image/webp"
                         hint="JPG, PNG, atau WEBP, maksimal 2 MB. Kosongkan jika tidak ingin mengubah." />
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn">{{ $edit ? 'Simpan perubahan' : 'Simpan mobil' }}</button>
            <a class="btn btn-line" href="{{ route('mitra.mobil.index') }}">Batal</a>
        </div>
    </form>
@endsection
