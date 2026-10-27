<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard · Rent In-Touch</title>
    <link rel="stylesheet" href="{{ asset('css/rent.css') }}">
</head>
<body>
    <header class="topbar">
        <a class="brand" href="{{ route('dashboard') }}">
            <span class="brand-mark" aria-hidden="true">
                <svg width="28" height="28" viewBox="0 0 28 28"><path d="M3 17c0-2 1-3 3-3l2-4c.4-.7 1-1 1.8-1h8.4c.8 0 1.4.3 1.8 1l2 4c2 0 3 1 3 3v3H3z" fill="#164a3d"/><circle cx="9" cy="21" r="3" fill="#164a3d" stroke="#e8a23b" stroke-width="1.5"/><circle cx="20" cy="21" r="3" fill="#164a3d" stroke="#e8a23b" stroke-width="1.5"/></svg>
            </span>
            <span class="brand-name">Rent In-Touch</span>
        </a>
        <div class="who">
            <span>{{ auth()->user()->name }}</span>
            <span class="badge">{{ auth()->user()->roleLabel() }}</span>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-ghost">Keluar</button>
            </form>
        </div>
    </header>

    <main class="page">
        @if (session('success'))
            <div class="alert alert-ok" role="status">{{ session('success') }}</div>
        @endif

        <h1>Halo, {{ auth()->user()->name }}</h1>
        <p>Kamu masuk sebagai {{ auth()->user()->roleLabel() }}.</p>

        <div class="panel">
            @if (auth()->user()->hasRole('admin'))
                <p>Kelola mitra rental yang terdaftar di platform.</p>
                <a class="link" href="{{ route('tenant.index') }}">Buka data tenant</a>
            @elseif (auth()->user()->hasRole('mitra'))
                <p>Dashboard mitra (unit, pemesanan masuk) akan dibuat di modul berikutnya.</p>
            @else
                <p>Beranda &amp; pencarian mobil akan dibuat di modul berikutnya.</p>
            @endif
        </div>
    </main>
</body>
</html>
