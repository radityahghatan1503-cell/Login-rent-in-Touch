@extends('layouts.app')

@section('title', 'Pesanan ' . $p->kode_pemesanan)

@section('content')
    <a class="back-link" href="{{ route('pesanan.index') }}">← Kembali ke status pesanan</a>

    <div class="page-head">
        <div>
            <h1>{{ $p->kode_pemesanan }}</h1>
            <p>Dipesan {{ \App\Support\Format::tanggal($p->tanggal_pesan, true) }}</p>
        </div>
        <div class="actions">
            <x-status :s="$p->status" />
            @if ($p->status === 'menunggu')
                <form method="POST" action="{{ route('pesanan.batal', $p->kode_pemesanan) }}"
                      onsubmit="return confirm('Batalkan pesanan ini?')">
                    @csrf @method('PATCH')
                    <button type="submit" class="btn btn-danger btn-sm">Batalkan pesanan</button>
                </form>
            @endif
        </div>
    </div>

    @include('pesanan._detail', ['p' => $p, 'mitra' => false])
@endsection
