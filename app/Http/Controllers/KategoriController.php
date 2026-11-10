<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use Illuminate\Http\Request;

class KategoriController extends Controller
{
    public function index(Request $request)
    {
        $kategori = Kategori::withCount('menu')
            ->orderBy('nama_kategori')
            ->paginate($this->perHalaman($request))
            ->withQueryString();

        return view('pages.kategori.index', [
            'title' => 'Kategori',
            'kategori' => $kategori,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_kategori' => ['required', 'max:50', 'unique:kategori,nama_kategori'],
        ]);

        Kategori::create($data);

        return redirect()->route('kategori.index')->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function edit(Kategori $kategori)
    {
        return view('pages.kategori.edit', [
            'title' => 'Edit Kategori',
            'kategori' => $kategori,
        ]);
    }

    public function update(Request $request, Kategori $kategori)
    {
        $data = $request->validate([
            'nama_kategori' => ['required', 'max:50', 'unique:kategori,nama_kategori,' . $kategori->id_kategori . ',id_kategori'],
        ]);

        $kategori->update($data);

        return redirect()->route('kategori.index')->with('success', 'Kategori berhasil diubah.');
    }

    public function destroy(Kategori $kategori)
    {
        if ($kategori->menu()->exists()) {
            return redirect()->route('kategori.index')->with('error', 'Kategori tidak bisa dihapus karena masih dipakai oleh menu.');
        }

        $kategori->delete();

        return redirect()->route('kategori.index')->with('success', 'Kategori berhasil dihapus.');
    }
}