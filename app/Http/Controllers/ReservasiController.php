<?php

namespace App\Http\Controllers;

use App\Models\Meja;
use App\Models\Reservasi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ReservasiController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->input('tab', 'mendatang');
        $hariIni = now()->toDateString();

        $query = Reservasi::with(['user', 'meja']);

        // Pencarian nama pelanggan atau nomor meja
        if ($request->filled('cari')) {
            $cari = '%' . $request->cari . '%';
            $query->where(function ($q) use ($cari) {
                $q->whereHas('user', fn ($u) => $u->where('nama', 'like', $cari))
                    ->orWhereHas('meja', fn ($m) => $m->where('nomor_meja', 'like', $cari));
            });
        }

        if ($tab == 'mendatang') {
            // Reservasi yang masih akan datang dan belum selesai/batal, yang paling dekat di atas
            $query->where('tanggal', '>=', $hariIni)
                ->whereIn('status', ['menunggu', 'dikonfirmasi'])
                ->orderBy('tanggal')->orderBy('jam');
        } else {
            // Riwayat: sudah selesai, dibatalkan, atau tanggalnya sudah lewat
            $query->where(function ($q) use ($hariIni) {
                $q->whereIn('status', ['selesai', 'dibatalkan'])
                    ->orWhere('tanggal', '<', $hariIni);
            })->orderByDesc('tanggal')->orderByDesc('jam');
        }

        return view('pages.reservasi.index', [
            'title' => 'Reservasi',
            'reservasi' => $query->paginate($this->perHalaman($request))->withQueryString(),
            'tab' => $tab,
        ]);
    }

    public function create()
    {
        return view('pages.reservasi.create', $this->dataForm() + ['title' => 'Tambah Reservasi']);
    }

    public function store(Request $request)
    {
        $data = $this->validasi($request, true);
        $data['status'] = empty($data['id_meja']) ? 'menunggu' : 'dikonfirmasi';

        $reservasi = Reservasi::create($data);
        $this->aturStatusMeja($reservasi, null);

        return redirect()->route('reservasi.index')->with('success', 'Reservasi berhasil ditambahkan.');
    }

    public function edit(Reservasi $reservasi)
    {
        return view('pages.reservasi.edit', $this->dataForm() + [
            'title' => 'Edit Reservasi',
            'reservasi' => $reservasi,
        ]);
    }

    public function update(Request $request, Reservasi $reservasi)
    {
        $data = $this->validasi($request, false);
        $mejaLama = $reservasi->id_meja;

        $reservasi->update($data);
        $this->aturStatusMeja($reservasi, $mejaLama);

        return redirect()->route('reservasi.index')->with('success', 'Reservasi berhasil diubah.');
    }

    private function dataForm(): array
    {
        return [
            'pelanggan' => User::whereHas('role', fn ($q) => $q->where('nama_role', 'pelanggan'))->orderBy('nama')->get(),
            'meja' => Meja::orderBy('nomor_meja')->get(),
        ];
    }

    private function validasi(Request $request, bool $baru): array
    {
        $aturan = [
            'id_user' => ['required', 'exists:users,id_user'],
            'id_meja' => ['nullable', 'exists:meja,id_meja'],
            'tanggal' => $baru ? ['required', 'date', 'after_or_equal:today'] : ['required', 'date'],
            'jam' => ['required', 'date_format:H:i'],
            'jumlah_orang' => ['required', 'integer', 'min:1'],
            'catatan' => ['nullable'],
        ];

        if (! $baru) {
            $aturan['status'] = ['required', 'in:menunggu,dikonfirmasi,dibatalkan,selesai'];
        }

        $data = $request->validate($aturan, [
            'id_user.required' => 'Pilih pelanggan yang melakukan reservasi.',
            'tanggal.after_or_equal' => 'Tanggal reservasi tidak boleh sebelum hari ini.',
        ]);

        // Cek kapasitas meja cukup untuk jumlah orang
        if (! empty($data['id_meja'])) {
            $meja = Meja::find($data['id_meja']);
            if ($meja->kapasitas < $data['jumlah_orang']) {
                throw ValidationException::withMessages([
                    'id_meja' => 'Kapasitas meja ' . $meja->nomor_meja . ' hanya ' . $meja->kapasitas . ' orang.',
                ]);
            }
        }

        return $data;
    }

    // Menyesuaikan status meja dengan status reservasi
    private function aturStatusMeja(Reservasi $reservasi, $idMejaLama): void
    {
        // Meja lama dilepas kalau mejanya diganti
        if ($idMejaLama && $idMejaLama != $reservasi->id_meja) {
            Meja::where('id_meja', $idMejaLama)->where('status', 'dipesan')->update(['status' => 'kosong']);
        }

        if (! $reservasi->id_meja) {
            return;
        }

        if ($reservasi->status == 'dikonfirmasi') {
            // Tandai meja sudah dipesan (kecuali sedang terisi tamu lain)
            Meja::where('id_meja', $reservasi->id_meja)->where('status', 'kosong')->update(['status' => 'dipesan']);
        } else {
            // Menunggu, dibatalkan, atau selesai: lepaskan tanda "dipesan"
            Meja::where('id_meja', $reservasi->id_meja)->where('status', 'dipesan')->update(['status' => 'kosong']);
        }
    }
}