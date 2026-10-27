<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
            'role' => ['required', 'in:pelanggan,mitra,admin'],
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email belum benar.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        $attempt = $request->only('email', 'password');

        if (! Auth::attempt($attempt, $request->boolean('remember'))) {
            return back()
                ->withInput($request->only('email', 'role', 'remember'))
                ->withErrors(['email' => 'Email atau kata sandi salah. Periksa lagi lalu coba masuk.']);
        }

        // Akun benar, tapi pastikan tab peran yang dipilih sesuai dengan peran akun.
        if (Auth::user()->role !== $credentials['role']) {
            $peranAkun = Auth::user()->roleLabel();
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()
                ->withInput($request->only('email', 'role', 'remember'))
                ->withErrors(['role' => "Akun ini terdaftar sebagai {$peranAkun}. Pilih tab {$peranAkun} untuk masuk."]);
        }

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'telepon' => ['nullable', 'regex:/^[0-9+\-\s]{8,20}$/'],
            // Admin tidak bisa mendaftar sendiri; dibuat lewat seeder/admin.
            'role' => ['required', 'in:pelanggan,mitra'],
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email belum benar.',
            'email.unique' => 'Email ini sudah terdaftar. Silakan masuk.',
            'telepon.regex' => 'Nomor telepon hanya boleh angka, spasi, + atau -, minimal 8 digit.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak sama.',
            'password.min' => 'Kata sandi minimal 8 karakter.',
            'password.letters' => 'Kata sandi harus mengandung huruf.',
            'password.numbers' => 'Kata sandi harus mengandung angka.',
        ]);

        // Password di-hash otomatis oleh cast 'hashed' di model User.
        $user = User::create($data);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard')
            ->with('success', 'Akun berhasil dibuat. Selamat datang di Rent In-Touch!');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Kamu sudah keluar.');
    }
}
