<?php

namespace App\Http\Controllers;

use App\Models\Pesanan;
use App\Models\Reservasi;
use Illuminate\Support\Facades\Auth;

// Isi lonceng notifikasi di kanan atas (dipanggil otomatis tiap 30 detik)
class NotifikasiController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $item = [];
        $jumlah = 0;

        // ---------- Pesanan baru: untuk semua staf ----------
        $pesananBaru = Pesanan::with(['meja', 'user'])->where('status', 'baru')->orderByDesc('created_at');
        $jumlah += (clone $pesananBaru)->count();
        foreach ($pesananBaru->limit(10)->get() as $p) {
            $item[] = [
                'kunci' => 'baru-' . $p->id_pesanan,
                'jenis' => 'baru',
                'judul' => 'Pesanan baru ' . $p->kode_pesanan,
                'isi' => $this->keteranganPesanan($p),
                'waktu' => $p->created_at->diffForHumans(),
                'url' => route('pesanan.show', $p),
            ];
        }

        if ($user->punyaRole('admin', 'kasir')) {
            // ---------- Pesanan siap: perlu disajikan / diantar / diserahkan ----------
            $pesananSiap = Pesanan::with(['meja', 'user'])->where('status', 'siap')->orderBy('created_at');
            $jumlah += (clone $pesananSiap)->count();
            foreach ($pesananSiap->limit(10)->get() as $p) {
                $item[] = [
                    'kunci' => 'siap-' . $p->id_pesanan,
                    'jenis' => 'siap',
                    'judul' => match ($p->tipe_pesanan) {
                        'delivery' => 'Siap diantar: ',
                        'takeaway' => 'Siap diambil: ',
                        default => 'Siap disajikan: ',
                    } . $p->kode_pesanan,
                    'isi' => $this->keteranganPesanan($p),
                    'waktu' => $p->created_at->diffForHumans(),
                    'url' => route('pesanan.show', $p),
                ];
            }

            // ---------- Reservasi yang menunggu konfirmasi ----------
            $reservasi = Reservasi::with('user')
                ->where('status', 'menunggu')
                ->where('tanggal', '>=', now()->toDateString())
                ->orderBy('tanggal')->orderBy('jam');
            $jumlah += (clone $reservasi)->count();
            foreach ($reservasi->limit(10)->get() as $r) {
                $item[] = [
                    'kunci' => 'reservasi-' . $r->id_reservasi,
                    'jenis' => 'reservasi',
                    'judul' => 'Reservasi menunggu konfirmasi',
                    'isi' => ($r->user->nama ?? 'Pelanggan') . ' · ' . $r->jumlah_orang . ' orang',
                    'waktu' => 'Untuk ' . \Carbon\Carbon::parse($r->tanggal)->format('d/m') . ' pukul ' . substr($r->jam, 0, 5),
                    'url' => route('reservasi.edit', $r),
                ];
            }
        }

        return response()->json([
            'jumlah' => $jumlah,
            'item' => $item,
        ]);
    }

    private function keteranganPesanan(Pesanan $p): string
    {
        $tempat = $p->meja ? 'Meja ' . $p->meja->nomor_meja : ucwords(str_replace('_', ' ', $p->tipe_pesanan));
        $nama = $p->user ? ' · ' . $p->user->nama : '';

        return $tempat . $nama . ' · Rp ' . number_format($p->total_harga, 0, ',', '.');
    }
}