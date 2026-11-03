@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <h1>Halo, {{ auth()->user()->nama }}</h1>
    <p class="sub">
        Kamu masuk sebagai {{ auth()->user()->roleLabel() }}@if (auth()->user()->tenant) di {{ auth()->user()->tenant->nama_rental }}@endif.
    </p>

    <div class="stats">
        @foreach ($stat as [$label, $nilai])
            <div class="stat"><div class="n">{{ $nilai }}</div><div class="l">{{ $label }}</div></div>
        @endforeach
    </div>

    <h2 class="section-title">Pemesanan terbaru</h2>
    <div class="table-wrap">
        <table class="tbl">
            <thead>
                <tr>
                    <th>Kode</th>
                    @if ($grup === 'admin') <th>Mitra</th> @endif
                    <th>Pelanggan</th><th>Unit</th><th class="num">Total</th><th>Status</th>
                    @if ($grup === 'mitra') <th></th> @endif
                </tr>
            </thead>
            <tbody>
                @forelse ($terbaru as $p)
                    <tr>
                        <td><strong>{{ $p->kode_pemesanan }}</strong><span class="sub">{{ \App\Support\Format::tanggal($p->tanggal_pesan) }}</span></td>
                        @if ($grup === 'admin') <td>{{ $p->tenant?->nama_rental }}</td> @endif
                        <td>{{ $p->pelanggan?->nama_lengkap }}</td>
                        <td>{{ $p->mobil?->merek }} {{ $p->mobil?->model }}</td>
                        <td class="num">{{ \App\Support\Format::rupiah($p->total_biaya) }}</td>
                        <td><x-status :s="$p->status" /></td>
                        @if ($grup === 'mitra')
                            <td><a class="btn btn-line btn-sm" href="{{ route('mitra.pemesanan.show', $p->id_pemesanan) }}">Detail</a></td>
                        @endif
                    </tr>
                @empty
                    <tr><td colspan="7" class="empty-row">Belum ada pemesanan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
