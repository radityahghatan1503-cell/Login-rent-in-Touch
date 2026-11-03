@extends('layouts.app')

@section('title', 'Status Pesanan')

@section('content')
    <p class="kicker">Modul pelanggan</p>
    <h1>Status pesanan</h1>
    <p class="sub">Pantau semua pesananmu dari berbagai mitra di satu tempat.</p>

    <div class="chips" role="group" aria-label="Filter status" style="margin-top:0">
        <a class="chip-btn {{ ! $status ? 'is-on' : '' }}" href="{{ route('pesanan.index') }}">Semua ({{ $hitung->sum() }})</a>
        @foreach (['menunggu', 'dikonfirmasi', 'berjalan', 'selesai', 'dibatalkan'] as $s)
            <a class="chip-btn {{ $status === $s ? 'is-on' : '' }}" href="{{ route('pesanan.index', ['status' => $s]) }}">{{ ucfirst($s) }} ({{ $hitung[$s] ?? 0 }})</a>
        @endforeach
    </div>

    <div style="margin-top:18px">
        @forelse ($pesanan as $p)
            <a class="pesanan-card" href="{{ route('pesanan.show', $p->kode_pemesanan) }}">
                <div>
                    <h3>{{ $p->mobil?->merek }} {{ $p->mobil?->model }}</h3>
                    <p>{{ $p->tenant?->nama_rental }} · {{ $p->kode_pemesanan }}</p>
                    <p>{{ \App\Support\Format::tanggal($p->tanggal_mulai) }} – {{ \App\Support\Format::tanggal($p->tanggal_selesai) }} ({{ $p->lama_sewa_hari }} hari)</p>
                </div>
                <div class="right">
                    <x-status :s="$p->status" />
                    <p><strong>{{ \App\Support\Format::rupiah($p->total_biaya) }}</strong></p>
                </div>
            </a>
        @empty
            <div class="empty">
                <p><strong>Belum ada pesanan{{ $status ? ' dengan status ini' : '' }}.</strong></p>
                <p>Cari mobil di beranda untuk memulai.</p>
                <a class="btn btn-inline" href="{{ route('beranda') }}">Ke beranda</a>
            </div>
        @endforelse
    </div>

    {{ $pesanan->links('partials.pagination') }}
@endsection
