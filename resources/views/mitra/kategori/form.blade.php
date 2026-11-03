@extends('layouts.app')

@php $edit = (bool) $kategori; @endphp

@section('title', $edit ? 'Ubah Kategori' : 'Tambah Kategori')

@section('content')
    <a class="back-link" href="{{ route('mitra.kategori.index') }}">← Kembali ke daftar kategori</a>
    <h1>{{ $edit ? 'Ubah kategori' : 'Tambah kategori' }}</h1>

    <form class="form-card" method="POST" novalidate
          action="{{ $edit ? route('mitra.kategori.update', $kategori->id_kategori) : route('mitra.kategori.store') }}">
        @csrf
        @if ($edit) @method('PUT') @endif

        <x-input name="nama_kategori" label="Nama kategori" :value="$kategori?->nama_kategori" placeholder="MPV" maxlength="50" required />
        <x-textarea name="deskripsi" label="Deskripsi (opsional)" :value="$kategori?->deskripsi" />

        <div class="form-actions">
            <button type="submit" class="btn">{{ $edit ? 'Simpan perubahan' : 'Simpan kategori' }}</button>
            <a class="btn btn-line" href="{{ route('mitra.kategori.index') }}">Batal</a>
        </div>
    </form>
@endsection
