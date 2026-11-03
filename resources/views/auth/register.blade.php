@extends('layouts.auth')

@section('title', 'Daftar')
@section('side_title', 'Gabung dan mulai bandingkan mobil')
@section('side_text', 'Buat akun pelanggan untuk menyewa mobil dari berbagai mitra. Data KTP dan SIM baru diminta saat kamu memesan.')

@section('content')
    <h2>Buat akun pelanggan</h2>
    <p class="lead">Gratis, hanya butuh satu menit. Ingin jadi mitra rental? Hubungi admin Rent In-Touch.</p>

    <form method="POST" action="{{ route('register.store') }}" novalidate>
        @csrf

        <div class="field @error('name') has-error @enderror">
            <label for="name">Nama lengkap</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" autocomplete="name" required autofocus>
            @error('name') <div class="error-text">{{ $message }}</div> @enderror
        </div>

        <div class="field @error('email') has-error @enderror">
            <label for="email">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" autocomplete="email" placeholder="nama@email.com" required>
            @error('email') <div class="error-text">{{ $message }}</div> @enderror
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
