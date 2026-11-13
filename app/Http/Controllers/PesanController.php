<?php

namespace App\Http\Controllers;

use App\Models\Meja;
use App\Models\Menu;
use App\Models\Pesanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

// Halaman pemesanan untuk PELANGGAN (dibuka dengan scan QR di meja, tanpa login)
class PesanController extends Controller
{
    public function menu(string $kodeQr)
    {
        $meja = Meja::where('kode_qr', $kodeQr)->firstOrFail();

        $menu = Menu::with('kategori')
            ->where('tersedia', true)
            ->orderBy('id_kategori')
            ->orderBy('nama_menu')
            ->get();

        // Pesanan yang dikirim dari HP/browser ini untuk meja ini, hari ini
        $pesananSaya = Pesanan::whereIn('kode_pesanan', session('pesanan_saya', []))
            ->where('id_meja', $meja->id_meja)
            ->whereDate('created_at', now()->toDateString())
            ->orderByDesc('created_at')
            ->get();

        return view('pages.pesan.menu', [
            'title' => 'Pesan Menu - Meja ' . $meja->nomor_meja,
            'meja' => $meja,
            'menuPerKategori' => $menu->groupBy(fn ($m) => $m->kategori->nama_kategori),
            'pesananSaya' => $pesananSaya,
        ]);
    }

    public function store(Request $request, string $kodeQr)
    {
        $meja = Meja::where('kode_qr', $kodeQr)->firstOrFail();

        $request->validate([
            'jumlah' => ['required', 'array'],
            'jumlah.*' => ['nullable', 'integer', 'min:0', 'max:50'],
            'catatan' => ['nullable', 'array'],
            'catatan.*' => ['nullable', 'max:255'],
        ]);

        $jumlahPerMenu = array_filter($request->input('jumlah', []), fn ($j) => (int) $j > 0);

        // Hanya menu yang masih tersedia yang boleh dipesan
        $menuDipilih = Menu::whereIn('id_menu', array_keys($jumlahPerMenu))->where('tersedia', true)->get();

        if ($menuDipilih->isEmpty()) {
            return back()->withInput()->with('error', 'Pilih minimal 1 menu terlebih dahulu.');
        }

        $pesanan = DB::transaction(function () use ($request, $meja, $jumlahPerMenu, $menuDipilih) {
            $pesanan = Pesanan::create([
                'kode_pesanan' => 'ORD-' . now()->format('ymd') . '-' . Str::upper(Str::random(5)),
                'id_user' => auth()->user()?->punyaRole('pelanggan') ? auth()->id() : null,
                'id_meja' => $meja->id_meja,
                'id_petugas' => null,
                'tipe_pesanan' => 'dine_in',
                'status' => 'baru',
                'total_harga' => 0,
            ]);

            $total = 0;
            foreach ($menuDipilih as $m) {
                $jumlah = (int) $jumlahPerMenu[$m->id_menu];
                $subtotal = $m->harga * $jumlah;

                $pesanan->detail()->create([
                    'id_menu' => $m->id_menu,
                    'jumlah' => $jumlah,
                    'harga_satuan' => $m->harga,
                    'subtotal' => $subtotal,
                    'catatan' => $request->input('catatan.' . $m->id_menu),
                ]);

                $total += $subtotal;
            }

            $pesanan->update(['total_harga' => $total]);
            $meja->update(['status' => 'terisi']);

            return $pesanan;
        });
                // Ingat kode pesanan ini di sesi browser, supaya muncul di kotak "Pesananmu"
        session()->push('pesanan_saya', $pesanan->kode_pesanan);


        return redirect()->route('pesan.status', [$meja->kode_qr, $pesanan->kode_pesanan]);
    }

    public function status(string $kodeQr, string $kodePesanan)
    {
        $pesanan = Pesanan::with(['detail.menu', 'meja', 'pembayaran'])
            ->where('kode_pesanan', $kodePesanan)
            ->whereHas('meja', fn ($q) => $q->where('kode_qr', $kodeQr))
            ->firstOrFail();

        return view('pages.pesan.status', [
            'title' => 'Status Pesanan ' . $pesanan->kode_pesanan,
            'pesanan' => $pesanan,
        ]);
    }
}