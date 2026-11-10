<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

abstract class Controller
{
    // Jumlah data per halaman yang dipilih pengguna (15, 50, atau 100)
    protected function perHalaman(Request $request): int
    {
        $jumlah = (int) $request->input('per_halaman', 15);

        return in_array($jumlah, [15, 50, 100]) ? $jumlah : 15;
    }
}