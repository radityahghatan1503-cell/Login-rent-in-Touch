@extends('layouts.app')

@php $edit = (bool) $tenant; @endphp

@section('title', $edit ? 'Ubah Mitra' : 'Tambah Mitra')

@section('content')
    <a class="back-link" href="{{ route('tenant.index') }}">← Kembali ke daftar mitra</a>
    <h1>{{ $edit ? 'Ubah mitra' : 'Tambah mitra' }}</h1>

    <form class="form-card" method="POST" novalidate
          action="{{ $edit ? route('tenant.update', $tenant->id_tenant) : route('tenant.store') }}">
        @csrf
        @if ($edit) @method('PUT') @endif

        <div class="form-grid">
            <x-input name="nama_rental" label="Nama rental" :value="$tenant?->nama_rental" maxlength="100" required />
            <x-input name="subdomain" label="Subdomain" :value="$tenant?->subdomain" placeholder="maju-jaya" maxlength="50" required
                     hint="Huruf kecil, angka, dan tanda strip." />
            <div class="full"><x-textarea name="alamat" label="Alamat (kota ikut dipakai pencarian lokasi)" :value="$tenant?->alamat" /></div>
            <x-input name="telepon" label="Telepon" type="tel" :value="$tenant?->telepon" placeholder="08xxxxxxxxxx" />
            <x-input name="email" label="Email" type="email" :value="$tenant?->email" maxlength="100" />
            <x-select name="status" label="Status" :options="['aktif' => 'Aktif', 'nonaktif' => 'Nonaktif', 'suspend' => 'Suspend']" :value="$tenant?->status ?? 'aktif'" />
        </div>

        <div class="form-actions">
            <button type="submit" class="btn">{{ $edit ? 'Simpan perubahan' : 'Simpan mitra' }}</button>
            <a class="btn btn-line" href="{{ route('tenant.index') }}">Batal</a>
        </div>
    </form>
@endsection
