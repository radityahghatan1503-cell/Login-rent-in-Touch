@php
    $grup = auth()->user()->grup();

    if ($grup === 'pelanggan') {
        $judul = 'Pelanggan';
        $terakhir = session('mobil_terakhir_url');
        $menu = [
            ['🔍', 'Beranda & Pencarian', route('beranda'), request()->routeIs('beranda')],
            ['🚘', 'Detail & Pemesanan', $terakhir, request()->routeIs('mobil.detail')],
            ['📋', 'Status Pesanan', route('pesanan.index'), request()->routeIs('pesanan.*')],
        ];
    } elseif ($grup === 'mitra') {
        $judul = 'Mitra';
        $menu = [
            ['🏠', 'Dashboard', route('dashboard'), request()->routeIs('dashboard')],
            ['🚗', 'Mobil', route('mitra.mobil.index'), request()->routeIs('mitra.mobil.*')],
            ['🏷️', 'Kategori', route('mitra.kategori.index'), request()->routeIs('mitra.kategori.*')],
            ['🧑‍✈️', 'Supir', route('mitra.supir.index'), request()->routeIs('mitra.supir.*')],
            ['📥', 'Pemesanan', route('mitra.pemesanan.index'), request()->routeIs('mitra.pemesanan.*')],
        ];
    } else {
        $judul = 'Admin';
        $menu = [
            ['🏠', 'Dashboard', route('dashboard'), request()->routeIs('dashboard')],
            ['🏢', 'Mitra rental', route('tenant.index'), request()->routeIs('tenant.*')],
        ];
    }
@endphp

<p class="side-title">{{ $judul }}</p>
@foreach ($menu as [$ikon, $label, $url, $aktif])
    @if ($url)
        <a class="side-link {{ $aktif ? 'is-active' : '' }}" href="{{ $url }}" @if ($aktif) aria-current="page" @endif>
            <span aria-hidden="true">{{ $ikon }}</span> {{ $label }}
        </a>
    @else
        <span class="side-link is-disabled" title="Pilih mobil dari Beranda dulu">
            <span aria-hidden="true">{{ $ikon }}</span> {{ $label }}
        </span>
    @endif
@endforeach
