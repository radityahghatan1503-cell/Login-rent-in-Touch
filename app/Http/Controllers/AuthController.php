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

        // is_active = 1: akun yang dinonaktifkan tidak bisa masuk.
        $attempt = $request->only('email', 'password') + ['is_active' => 1];

        if (! Auth::attempt($attempt, $request->boolean('remember'))) {
            return back()
                ->withInput($request->only('email', 'role', 'remember'))
                ->withErrors(['email' => 'Email atau kata sandi salah, atau akun tidak aktif.']);
        }

        // Akun benar, tapi pastikan tab yang dipilih sesuai dengan grup akun.
        $grup = Auth::user()->grup();
        if ($grup !== $credentials['role']) {
            $label = Auth::user()->grupLabel();
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()
                ->withInput($request->only('email', 'role', 'remember'))
                ->withErrors(['role' => "Akun ini terdaftar sebagai {$label}. Pilih tab {$label} untuk masuk."]);
        }

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    /**
     * Pendaftaran publik hanya untuk pelanggan.
     * Akun global (id_tenant NULL); data tb_pelanggan dibuat saat pertama kali memesan.
     * Akun mitra/staff/admin dibuat oleh admin.
     */
    public function register(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:100', 'unique:tb_users,email'],
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email belum benar.',
            'email.unique' => 'Email ini sudah terdaftar. Silakan masuk.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak sama.',
            'password.min' => 'Kata sandi minimal 8 karakter.',
            'password.letters' => 'Kata sandi harus mengandung huruf.',
            'password.numbers' => 'Kata sandi harus mengandung angka.',
        ]);

        // Password di-hash otomatis oleh cast 'hashed' di model User.
        $user = User::create([
            'id_tenant' => null,
            'nama' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'role' => User::ROLE_PELANGGAN,
            'is_active' => true,
        ]);

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
