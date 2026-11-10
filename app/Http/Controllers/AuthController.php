<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Role;
use App\Models\User;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('pages.auth.signin', ['title' => 'Sign In']);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // Pelanggan diarahkan ke area pelanggan, staf ke dashboard
            if (Auth::user()->punyaRole('pelanggan')) {
                return redirect()->route('akun.index');
            }

            return redirect()->intended(route('dashboard'));
        }
        return back()->withErrors(['email' => 'Email atau password salah.'])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('signin');
    }

        public function showRegister()
    {
        return view('pages.auth.signup', ['title' => 'Daftar Akun']);
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'nama' => ['required', 'max:100'],
            'email' => ['required', 'email', 'max:100', 'unique:users,email'],
            'no_hp' => ['required', 'max:20'],
            'alamat' => ['nullable'],
            'password' => ['required', 'min:6', 'confirmed'],
        ], [
            'email.unique' => 'Email ini sudah terdaftar. Silakan login.',
            'password.confirmed' => 'Konfirmasi password tidak sama.',
        ]);

        // Pendaftar selalu menjadi pelanggan, tidak bisa memilih role sendiri
        $data['id_role'] = Role::where('nama_role', 'pelanggan')->value('id_role');

        $user = User::create($data);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('akun.index')->with('success', 'Akun berhasil dibuat. Selamat datang, ' . $user->nama . '!');
    }
}