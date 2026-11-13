<?php

namespace App\Http\Controllers;

use App\Models\Meja;
use App\Models\Menu;
use App\Models\Pesanan;
use App\Models\Reservasi;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

// Halaman untuk pelanggan yang sudah login
class AkunController extends Controller
{
    // ---------- Beranda pelanggan ----------

    public function index()
    {
        $idUser = Auth::id();
        $hariIni = now()->toDateString();

        // Hanya pesanan HARI INI yang dihitung di kartu ringkasan,
        // jadi besok angkanya mulai dari 0 lagi (data lama tetap ada di Riwayat).
        $pesananHariIni = Pesanan::where('id_user', $idUser)->whereDate('created_at', $hariIni)->get();

        return view('pages.akun.index', [
            'title' => 'Beranda',
            'jumlahHariIni' => $pesananHariIni->count(),
            'belanjaHariIni' => $pesananHariIni->where('status', '!=', 'dibatalkan')->sum('total_harga'),
            'jumlahSemua' => Pesanan::where('id_user', $idUser)->count(),
            'pesananAktif' => Pesanan::with('meja')
                ->where('id_user', $idUser)
                ->whereIn('status', ['baru', 'dimasak', 'siap'])
                ->orderByDesc('created_at')
                ->get(),
            'reservasiMendatang' => Reservasi::with('meja')
                ->where('id_user', $idUser)
                ->where('tanggal', '>=', $hariIni)
                ->whereIn('status', ['menunggu', 'dikonfirmasi'])
                ->orderBy('tanggal')
                ->orderBy('jam')
                ->get(),
        ]);
    }

    // ---------- Riwayat pesanan (per hari) dan riwayat reservasi ----------

    public function riwayat(Request $request)
    {
        $request->validate([
            'tanggal' => ['nullable', 'date_format:Y-m-d'],
        ]);

        $idUser = Auth::id();
        $tab = $request->input('tab') == 'reservasi' ? 'reservasi' : 'pesanan';
        $tanggal = $request->input('tanggal', now()->toDateString());

        // Pesanan pada tanggal terpilih (bawaannya hari ini)
        $pesanan = Pesanan::with(['meja', 'detail.menu'])
            ->where('id_user', $idUser)
            ->whereDate('created_at', $tanggal)
            ->orderByDesc('created_at')
            ->get();

        // Daftar tanggal lain yang ada pesanannya, untuk lompat cepat
        $hariLain = Pesanan::where('id_user', $idUser)
            ->selectRaw('DATE(created_at) as tgl, COUNT(*) as jumlah')
            ->groupBy('tgl')
            ->orderByDesc('tgl')
            ->limit(14)
            ->get();

        $reservasi = Reservasi::with('meja')
            ->where('id_user', $idUser)
            ->orderByDesc('tanggal')
            ->orderByDesc('jam')
            ->paginate(10)
            ->withQueryString();

        return view('pages.akun.riwayat', [
            'title' => 'Riwayat',
            'tab' => $tab,
            'tanggal' => $tanggal,
            'pesanan' => $pesanan,
            'belanja' => $pesanan->where('status', '!=', 'dibatalkan')->sum('total_harga'),
            'hariLain' => $hariLain,
            'reservasi' => $reservasi,
        ]);
    }

    // ---------- Reservasi oleh pelanggan ----------

    public function buatReservasi()
    {
        return view('pages.akun.reservasi', ['title' => 'Buat Reservasi']);
    }

    public function simpanReservasi(Request $request)
    {
        $data = $request->validate([
            'tanggal' => ['required', 'date', 'after_or_equal:today', 'before_or_equal:' . now()->addDays(30)->toDateString()],
            'jam' => ['required', 'date_format:H:i', 'after_or_equal:10:00', 'before_or_equal:21:00'],
            'jumlah_orang' => ['required', 'integer', 'min:1', 'max:20'],
            'catatan' => ['nullable', 'max:255'],
        ], [
            'tanggal.after_or_equal' => 'Tanggal tidak boleh sebelum hari ini.',
            'tanggal.before_or_equal' => 'Reservasi paling lambat 30 hari dari sekarang.',
            'jam.after_or_equal' => 'Restoran buka pukul 10.00.',
            'jam.before_or_equal' => 'Reservasi paling malam pukul 21.00.',
            'jumlah_orang.max' => 'Untuk lebih dari 20 orang, silakan hubungi restoran langsung.',
        ]);

        // Kalau reservasi untuk hari ini, jamnya tidak boleh sudah lewat
        $waktu = Carbon::parse($data['tanggal'] . ' ' . $data['jam']);
        if ($waktu->isPast()) {
            throw ValidationException::withMessages(['jam' => 'Jam tersebut sudah lewat.']);
        }

        Reservasi::create($data + [
            'id_user' => Auth::id(),
            'id_meja' => null,
            'status' => 'menunggu',
        ]);

        return redirect()->route('akun.index')->with('success', 'Reservasi terkirim. Kami akan memilihkan meja dan mengonfirmasinya.');
    }

    public function batalReservasi(Reservasi $reservasi)
    {
        // Pelanggan hanya boleh membatalkan reservasinya sendiri
        if ($reservasi->id_user != Auth::id()) {
            abort(403);
        }

        if (! in_array($reservasi->status, ['menunggu', 'dikonfirmasi'])) {
            return back()->with('error', 'Reservasi ini sudah tidak bisa dibatalkan.');
        }

        $reservasi->update(['status' => 'dibatalkan']);

        // Lepaskan meja yang sempat ditandai "dipesan"
        if ($reservasi->id_meja) {
            Meja::where('id_meja', $reservasi->id_meja)->where('status', 'dipesan')->update(['status' => 'kosong']);
        }

        return back()->with('success', 'Reservasi berhasil dibatalkan.');
    }

    // ---------- Pesan takeaway / delivery oleh pelanggan ----------

    public function pesanMenu()
    {
        $menu = Menu::with('kategori')
            ->where('tersedia', true)
            ->orderBy('id_kategori')
            ->orderBy('nama_menu')
            ->get();

        return view('pages.akun.pesan', [
            'title' => 'Pesan Antar / Ambil',
            'menuPerKategori' => $menu->groupBy(fn ($m) => $m->kategori->nama_kategori),
            'hargaMenu' => $menu->mapWithKeys(fn ($m) => [$m->id_menu => (int) $m->harga]),
        ]);
    }

    public function simpanPesanan(Request $request)
    {
        $request->validate([
            'tipe_pesanan' => ['required', 'in:takeaway,delivery'],
            'alamat_antar' => ['nullable', 'required_if:tipe_pesanan,delivery', 'max:500'],
            'jumlah' => ['required', 'array'],
            'jumlah.*' => ['nullable', 'integer', 'min:0', 'max:50'],
            'catatan' => ['nullable', 'array'],
            'catatan.*' => ['nullable', 'max:255'],
        ], [
            'alamat_antar.required_if' => 'Alamat antar wajib diisi untuk pesanan delivery.',
        ]);

        $jumlahPerMenu = array_filter($request->input('jumlah', []), fn ($j) => (int) $j > 0);
        $menuDipilih = Menu::whereIn('id_menu', array_keys($jumlahPerMenu))->where('tersedia', true)->get();

        if ($menuDipilih->isEmpty()) {
            return back()->withInput()->with('error', 'Pilih minimal 1 menu terlebih dahulu.');
        }

        $pesanan = DB::transaction(function () use ($request, $jumlahPerMenu, $menuDipilih) {
            $pesanan = Pesanan::create([
                'kode_pesanan' => 'ORD-' . now()->format('ymd') . '-' . Str::upper(Str::random(5)),
                'id_user' => Auth::id(),
                'id_meja' => null,
                'id_petugas' => null,
                'tipe_pesanan' => $request->tipe_pesanan,
                'status' => 'baru',
                'total_harga' => 0,
                'alamat_antar' => $request->tipe_pesanan == 'delivery' ? $request->alamat_antar : null,
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

            return $pesanan;
        });

        return redirect()->route('akun.pesanan', $pesanan)->with('success', 'Pesanan berhasil dikirim!');
    }

    public function detailPesanan(Pesanan $pesanan)
    {
        // Pelanggan hanya boleh melihat pesanannya sendiri
        if ($pesanan->id_user != Auth::id()) {
            abort(403);
        }

        $pesanan->load(['detail.menu', 'meja', 'pembayaran']);

        return view('pages.akun.pesanan', [
            'title' => 'Pesanan ' . $pesanan->kode_pesanan,
            'pesanan' => $pesanan,
        ]);
    }

    // ---------- Profil pelanggan (dipakai di langkah 18b) ----------

    public function profil()
    {
        return view('pages.akun.profil', [
            'title' => 'Profil Saya',
            'user' => Auth::user(),
            'jumlahPesanan' => Pesanan::where('id_user', Auth::id())->count(),
        ]);
    }

    public function simpanProfil(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'nama' => ['required', 'max:100'],
            'email' => ['required', 'email', 'max:100', Rule::unique('users', 'email')->ignore($user->id_user, 'id_user')],
            'no_hp' => ['required', 'max:20'],
            'alamat' => ['nullable', 'max:500'],
        ], [
            'email.unique' => 'Email ini sudah dipakai akun lain.',
        ]);

        $user->update($data);

        return back()->with('success', 'Profil berhasil disimpan.');
    }

    public function gantiPassword(Request $request)
    {
        $request->validate([
            'password_lama' => ['required', 'current_password'],
            'password' => ['required', 'min:6', 'confirmed', 'different:password_lama'],
        ], [
            'password_lama.current_password' => 'Password lama salah.',
            'password.confirmed' => 'Konfirmasi password baru tidak sama.',
            'password.different' => 'Password baru harus berbeda dari password lama.',
        ]);

        Auth::user()->update(['password' => $request->password]);

        return back()->with('success', 'Password berhasil diganti. Pakai password baru saat login berikutnya.');
    }

    public function nonaktifkan(Request $request)
    {
        $request->validate([
            'password_konfirmasi' => ['required', 'current_password'],
            'setuju' => ['accepted'],
        ], [
            'password_konfirmasi.required' => 'Masukkan password untuk konfirmasi.',
            'password_konfirmasi.current_password' => 'Password salah.',
            'setuju.accepted' => 'Centang pernyataan di atas untuk melanjutkan.',
        ]);

        $user = Auth::user();

        // Jangan nonaktifkan kalau masih ada pesanan yang sedang diproses
        $masihDiproses = Pesanan::where('id_user', $user->id_user)
            ->whereIn('status', ['baru', 'dimasak', 'siap'])
            ->exists();

        if ($masihDiproses) {
            return back()->with('error', 'Masih ada pesanan yang sedang diproses. Tunggu sampai selesai, lalu coba lagi.');
        }

        DB::transaction(function () use ($user) {
            // Batalkan reservasi yang masih berjalan, dan lepaskan mejanya
            $reservasi = Reservasi::where('id_user', $user->id_user)
                ->whereIn('status', ['menunggu', 'dikonfirmasi'])
                ->get();

            foreach ($reservasi as $r) {
                $r->update(['status' => 'dibatalkan']);

                if ($r->id_meja) {
                    Meja::where('id_meja', $r->id_meja)->where('status', 'dipesan')->update(['status' => 'kosong']);
                }
            }

            // Akun dinonaktifkan, BUKAN dihapus, supaya riwayat pesanan restoran tetap utuh.
            // Email asli disimpan di email_lama, lalu kolom email diganti
            // supaya alamat email itu bisa dipakai lagi untuk mendaftar akun baru.
            $user->update([
                'aktif' => false,
                'dinonaktifkan_pada' => now(),
                'email_lama' => $user->email,
                'email' => 'nonaktif-' . $user->id_user . '@akun.nonaktif',
                'password' => Str::random(40),
            ]);
        });

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('signin')->with('success', 'Akunmu sudah dinonaktifkan. Terima kasih sudah memesan di sini. Kalau ingin memesan lagi, silakan daftar akun baru.');
    }
}