@extends('layouts.app')

@section('title', 'Kelola Supir')

@section('content')
    <div class="page-head">
        <div>
            <h1>Supir</h1>
            <p>Supir yang bisa dipilih pelanggan saat memesan dengan sopir.</p>
        </div>
        <a class="btn btn-inline" href="{{ route('mitra.supir.create') }}">+ Tambah supir</a>
    </div>

    <div class="table-wrap">
        <table class="tbl">
            <thead><tr><th>Nama</th><th>Telepon</th><th>No. SIM</th><th class="num">Tarif / hari</th><th>Status</th><th>Aksi</th></tr></thead>
            <tbody>
                @forelse ($supir as $s)
                    <tr class="{{ $s->status === 'nonaktif' ? 'is-muted' : '' }}">
                        <td><strong>{{ $s->nama_supir }}</strong></td>
                        <td>{{ $s->telepon }}</td>
                        <td>{{ $s->no_sim }}</td>
                        <td class="num">{{ \App\Support\Format::rupiah($s->tarif_per_hari) }}</td>
                        <td><x-status :s="$s->status" /></td>
                        <td>
                            <div class="actions">
                                <a class="btn btn-line btn-sm" href="{{ route('mitra.supir.edit', $s->id_supir) }}">Ubah</a>
                                @if ($s->status !== 'nonaktif')
                                    <form method="POST" action="{{ route('mitra.supir.destroy', $s->id_supir) }}"
                                          onsubmit="return confirm('Nonaktifkan supir {{ $s->nama_supir }}?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">Nonaktifkan</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="empty-row">Belum ada supir.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
