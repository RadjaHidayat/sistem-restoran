<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use App\Models\Pesanan;
use App\Models\Reservasi;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PenggunaController extends Controller
{
    public function index(Request $request)
    {
        $pengguna = User::with('role')
            ->orderBy('id_role')
            ->orderBy('nama')
            ->paginate($this->perHalaman($request))
            ->withQueryString();

        return view('pages.pengguna.index', [
            'title' => 'Pengguna',
            'pengguna' => $pengguna,
        ]);
    }

    public function create()
    {
        return view('pages.pengguna.create', [
            'title' => 'Tambah Pengguna',
            'roles' => Role::orderBy('id_role')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'id_role' => ['required', 'exists:roles,id_role'],
            'nama' => ['required', 'max:100'],
            'email' => ['required', 'email', 'max:100', 'unique:users,email'],
            'password' => ['required', 'min:6'],
            'no_hp' => ['nullable', 'max:20'],
            'alamat' => ['nullable'],
        ]);

        User::create($data);

        return redirect()->route('pengguna.index')->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function edit(User $pengguna)
    {
        return view('pages.pengguna.edit', [
            'title' => 'Edit Pengguna',
            'pengguna' => $pengguna,
            'roles' => Role::orderBy('id_role')->get(),
        ]);
    }

    public function update(Request $request, User $pengguna)
    {
        $data = $request->validate([
            'id_role' => ['required', 'exists:roles,id_role'],
            'nama' => ['required', 'max:100'],
            'email' => ['required', 'email', 'max:100', 'unique:users,email,' . $pengguna->id_user . ',id_user'],
            'password' => ['nullable', 'min:6'],
            'no_hp' => ['nullable', 'max:20'],
            'alamat' => ['nullable'],
        ]);

        // Kalau password dikosongkan, password lama tetap dipakai
        if (empty($data['password'])) {
            unset($data['password']);
        }

        $pengguna->update($data);

        return redirect()->route('pengguna.index')->with('success', 'Pengguna berhasil diubah.');
    }

    public function destroy(User $pengguna)
    {
        if ($pengguna->id_user == Auth::id()) {
            return redirect()->route('pengguna.index')->with('error', 'Kamu tidak bisa menghapus akunmu sendiri.');
        }

        $id = $pengguna->id_user;
        $punyaRiwayat = Pesanan::where('id_user', $id)->orWhere('id_petugas', $id)->exists()
            || Reservasi::where('id_user', $id)->exists()
            || Pembayaran::where('id_kasir', $id)->exists();

        if ($punyaRiwayat) {
            return redirect()->route('pengguna.index')->with('error', 'Pengguna tidak bisa dihapus karena sudah punya riwayat pesanan, reservasi, atau pembayaran.');
        }

        $pengguna->delete();

        return redirect()->route('pengguna.index')->with('success', 'Pengguna berhasil dihapus.');
    }
}