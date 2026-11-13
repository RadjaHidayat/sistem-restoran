<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CekRole
{
    // Contoh pemakaian di route: ->middleware('role:admin,kasir')
    public function handle(Request $request, Closure $next, string ...$roles)
    {
        $user = $request->user();

        // Akun yang sudah dinonaktifkan langsung dikeluarkan, walaupun sedang login di perangkat lain
        if ($user && ! $user->aktif) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('signin')->withErrors(['email' => 'Akun ini sudah tidak aktif.']);
        }

        if (! $user || ! $user->punyaRole(...$roles)) {
            // Pelanggan yang nyasar ke halaman staf diarahkan ke area pelanggan
            if ($user && $user->punyaRole('pelanggan')) {
                return redirect()->route('akun.index');
            }

            abort(403, 'Kamu tidak punya akses ke halaman ini.');
        }

        return $next($request);
    }
}