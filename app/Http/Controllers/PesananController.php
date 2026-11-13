<?php

namespace App\Http\Controllers;

use App\Models\Meja;
use App\Models\Menu;
use App\Models\Pesanan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PesananController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'tanggal' => ['nullable', 'date_format:Y-m-d'],
            'status' => ['nullable', 'in:semua,baru,dimasak,siap,selesai,dibatalkan'],
        ]);

        $tanggal = $request->input('tanggal', now()->toDateString());
        $status = $request->input('status', 'semua');
        $cari = trim((string) $request->input('cari', ''));

        // ---------- Pesanan yang "terlihat": tanggal terpilih, atau semua tanggal kalau sedang mencari ----------
        $dasar = Pesanan::query();

        if ($cari !== '') {
            $dasar->where(function ($q) use ($cari) {
                $q->where('kode_pesanan', 'like', "%{$cari}%")
                    ->orWhereHas('user', fn ($u) => $u->where('nama', 'like', "%{$cari}%"))
                    ->orWhereHas('meja', fn ($m) => $m->where('nomor_meja', 'like', "%{$cari}%"))
                    ->orWhereHas('detail.menu', fn ($m) => $m->where('nama_menu', 'like', "%{$cari}%"));
            });
        } else {
            $dasar->whereDate('created_at', $tanggal);
        }

        // ---------- Jumlah pesanan per status, untuk angka di tiap tab ----------
        $perStatus = (clone $dasar)
            ->selectRaw('status, COUNT(*) as jumlah')
            ->groupBy('status')
            ->pluck('jumlah', 'status');

        $jumlah = ['semua' => (int) $perStatus->sum()];
        foreach (['baru', 'dimasak', 'siap', 'selesai', 'dibatalkan'] as $s) {
            $jumlah[$s] = (int) ($perStatus[$s] ?? 0);
        }

        // ---------- Isi tabel ----------
        $query = (clone $dasar)->with(['meja', 'user']);

        if ($status != 'semua') {
            $query->where('status', $status);
        }

        // Pesanan yang masih dikerjakan: yang paling lama menunggu di atas.
        // Selain itu: yang terbaru di atas.
        if (in_array($status, ['baru', 'dimasak', 'siap'])) {
            $query->orderBy('created_at');
        } else {
            $query->orderByDesc('created_at');
        }

        // ---------- Pengingat: pesanan hari sebelumnya yang belum selesai ----------
        $tertunda = Pesanan::whereIn('status', ['baru', 'dimasak', 'siap'])
            ->whereDate('created_at', '<', now()->toDateString())
            ->orderBy('created_at')
            ->get(['id_pesanan', 'created_at']);

        return view('pages.pesanan.index', [
            'title' => 'Pesanan',
            'pesanan' => $query->paginate($this->perHalaman($request))->withQueryString(),
            'statusDipilih' => $status,
            'tanggal' => $tanggal,
            'cari' => $cari,
            'jumlah' => $jumlah,
            'jumlahTertunda' => $tertunda->count(),
            'tanggalTertunda' => $tertunda->first()?->created_at->toDateString(),
        ]);
    }

    public function create()
    {
        return view('pages.pesanan.create', [
            'title' => 'Buat Pesanan',
            'menu' => Menu::with('kategori')->where('tersedia', true)->orderBy('id_kategori')->orderBy('nama_menu')->get(),
            'meja' => Meja::where('status', '!=', 'terisi')->orderBy('nomor_meja')->get(),
            'pelanggan' => User::whereHas('role', fn ($q) => $q->where('nama_role', 'pelanggan'))->where('aktif', true)->orderBy('nama')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'tipe_pesanan' => ['required', 'in:dine_in,takeaway,delivery'],
            'id_meja' => ['nullable', 'required_if:tipe_pesanan,dine_in', 'exists:meja,id_meja'],
            'id_user' => ['nullable', 'exists:users,id_user'],
            'alamat_antar' => ['nullable', 'required_if:tipe_pesanan,delivery'],
            'jumlah' => ['required', 'array'],
            'jumlah.*' => ['nullable', 'integer', 'min:0'],
            'catatan' => ['nullable', 'array'],
        ], [
            'id_meja.required_if' => 'Pesanan dine in wajib memilih meja.',
            'alamat_antar.required_if' => 'Pesanan delivery wajib mengisi alamat antar.',
        ]);

        // Ambil hanya menu yang jumlahnya lebih dari 0
        $jumlahPerMenu = array_filter($request->input('jumlah', []), fn ($j) => (int) $j > 0);

        if (empty($jumlahPerMenu)) {
            return back()->withInput()->with('error', 'Pilih minimal 1 menu dengan jumlah lebih dari 0.');
        }

        $pesanan = DB::transaction(function () use ($request, $jumlahPerMenu) {
            $pesanan = Pesanan::create([
                'kode_pesanan' => 'ORD-' . now()->format('ymd') . '-' . Str::upper(Str::random(5)),
                'id_user' => $request->id_user,
                'id_meja' => $request->tipe_pesanan == 'dine_in' ? $request->id_meja : null,
                'id_petugas' => Auth::id(),
                'tipe_pesanan' => $request->tipe_pesanan,
                'status' => 'baru',
                'total_harga' => 0,
                'alamat_antar' => $request->tipe_pesanan == 'delivery' ? $request->alamat_antar : null,
            ]);

            $total = 0;
            $menuDipilih = Menu::whereIn('id_menu', array_keys($jumlahPerMenu))->get();

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

            if ($pesanan->id_meja) {
                Meja::where('id_meja', $pesanan->id_meja)->update(['status' => 'terisi']);
            }

            return $pesanan;
        });

        return redirect()->route('pesanan.show', $pesanan)->with('success', 'Pesanan ' . $pesanan->kode_pesanan . ' berhasil dibuat.');
    }

    public function show(Pesanan $pesanan)
    {
        $pesanan->load(['detail.menu', 'meja', 'user', 'petugas', 'pembayaran']);

        return view('pages.pesanan.show', [
            'title' => 'Detail Pesanan',
            'pesanan' => $pesanan,
        ]);
    }

    public function updateStatus(Request $request, Pesanan $pesanan)
    {
        $request->validate([
            'status' => ['required', 'in:baru,dimasak,siap,selesai,dibatalkan'],
        ]);

        $pesanan->update(['status' => $request->status]);

        // Kalau pesanan selesai atau dibatalkan, meja dikosongkan lagi
        if (in_array($request->status, ['selesai', 'dibatalkan']) && $pesanan->id_meja) {
            Meja::where('id_meja', $pesanan->id_meja)->update(['status' => 'kosong']);
        }

        return redirect()->route('pesanan.show', $pesanan)->with('success', 'Status pesanan diubah menjadi "' . $request->status . '".');
    }
}