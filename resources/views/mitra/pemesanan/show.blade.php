@extends('layouts.app')

@section('title', 'Pesanan ' . $p->kode_pemesanan)

@php
    $label = ['dikonfirmasi' => 'Konfirmasi pesanan', 'berjalan' => 'Mulai sewa (serah terima unit)', 'selesai' => 'Selesaikan sewa', 'dibatalkan' => 'Batalkan pesanan'];
@endphp

@section('content')
    <a class="back-link" href="{{ route('mitra.pemesanan.index') }}">← Kembali ke daftar pemesanan</a>

    <div class="page-head">
        <div>
            <h1>{{ $p->kode_pemesanan }}</h1>
            <p>Dipesan {{ \App\Support\Format::tanggal($p->tanggal_pesan, true) }}</p>
        </div>
        <x-status :s="$p->status" />
    </div>

    @if ($alur)
        <div class="card">
            <h2>Ubah status</h2>
            <div class="actions">
                @foreach ($alur as $next)
                    <form method="POST" action="{{ route('mitra.pemesanan.status', $p->id_pemesanan) }}"
                          @if ($next === 'dibatalkan') onsubmit="return confirm('Batalkan pesanan ini?')" @endif>
                        @csrf @method('PATCH')
                        <input type="hidden" name="status" value="{{ $next }}">
                        <button type="submit" class="btn btn-sm {{ $next === 'dibatalkan' ? 'btn-danger' : 'btn-green' }}">{{ $label[$next] }}</button>
                    </form>
                @endforeach
            </div>
        </div>
    @endif

    @include('pesanan._detail', ['p' => $p, 'mitra' => true])
@endsection
