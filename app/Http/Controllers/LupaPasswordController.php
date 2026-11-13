<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

// Lupa password untuk PELANGGAN: kirim link reset ke email, lalu buat password baru
class LupaPasswordController extends Controller
{
    public function form()
    {
        return view('pages.auth.lupa-password', ['title' => 'Lupa Password']);
    }

    public function kirimLink(Request $request)
    {
        $request->validate(['email' => ['required', 'email']]);

        // Link hanya dikirim untuk akun PELANGGAN yang masih aktif.
        // Akun staf direset oleh admin lewat halaman Pengguna.
        $user = User::where('email', $request->email)
            ->where('aktif', true)
            ->whereHas('role', fn ($q) => $q->where('nama_role', 'pelanggan'))
            ->first();

        if ($user) {
            $status = Password::sendResetLink(['email' => $user->email]);

            if ($status == Password::RESET_THROTTLED) {
                return back()->withInput()->withErrors(['email' => 'Link baru saja dikirim. Tunggu 1 menit sebelum meminta lagi.']);
            }
        }

        // Pesannya sengaja sama, baik email terdaftar maupun tidak,
        // supaya orang lain tidak bisa menebak email siapa saja yang terdaftar.
        return back()->with('success', 'Kalau email itu terdaftar sebagai akun pelanggan, link untuk membuat password baru sudah kami kirim. Cek kotak masuk atau folder spam. Link berlaku 60 menit.');
    }

    public function formReset(Request $request, string $token)
    {
        return view('pages.auth.reset-password', [
            'title' => 'Buat Password Baru',
            'token' => $token,
            'email' => $request->query('email'),
        ]);
    }

    public function simpanReset(Request $request)
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'min:6', 'confirmed'],
        ], [
            'password.confirmed' => 'Konfirmasi password tidak sama.',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                // Model User sudah otomatis mengenkripsi password ('password' => 'hashed')
                $user->update(['password' => $password]);
            }
        );

        if ($status == Password::PASSWORD_RESET) {
            return redirect()->route('signin')->with('success', 'Password berhasil diganti. Silakan masuk dengan password baru.');
        }

        $pesan = match ($status) {
            Password::INVALID_TOKEN => 'Link reset sudah tidak berlaku atau sudah pernah dipakai. Minta link baru dari halaman Lupa Password.',
            Password::INVALID_USER => 'Email tidak cocok dengan link reset ini.',
            default => 'Gagal mengganti password. Coba minta link baru.',
        };

        return back()->withInput($request->only('email'))->withErrors(['email' => $pesan]);
    }
}