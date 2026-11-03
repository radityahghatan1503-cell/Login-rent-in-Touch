@extends('layouts.app')

@php $edit = (bool) $supir; @endphp

@section('title', $edit ? 'Ubah Supir' : 'Tambah Supir')

@section('content')
    <a class="back-link" href="{{ route('mitra.supir.index') }}">← Kembali ke daftar supir</a>
    <h1>{{ $edit ? 'Ubah supir' : 'Tambah supir' }}</h1>

    <form class="form-card" method="POST" novalidate
          action="{{ $edit ? route('mitra.supir.update', $supir->id_supir) : route('mitra.supir.store') }}">
        @csrf
        @if ($edit) @method('PUT') @endif

        <div class="form-grid">
            <div class="full"><x-input name="nama_supir" label="Nama supir" :value="$supir?->nama_supir" maxlength="100" required /></div>
            <x-input name="telepon" label="Telepon" type="tel" :value="$supir?->telepon" placeholder="08xxxxxxxxxx" required />
            <x-input name="no_sim" label="Nomor SIM" :value="$supir?->no_sim" maxlength="30" required />
            <x-input name="tarif_per_hari" label="Tarif per hari (Rp)" type="number" :value="$supir ? (int) $supir->tarif_per_hari : null" min="0" step="1000" required />

            @if ($edit && $supir->status === 'bertugas')
                <div class="field">
                    <label>Status</label>
                    <div class="note">Sedang bertugas. Status berubah otomatis saat sewa selesai.</div>
                </div>
            @else
                <x-select name="status" label="Status" :options="['tersedia' => 'Tersedia', 'nonaktif' => 'Nonaktif']" :value="$supir?->status ?? 'tersedia'" />
            @endif
        </div>

        <div class="form-actions">
            <button type="submit" class="btn">{{ $edit ? 'Simpan perubahan' : 'Simpan supir' }}</button>
            <a class="btn btn-line" href="{{ route('mitra.supir.index') }}">Batal</a>
        </div>
    </form>
@endsection
