@extends('layouts.auth')

@section('title', 'Masuk')
@section('side_title', 'Masuk dan lanjutkan perjalananmu')
@section('side_text', 'Cari mobil dari berbagai mitra terpercaya, lalu pesan dalam beberapa langkah.')

@section('content')
    <h2>Masuk ke akunmu</h2>
    <p class="lead">Pilih peran, lalu masukkan email dan kata sandi.</p>

    <form method="POST" action="{{ route('login.attempt') }}" novalidate>
        @csrf

        @php $peran = old('role', 'pelanggan'); @endphp
        <fieldset class="pills">
            <legend>Masuk sebagai</legend>
            @foreach (['pelanggan' => 'Pelanggan', 'mitra' => 'Mitra', 'admin' => 'Admin'] as $nilai => $label)
                <label class="pill">
                    <input type="radio" name="role" value="{{ $nilai }}" @checked($peran === $nilai)>
                    <span>{{ $label }}</span>
                </label>
            @endforeach
        </fieldset>

        @error('role')
            <div class="alert alert-error" role="alert">{{ $message }}</div>
        @enderror
        @error('email')
            <div class="alert alert-error" role="alert">{{ $message }}</div>
        @enderror

        <div class="field @error('email') has-error @enderror">
            <label for="email">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}"
                   autocomplete="email" placeholder="nama@email.com" required autofocus>
        </div>

        <div class="field @error('password') has-error @enderror">
            <label for="password">Kata sandi</label>
            <div class="control">
                <input id="password" type="password" name="password"
                       autocomplete="current-password" placeholder="Masukkan kata sandi" required>
                <button type="button" class="toggle" data-toggle-password="password" aria-pressed="false">Lihat</button>
            </div>
            @error('password') <div class="error-text">{{ $message }}</div> @enderror
        </div>

        <div class="row">
            <label class="check">
                <input type="checkbox" name="remember" value="1" @checked(old('remember'))>
                Ingat saya
            </label>
        </div>

        <button type="submit" class="btn">Masuk</button>
    </form>

    <p class="switch">Belum punya akun? <a href="{{ route('register') }}">Daftar sekarang</a></p>

    @if (app()->environment('local'))
        <div class="demo-box">
            Akun demo (setelah <code>php artisan db:seed</code>):<br>
            Pelanggan: <code>andi@example.com</code> / <code>andi123</code><br>
            Mitra: <code>admin1@example.com</code> / <code>adminmaju</code>, atau <code>staff1@example.com</code> / <code>staffmaju</code><br>
            Admin: <code>superadmin@example.com</code> / <code>superadmin123</code>
        </div>
    @endif
@endsection
