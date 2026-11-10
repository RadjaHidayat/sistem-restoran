<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use App\Models\Pesanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PembayaranController extends Controller
{
       public function index(Request $request)
    {
        $tanggal = $request->input('tanggal', now()->toDateString());

        // Semua pembayaran pada tanggal itu, untuk menghitung ringkasan pendapatan
        $semua = Pembayaran::with('pesanan')->whereDate('waktu_bayar', $tanggal)->get();
        $totalPendapatan = $semua->sum(fn ($p) => $p->pesanan->total_harga);
        $perMetode = [];
        foreach (['tunai', 'qris', 'transfer'] as $m) {
            $perMetode[$m] = $semua->where('metode', $m)->sum(fn ($p) => $p->pesanan->total_harga);
        }

        // Riwayat pembayaran yang ditampilkan di tabel (per halaman)
        $pembayaran = Pembayaran::with(['pesanan', 'kasir'])
            ->whereDate('waktu_bayar', $tanggal)
            ->orderByDesc('waktu_bayar')
            ->paginate($this->perHalaman($request))
            ->withQueryString();

        // Semua pesanan yang belum dibayar (kecuali yang dibatalkan)
        $belumBayar = Pesanan::with('meja')
            ->doesntHave('pembayaran')
            ->where('status', '!=', 'dibatalkan')
            ->orderBy('created_at')
            ->get();

        return view('pages.pembayaran.index', [
            'title' => 'Pembayaran',
            'tanggal' => $tanggal,
            'pembayaran' => $pembayaran,
            'belumBayar' => $belumBayar,
            'totalPendapatan' => $totalPendapatan,
            'perMetode' => $perMetode,
            'jumlahTransaksi' => $semua->count(),
        ]);
    }
    public function store(Request $request, Pesanan $pesanan)
    {
        if ($pesanan->pembayaran) {
            return back()->with('error', 'Pesanan ini sudah dibayar.');
        }

        if ($pesanan->status == 'dibatalkan') {
            return back()->with('error', 'Pesanan yang dibatalkan tidak bisa dibayar.');
        }

        $request->validate([
            'metode' => ['required', 'in:tunai,qris,transfer'],
            'jumlah_bayar' => ['nullable', 'required_if:metode,tunai', 'numeric', 'min:' . $pesanan->total_harga],
        ], [
            'jumlah_bayar.required_if' => 'Isi jumlah uang yang diterima.',
            'jumlah_bayar.min' => 'Uang yang diterima kurang dari total pesanan.',
        ]);

        // Tunai: simpan uang yang diterima (bisa lebih, nanti ada kembalian)
        // QRIS / transfer: jumlahnya pasti sama dengan total
        $jumlahBayar = $request->metode == 'tunai' ? $request->jumlah_bayar : $pesanan->total_harga;

        Pembayaran::create([
            'id_pesanan' => $pesanan->id_pesanan,
            'id_kasir' => Auth::id(),
            'metode' => $request->metode,
            'jumlah_bayar' => $jumlahBayar,
            'status' => 'lunas',
            'waktu_bayar' => now(),
        ]);

        $pesan = 'Pembayaran berhasil dicatat.';
        $kembalian = $jumlahBayar - $pesanan->total_harga;
        if ($kembalian > 0) {
            $pesan .= ' Kembalian: Rp ' . number_format($kembalian, 0, ',', '.');
        }

        return redirect()->route('pesanan.show', $pesanan)->with('success', $pesan);
    }
}