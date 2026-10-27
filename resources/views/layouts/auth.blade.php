<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') · Rent In-Touch</title>
    <link rel="stylesheet" href="{{ asset('css/rent.css') }}">
</head>
<body>
<div class="auth">
    <aside class="auth-side">
        <a class="brand" href="{{ url('/') }}">
            <span class="brand-mark">
                <svg width="28" height="28" viewBox="0 0 28 28" aria-hidden="true">
                    <path d="M3 17c0-2 1-3 3-3l2-4c.4-.7 1-1 1.8-1h8.4c.8 0 1.4.3 1.8 1l2 4c2 0 3 1 3 3v3H3z" fill="#164a3d"/>
                    <circle cx="9" cy="21" r="3" fill="#164a3d" stroke="#e8a23b" stroke-width="1.5"/>
                    <circle cx="20" cy="21" r="3" fill="#164a3d" stroke="#e8a23b" stroke-width="1.5"/>
                </svg>
            </span>
            <span>
                <span class="brand-name">Rent In-Touch</span><br>
                <span class="brand-sub">Platform rental mobil multi-mitra</span>
            </span>
        </a>

        <div>
            <h1>@yield('side_title')</h1>
            <p>@yield('side_text')</p>
        </div>

        <div class="car-tile" aria-hidden="true">
            <span class="chip">Jaya Rent</span>
            <svg viewBox="0 0 300 110">
                <path d="M12 74c0-10 6-16 16-17l26-4 22-24c3-3 7-5 12-5h62c6 0 11 2 15 6l26 22 38 6c10 2 17 8 17 16v14c0 3-2 5-5 5H17c-3 0-5-2-5-5z" fill="#164a3d"/>
                <path d="M96 31h62c3 0 5 1 7 3l18 17H80z" fill="#6f9487"/>
                <circle cx="82" cy="88" r="20" fill="#164a3d"/><circle cx="82" cy="88" r="9" fill="#e8a23b"/>
                <circle cx="222" cy="88" r="20" fill="#164a3d"/><circle cx="222" cy="88" r="9" fill="#e8a23b"/>
            </svg>
        </div>

        <ul class="perks">
            <li>Satu akun untuk banyak mitra rental</li>
            <li>Bandingkan unit berdasarkan lokasi, tanggal, dan jenis</li>
            <li>Pantau status pesanan dari satu tempat</li>
        </ul>
    </aside>

    <main class="auth-main">
        <div class="auth-card">
            @if (session('success'))
                <div class="alert alert-ok" role="status">{{ session('success') }}</div>
            @endif
            @yield('content')
        </div>
    </main>
</div>

<script>
    // Tampilkan / sembunyikan kata sandi
    document.querySelectorAll('[data-toggle-password]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var input = document.getElementById(btn.dataset.togglePassword);
            var show = input.type === 'password';
            input.type = show ? 'text' : 'password';
            btn.textContent = show ? 'Sembunyi' : 'Lihat';
            btn.setAttribute('aria-pressed', show);
        });
    });
</script>
</body>
</html>
