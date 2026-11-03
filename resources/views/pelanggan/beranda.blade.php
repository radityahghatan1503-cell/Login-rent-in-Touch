@extends('layouts.app')

@section('title', 'Beranda & Pencarian')

@section('content')
    <p class="kicker">Modul pelanggan · UC-03</p>
    <h1>Cari mobil dari berbagai mitra terpercaya</h1>
    <p class="sub">Bandingkan unit berdasarkan lokasi, tanggal, dan jenis kendaraan dari seluruh mitra terdaftar.</p>

    <section class="hero-card" aria-labelledby="judul-cari">
        <h2 id="judul-cari">Temukan mobil untuk perjalananmu</h2>
        <p>{{ $jumlahUnit }} unit dari {{ $jumlahMitra }} mitra rental aktif</p>

        @if ($errors->any())
            <div class="alert alert-error" role="alert">{{ $errors->first() }}</div>
        @endif

        <form method="GET" action="{{ route('beranda') }}" class="searchbar">
            <div class="sfield">
                <label for="lokasi">Lokasi penjemputan</label>
                <input id="lokasi" type="text" name="lokasi" value="{{ $lokasi }}" placeholder="Kota, mis. Jakarta">
            </div>
            <div class="sfield">
                <label for="mulai">Tanggal mulai</label>
                <input id="mulai" type="date" name="mulai" value="{{ $mulai }}" min="{{ now()->toDateString() }}">
            </div>
            <div class="sfield">
                <label for="selesai">Tanggal selesai</label>
                <input id="selesai" type="date" name="selesai" value="{{ $selesai }}" min="{{ $mulai }}">
            </div>
            <div class="sfield">
                <label for="jenis">Jenis kendaraan</label>
                <select id="jenis" name="jenis">
                    @foreach ($jenisList as $j)
                        <option value="{{ $j }}" @selected($jenis === $j)>{{ $j }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-cari">Cari mobil</button>
        </form>
    </section>

    <div class="chips" role="group" aria-label="Filter cepat">
        <button type="button" class="chip-btn is-on" data-filter="all" aria-pressed="true">Semua</button>
        <button type="button" class="chip-btn" data-filter="lepas_kunci" aria-pressed="false">Lepas Kunci</button>
        <button type="button" class="chip-btn" data-filter="sopir" aria-pressed="false">Dengan Sopir</button>
        <button type="button" class="chip-btn" data-filter="matic" aria-pressed="false">Matic</button>
        <button type="button" class="chip-btn" data-filter="manual" aria-pressed="false">Manual</button>
        <button type="button" class="chip-btn" data-filter="kursi7" aria-pressed="false">7 Kursi</button>
    </div>

    <p class="result-info" id="info-hasil" aria-live="polite">
        {{ $mobil->count() }} unit tersedia untuk {{ $lamaHari }} hari
    </p>

    @if ($mobil->isEmpty())
        <div class="empty">
            <p><strong>Belum ada unit yang cocok.</strong></p>
            <p>Coba ubah lokasi atau jenis kendaraan.</p>
            <a class="btn btn-inline" href="{{ route('beranda') }}">Reset pencarian</a>
        </div>
    @else
        <div class="cars" id="daftar-mobil">
            @foreach ($mobil as $m)
                <article class="car-card" data-tags="{{ $m['tags'] }}">
                    <div class="car-img">
                        <span class="chip">{{ $m['mitra'] }}</span>
                        @include('partials.car')
                    </div>
                    <div class="car-body">
                        <h3>{{ $m['nama'] }}</h3>
                        <p class="car-spek">{{ $m['spek'] }}</p>
                        <p class="car-tags">
                            @foreach ($m['layanan'] as $l)
                                <span class="tag">{{ $l }}</span>
                            @endforeach
                        </p>
                        <div class="car-foot">
                            <div>
                                <p class="price">{{ $m['harga_fmt'] }} <small>/ hari</small></p>
                                <p class="car-total">{{ $m['total_fmt'] }} untuk {{ $lamaHari }} hari</p>
                            </div>
                            <a class="btn btn-inline" href="{{ $m['url'] }}">Lihat detail</a>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
        <p class="empty" id="kosong-filter" hidden>Tidak ada unit dengan filter ini. Pilih filter lain atau tekan Semua.</p>
    @endif
@endsection

@push('scripts')
<script>
    // Filter cepat: tampilkan kartu yang punya semua filter aktif
    (function () {
        var chips = document.querySelectorAll('.chip-btn');
        var cards = document.querySelectorAll('.car-card');
        var info = document.getElementById('info-hasil');
        var kosong = document.getElementById('kosong-filter');
        var hari = {{ $lamaHari }};
        var aktif = [];

        function terapkan() {
            var tampil = 0;
            cards.forEach(function (c) {
                var tags = c.dataset.tags.split(' ');
                var cocok = aktif.every(function (f) { return tags.indexOf(f) !== -1; });
                c.hidden = !cocok;
                if (cocok) tampil++;
            });
            info.textContent = tampil + ' unit tersedia untuk ' + hari + ' hari';
            if (kosong) kosong.hidden = tampil !== 0;
        }

        chips.forEach(function (chip) {
            chip.addEventListener('click', function () {
                var f = chip.dataset.filter;
                if (f === 'all') {
                    aktif = [];
                } else if (aktif.indexOf(f) === -1) {
                    aktif.push(f);
                } else {
                    aktif.splice(aktif.indexOf(f), 1);
                }
                chips.forEach(function (c) {
                    var on = c.dataset.filter === 'all' ? aktif.length === 0 : aktif.indexOf(c.dataset.filter) !== -1;
                    c.classList.toggle('is-on', on);
                    c.setAttribute('aria-pressed', on);
                });
                terapkan();
            });
        });
    })();
</script>
@endpush
