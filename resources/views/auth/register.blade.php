@extends('layouts.auth')

@section('title', 'Daftar')
@section('side_title', 'Gabung dan mulai bandingkan mobil')
@section('side_text', 'Daftar sebagai pelanggan untuk menyewa, atau sebagai mitra untuk menawarkan unit rentalmu.')

@section('content')
    <h2>Buat akun baru</h2>
    <p class="lead">Gratis, hanya butuh satu menit.</p>

    <form method="POST" action="{{ route('register.store') }}" novalidate>
        @csrf

        @php $peran = old('role', 'pelanggan'); @endphp
        <fieldset class="pills">
            <legend>Daftar sebagai</legend>
            @foreach (['pelanggan' => 'Pelanggan', 'mitra' => 'Mitra'] as $nilai => $label)
                <label class="pill">
                    <input type="radio" name="role" value="{{ $nilai }}" @checked($peran === $nilai)>
                    <span>{{ $label }}</span>
                </label>
            @endforeach
        </fieldset>
        @error('role') <div class="alert alert-error" role="alert">{{ $message }}</div> @enderror

        <div class="field @error('name') has-error @enderror">
            <label for="name">Nama lengkap</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" autocomplete="name" required autofocus>
            @error('name') <div class="error-text">{{ $message }}</div> @enderror
        </div>

        <div class="grid-2">
            <div class="field @error('email') has-error @enderror">
                <label for="email">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" autocomplete="email" placeholder="nama@email.com" required>
                @error('email') <div class="error-text">{{ $message }}</div> @enderror
            </div>
            <div class="field @error('telepon') has-error @enderror">
                <label for="telepon">Telepon (opsional)</label>
                <input id="telepon" type="tel" name="telepon" value="{{ old('telepon') }}" autocomplete="tel" placeholder="08xxxxxxxxxx">
                @error('telepon') <div class="error-text">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="field @error('password') has-error @enderror">
            <label for="password">Kata sandi</label>
            <div class="control">
                <input id="password" type="password" name="password" autocomplete="new-password" required>
                <button type="button" class="toggle" data-toggle-password="password" aria-pressed="false">Lihat</button>
            </div>
            @error('password')
                <div class="error-text">{{ $message }}</div>
            @else
                <div class="hint">Minimal 8 karakter, kombinasi huruf dan angka.</div>
            @enderror
        </div>

        <div class="field">
            <label for="password_confirmation">Ulangi kata sandi</label>
            <div class="control">
                <input id="password_confirmation" type="password" name="password_confirmation" autocomplete="new-password" required>
                <button type="button" class="toggle" data-toggle-password="password_confirmation" aria-pressed="false">Lihat</button>
            </div>
        </div>

        <button type="submit" class="btn">Buat akun</button>
    </form>

    <p class="switch">Sudah punya akun? <a href="{{ route('login') }}">Masuk</a></p>
@endsection
