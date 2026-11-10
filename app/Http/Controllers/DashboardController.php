<?php

namespace App\Http\Controllers;

use App\Models\DetailPesanan;
use App\Models\Meja;
use App\Models\Pembayaran;
use App\Models\Pesanan;
use App\Models\Reservasi;

class DashboardController extends Controller
{
    public function index()
    {
        $hariIni = now()->toDateString();

        // ---------- Angka ringkasan hari ini ----------
        $pembayaranHariIni = Pembayaran::with('pesanan')->whereDate('waktu_bayar', $hariIni)->get();
        $pendapatanHariIni = $pembayaranHariIni->sum(fn ($p) => $p->pesanan->total_harga);

        $pesananHariIni = Pesanan::whereDate('created_at', $hariIni)->where('status', '!=', 'dibatalkan')->count();
        $pesananAktif = Pesanan::with('meja')->whereIn('status', ['baru', 'dimasak', 'siap'])->orderBy('created_at')->get();

        $mejaTerisi = Meja::where('status', 'terisi')->count();
        $totalMeja = Meja::count();

        $reservasiHariIni = Reservasi::with(['user', 'meja'])
            ->where('tanggal', $hariIni)
            ->whereIn('status', ['menunggu', 'dikonfirmasi'])
            ->orderBy('jam')
            ->get();

        // ---------- Pendapatan 7 hari terakhir (untuk grafik batang) ----------
        $namaHari = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
        $grafik = [];
        for ($i = 6; $i >= 0; $i--) {
            $tanggal = now()->subDays($i);
            $total = Pembayaran::with('pesanan')
                ->whereDate('waktu_bayar', $tanggal->toDateString())
                ->get()
                ->sum(fn ($p) => $p->pesanan->total_harga);

            $grafik[] = [
                'label' => $namaHari[$tanggal->dayOfWeek] . ' ' . $tanggal->format('d/m'),
                'total' => $total,
            ];
        }
        $grafikMaks = max(1, max(array_column($grafik, 'total')));

        // ---------- 5 menu terlaris 7 hari terakhir ----------
        $menuTerlaris = DetailPesanan::with('menu')
            ->selectRaw('id_menu, SUM(jumlah) as total_terjual')
            ->whereHas('pesanan', function ($q) {
                $q->where('status', '!=', 'dibatalkan')
                    ->where('created_at', '>=', now()->subDays(6)->startOfDay());
            })
            ->groupBy('id_menu')
            ->orderByDesc('total_terjual')
            ->limit(5)
            ->get();

        return view('pages.dashboard.index', [
            'title' => 'Dashboard',
            'pendapatanHariIni' => $pendapatanHariIni,
            'transaksiHariIni' => $pembayaranHariIni->count(),
            'pesananHariIni' => $pesananHariIni,
            'pesananAktif' => $pesananAktif,
            'mejaTerisi' => $mejaTerisi,
            'totalMeja' => $totalMeja,
            'reservasiHariIni' => $reservasiHariIni,
            'grafik' => $grafik,
            'grafikMaks' => $grafikMaks,
            'menuTerlaris' => $menuTerlaris,
        ]);
    }
}