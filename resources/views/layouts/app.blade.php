<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard') · Rent In-Touch</title>
    <link rel="stylesheet" href="{{ asset('css/rent.css') }}">
</head>
<body>
    @php
        $user = auth()->user();
        $labelMenu = match ($user->grup()) {
            'admin' => 'Dashboard Admin',
            'mitra' => 'Dashboard Mitra',
            default => 'Pelanggan',
        };
    @endphp

    <header class="topbar">
        <a class="brand" href="{{ route('dashboard') }}">
            <span class="brand-mark" aria-hidden="true">
                <svg width="28" height="28" viewBox="0 0 28 28"><path d="M3 17c0-2 1-3 3-3l2-4c.4-.7 1-1 1.8-1h8.4c.8 0 1.4.3 1.8 1l2 4c2 0 3 1 3 3v3H3z" fill="#164a3d"/><circle cx="9" cy="21" r="3" fill="#164a3d" stroke="#e8a23b" stroke-width="1.5"/><circle cx="20" cy="21" r="3" fill="#164a3d" stroke="#e8a23b" stroke-width="1.5"/></svg>
            </span>
            <span>
                <span class="brand-name">Rent In-Touch</span><br>
                <span class="brand-sub">Platform rental mobil multi-mitra</span>
            </span>
        </a>

        <nav class="topnav" aria-label="Menu utama">
            <a class="navpill" href="{{ route('dashboard') }}" aria-current="page">{{ $labelMenu }}</a>
            <span class="who">{{ $user->nama }}</span>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-ghost">Keluar</button>
            </form>
        </nav>
    </header>

    <div class="shell">
        <aside class="side" aria-label="Menu {{ $user->grupLabel() }}">
            @include('partials.sidebar')
        </aside>
        <main class="content">
            @if (session('success'))
                <div class="alert alert-ok" role="status">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-error" role="alert">{{ session('error') }}</div>
            @endif
            @yield('content')
        </main>
    </div>

    @stack('scripts')
</body>
</html>
