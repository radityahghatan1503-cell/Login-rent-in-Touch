@extends('layouts.app')

@section('title', 'Pemesanan Masuk')

@section('content')
    <div class="page-head">
        <div>
            <h1>Pemesanan</h1>
            <p>Pesanan pelanggan untuk unit {{ auth()->user()->tenant?->nama_rental }}.</p>
        </div>
    </div>

    <div class="chips" role="group" aria-label="Filter status" style="margin-top:0">
        <a class="chip-btn {{ ! $status ? 'is-on' : '' }}" href="{{ route('mitra.pemesanan.index') }}">Semua ({{ $hitung->sum() }})</a>
        @foreach (['menunggu', 'dikonfirmasi', 'berjalan', 'selesai', 'dibatalkan'] as $s)
            <a class="chip-btn {{ $status === $s ? 'is-on' : '' }}" href="{{ route('mitra.pemesanan.index', ['status' => $s]) }}">{{ ucfirst($s) }} ({{ $hitung[$s] ?? 0 }})</a>
        @endforeach
    </div>

    <form method="GET" action="{{ route('mitra.pemesanan.index') }}" class="toolbar" style="margin-top:14px">
        @if ($status) <input type="hidden" name="status" value="{{ $status }}"> @endif
        <input type="search" class="grow" name="q" value="{{ $cari }}" placeholder="Cari kode pesanan atau nama pelanggan" aria-label="Cari pesanan">
        <button type="submit" class="btn btn-green btn-sm">Cari</button>
    </form>

    <div class="table-wrap">
        <table class="tbl">
            <thead><tr><th>Kode</th><th>Pelanggan</th><th>Unit</th><th>Jadwal</th><th class="num">Total</th><th>Status</th><th></th></tr></thead>
            <tbody>
                @forelse ($pesanan as $p)
                    <tr>
                        <td><strong>{{ $p->kode_pemesanan }}</strong></td>
                        <td>{{ $p->pelanggan?->nama_lengkap }}</td>
                        <td>{{ $p->mobil?->merek }} {{ $p->mobil?->model }}<span class="sub">{{ $p->mobil?->plat_nomor }}</span></td>
                        <td>{{ \App\Support\Format::tanggal($p->tanggal_mulai) }} – {{ \App\Support\Format::tanggal($p->tanggal_selesai) }}<span class="sub">{{ $p->lama_sewa_hari }} hari</span></td>
                        <td class="num">{{ \App\Support\Format::rupiah($p->total_biaya) }}</td>
                        <td><x-status :s="$p->status" /></td>
                        <td><a class="btn btn-line btn-sm" href="{{ route('mitra.pemesanan.show', $p->id_pemesanan) }}">Detail</a></td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="empty-row">Belum ada pemesanan{{ $status ? ' dengan status ini' : '' }}.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $pesanan->links('partials.pagination') }}
@endsection
