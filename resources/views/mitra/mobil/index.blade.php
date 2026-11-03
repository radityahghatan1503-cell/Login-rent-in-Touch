@extends('layouts.app')

@section('title', 'Kelola Mobil')

@section('content')
    <div class="page-head">
        <div>
            <h1>Mobil</h1>
            <p>Kelola unit rental milik {{ auth()->user()->tenant?->nama_rental }}.</p>
        </div>
        <a class="btn btn-inline" href="{{ route('mitra.mobil.create') }}">+ Tambah mobil</a>
    </div>

    <form method="GET" action="{{ route('mitra.mobil.index') }}" class="toolbar">
        <input type="search" class="grow" name="q" value="{{ $cari }}" placeholder="Cari plat, merek, atau model" aria-label="Cari mobil">
        <select name="status" aria-label="Filter status">
            <option value="">Semua status</option>
            @foreach (['tersedia', 'disewa', 'perawatan', 'nonaktif'] as $s)
                <option value="{{ $s }}" @selected($status === $s)>{{ ucfirst($s) }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn btn-green btn-sm">Cari</button>
    </form>

    <div class="table-wrap">
        <table class="tbl">
            <thead>
                <tr><th>Unit</th><th>Plat</th><th>Kategori</th><th>Spesifikasi</th><th class="num">Harga / hari</th><th>Status</th><th>Aksi</th></tr>
            </thead>
            <tbody>
                @forelse ($mobil as $m)
                    <tr class="{{ $m->status === 'nonaktif' ? 'is-muted' : '' }}">
                        <td>
                            <div class="unit-cell">
                                @if ($m->foto)
                                    <img class="thumb" src="{{ asset('storage/' . $m->foto) }}" alt="">
                                @else
                                    <span class="thumb-svg">@include('partials.car', ['m' => []])</span>
                                @endif
                                <div>
                                    <strong>{{ $m->merek }} {{ $m->model }}</strong>
                                    <span class="sub">{{ $m->tahun }} · {{ $m->warna ?? '-' }}</span>
                                </div>
                            </div>
                        </td>
                        <td>{{ $m->plat_nomor }}</td>
                        <td>{{ $m->kategori?->nama_kategori }}</td>
                        <td>{{ ucfirst($m->transmisi) }} · {{ ucfirst($m->bahan_bakar) }} · {{ $m->kapasitas_penumpang }} kursi</td>
                        <td class="num">{{ \App\Support\Format::rupiah($m->harga_per_hari) }}</td>
                        <td><x-status :s="$m->status" /></td>
                        <td>
                            <div class="actions">
                                <a class="btn btn-line btn-sm" href="{{ route('mitra.mobil.edit', $m->id_mobil) }}">Ubah</a>
                                @if ($m->status !== 'nonaktif')
                                    <form method="POST" action="{{ route('mitra.mobil.destroy', $m->id_mobil) }}"
                                          onsubmit="return confirm('Nonaktifkan {{ $m->merek }} {{ $m->model }}? Mobil tidak akan tampil di beranda pelanggan.')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">Nonaktifkan</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="empty-row">Belum ada mobil. Klik "Tambah mobil" untuk mulai.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $mobil->links('partials.pagination') }}
@endsection
