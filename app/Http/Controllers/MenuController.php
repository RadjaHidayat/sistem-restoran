<?php

namespace App\Http\Controllers;

use App\Models\DetailPesanan;
use App\Models\Kategori;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MenuController extends Controller
{
    public function index(Request $request)
    {
        $menu = Menu::with('kategori')
            ->orderBy('nama_menu')
            ->paginate($this->perHalaman($request))
            ->withQueryString();

        return view('pages.menu.index', [
            'title' => 'Menu Makanan',
            'menu' => $menu,
        ]);
    }

    public function create()
    {
        return view('pages.menu.create', [
            'title' => 'Tambah Menu',
            'kategori' => Kategori::orderBy('nama_kategori')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validasi($request);

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('menu', 'public');
        }

        Menu::create($data);

        return redirect()->route('menu.index')->with('success', 'Menu berhasil ditambahkan.');
    }

    public function edit(Menu $menu)
    {
        return view('pages.menu.edit', [
            'title' => 'Edit Menu',
            'menu' => $menu,
            'kategori' => Kategori::orderBy('nama_kategori')->get(),
        ]);
    }

    public function update(Request $request, Menu $menu)
    {
        $data = $this->validasi($request);

        if ($request->hasFile('foto')) {
            if ($menu->foto) {
                Storage::disk('public')->delete($menu->foto);
            }
            $data['foto'] = $request->file('foto')->store('menu', 'public');
        }

        $menu->update($data);

        return redirect()->route('menu.index')->with('success', 'Menu berhasil diubah.');
    }

    public function destroy(Menu $menu)
    {
        if (DetailPesanan::where('id_menu', $menu->id_menu)->exists()) {
            return redirect()->route('menu.index')->with('error', 'Menu tidak bisa dihapus karena sudah pernah dipesan. Ubah saja statusnya menjadi "Habis".');
        }

        if ($menu->foto) {
            Storage::disk('public')->delete($menu->foto);
        }

        $menu->delete();

        return redirect()->route('menu.index')->with('success', 'Menu berhasil dihapus.');
    }

    private function validasi(Request $request): array
    {
        $data = $request->validate([
            'id_kategori' => ['required', 'exists:kategori,id_kategori'],
            'nama_menu' => ['required', 'max:100'],
            'deskripsi' => ['nullable'],
            'harga' => ['required', 'numeric', 'min:0'],
            'foto' => ['nullable', 'image', 'max:2048'],
        ]);

        unset($data['foto']);
        $data['tersedia'] = $request->boolean('tersedia');

        return $data;
    }
}