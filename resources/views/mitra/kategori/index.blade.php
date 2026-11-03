@extends('layouts.app')

@section('title', 'Kategori Mobil')

@section('content')
    <div class="page-head">
        <div>
            <h1>Kategori mobil</h1>
            <p>Kelompokkan unit, misalnya MPV, SUV, atau City Car.</p>
        </div>
        <a class="btn btn-inline" href="{{ route('mitra.kategori.create') }}">+ Tambah kategori</a>
    </div>

    <div class="table-wrap">
        <table class="tbl">
            <thead><tr><th>Nama</th><th>Deskripsi</th><th class="num">Jumlah mobil</th><th>Aksi</th></tr></thead>
            <tbody>
                @forelse ($kategori as $k)
                    <tr>
                        <td><strong>{{ $k->nama_kategori }}</strong></td>
                        <td>{{ $k->deskripsi ?: '-' }}</td>
                        <td class="num">{{ $k->mobil_count }}</td>
                        <td>
                            <div class="actions">
                                <a class="btn btn-line btn-sm" href="{{ route('mitra.kategori.edit', $k->id_kategori) }}">Ubah</a>
                                <form method="POST" action="{{ route('mitra.kategori.destroy', $k->id_kategori) }}"
                                      onsubmit="return confirm('Hapus kategori {{ $k->nama_kategori }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" @disabled($k->mobil_count > 0)
                                            title="{{ $k->mobil_count > 0 ? 'Masih dipakai oleh mobil' : '' }}">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="empty-row">Belum ada kategori.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
