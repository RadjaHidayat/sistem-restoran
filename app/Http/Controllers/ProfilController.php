<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use App\Models\Pesanan;
use Illuminate\Support\Facades\Auth;

// Halaman profil untuk staf (admin, kasir, koki). Hanya untuk dilihat.
class ProfilController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $hariIni = now()->toDateString();

        return view('pages.profile', [
            'title' => 'Profil Saya',
            'user' => $user,
            // Ringkasan kerja hari ini
            'pesananDicatat' => Pesanan::where('id_petugas', $user->id_user)->whereDate('created_at', $hariIni)->count(),
            'pembayaranDicatat' => Pembayaran::where('id_kasir', $user->id_user)->whereDate('waktu_bayar', $hariIni)->count(),
        ]);
    }
}