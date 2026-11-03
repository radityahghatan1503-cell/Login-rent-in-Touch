@extends('layouts.app')

@section('title', $mobil->merek . ' ' . $mobil->model)

@section('content')
    @php
        $F = \App\Support\Format::class;
        $nama = $mobil->merek . ' ' . $mobil->model;
        $adaSopir = $supir->isNotEmpty();
        $total = $mobil->harga_per_hari * $lamaHari;
    @endphp

    <a class="back-link" href="{{ route('beranda', ['mulai' => $mulai, 'selesai' => $selesai]) }}">← Kembali ke pencarian</a>

    <div class="detail-grid">
        <div>
            <div class="hero-img">
                <span class="chip">{{ $mobil->tenant->nama_rental }}</span>
                @include('partials.car', ['m' => ['foto' => $mobil->foto, 'nama' => $nama]])
            </div>

            <h1>{{ $nama }}</h1>
            <p class="sub">{{ $mobil->kategori?->nama_kategori }} · {{ $mobil->tahun }}</p>

            <div class="card">
                <h2>Spesifikasi</h2>
                <dl class="dl">
                    <dt>Kategori</dt><dd>{{ $mobil->kategori?->nama_kategori ?? '-' }}</dd>
                    <dt>Tahun</dt><dd>{{ $mobil->tahun }}</dd>
                    <dt>Warna</dt><dd>{{ $mobil->warna ?: '-' }}</dd>
                    <dt>Transmisi</dt><dd>{{ ucfirst($mobil->transmisi) }}</dd>
                    <dt>Bahan bakar</dt><dd>{{ ucfirst($mobil->bahan_bakar) }}</dd>
                    <dt>Kapasitas</dt><dd>{{ $mobil->kapasitas_penumpang }} kursi</dd>
                </dl>
            </div>

            <div class="card">
                <h2>Layanan</h2>
                <p class="car-tags" style="min-height:0">
                    <span class="tag">Lepas kunci</span>
                    @if ($adaSopir) <span class="tag">Dengan sopir</span> @endif
                </p>
                @if ($adaSopir)
                    <div class="table-wrap" style="border:0">
                        <table class="tbl">
                            <thead><tr><th>Supir tersedia</th><th class="num">Tarif / hari</th></tr></thead>
                            <tbody>
                                @foreach ($supir as $s)
                                    <tr><td>{{ $s->nama_supir }}</td><td class="num">{{ $F::rupiah($s->tarif_per_hari) }}</td></tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p style="margin:0;color:var(--muted)">Mitra ini belum memiliki supir yang tersedia. Unit disewa lepas kunci.</p>
                @endif
            </div>

            <div class="card">
                <h2>Mitra rental</h2>
                <dl class="dl">
                    <dt>Nama</dt><dd>{{ $mobil->tenant->nama_rental }}</dd>
                    <dt>Alamat</dt><dd>{{ $mobil->tenant->alamat ?: '-' }}</dd>
                    <dt>Telepon</dt><dd>{{ $mobil->tenant->telepon ?: '-' }}</dd>
                </dl>
            </div>
        </div>

        <aside class="sticky">
            <div class="card">
                <p class="price-big">{{ $F::rupiah($mobil->harga_per_hari) }} <small>/ hari</small></p>

                <form method="GET" action="{{ route('mobil.detail', $mobil->id_mobil) }}" class="mini-form">
                    <div><label for="mulai">Mulai</label><input id="mulai" type="date" name="mulai" value="{{ $mulai }}" min="{{ now()->toDateString() }}"></div>
                    <div><label for="selesai">Selesai</label><input id="selesai" type="date" name="selesai" value="{{ $selesai }}" min="{{ $mulai }}"></div>
                    <button type="submit" class="btn btn-line btn-sm">Cek</button>
                </form>

                @if ($bentrok)
                    <div class="avail no" role="status">Sudah dipesan pada tanggal ini. Coba tanggal lain.</div>
                @else
                    <div class="avail ok" role="status">Tersedia pada tanggal ini</div>
                @endif

                <div class="sum-row"><span>{{ $F::rupiah($mobil->harga_per_hari) }} × {{ $lamaHari }} hari</span><span>{{ $F::rupiah($total) }}</span></div>
                @if ($adaSopir)
                    <div class="sum-row"><span>Sopir (opsional)</span><span>mulai {{ $F::rupiah($supir->first()->tarif_per_hari) }} / hari</span></div>
                @endif
                <div class="sum-row total"><span>Total sewa mobil</span><span>{{ $F::rupiah($total) }}</span></div>

                <button type="button" class="btn" style="margin-top:14px" disabled>Pesan mobil (segera hadir)</button>
                <p class="hint">Pemesanan lengkap dengan verifikasi data diri dibuka di tahap berikutnya.</p>
            </div>
        </aside>
    </div>
@endsection
