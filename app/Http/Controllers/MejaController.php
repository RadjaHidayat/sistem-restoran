<?php

namespace App\Http\Controllers;

use App\Models\Meja;
use App\Models\Pesanan;
use App\Models\Reservasi;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class MejaController extends Controller
{
    public function index(Request $request)
    {
        $meja = Meja::orderBy('nomor_meja')
            ->paginate($this->perHalaman($request))
            ->withQueryString();

        return view('pages.meja.index', [
            'title' => 'Meja',
            'meja' => $meja,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nomor_meja' => ['required', 'max:10', 'unique:meja,nomor_meja'],
            'kapasitas' => ['required', 'integer', 'min:1'],
        ]);

        $data['kode_qr'] = 'MEJA-' . Str::upper(Str::random(10));
        $data['status'] = 'kosong';

        Meja::create($data);

        return redirect()->route('meja.index')->with('success', 'Meja berhasil ditambahkan.');
    }

    public function edit(Meja $meja)
    {
        return view('pages.meja.edit', [
            'title' => 'Edit Meja',
            'meja' => $meja,
        ]);
    }

    public function update(Request $request, Meja $meja)
    {
        $data = $request->validate([
            'nomor_meja' => ['required', 'max:10', 'unique:meja,nomor_meja,' . $meja->id_meja . ',id_meja'],
            'kapasitas' => ['required', 'integer', 'min:1'],
            'status' => ['required', 'in:kosong,terisi,dipesan'],
        ]);

        $meja->update($data);

        return redirect()->route('meja.index')->with('success', 'Meja berhasil diubah.');
    }

    public function destroy(Meja $meja)
    {
        $dipakai = Pesanan::where('id_meja', $meja->id_meja)->exists()
            || Reservasi::where('id_meja', $meja->id_meja)->exists();

        if ($dipakai) {
            return redirect()->route('meja.index')->with('error', 'Meja tidak bisa dihapus karena sudah punya riwayat pesanan atau reservasi.');
        }

        $meja->delete();

        return redirect()->route('meja.index')->with('success', 'Meja berhasil dihapus.');
    }
    public function qr(Meja $meja)
    {
        // Alamat yang dibuka saat QR di-scan
        $url = route('pesan.menu', $meja->kode_qr);

        // Buat gambar QR dalam format SVG (tidak butuh ekstensi PHP tambahan)
        $renderer = new ImageRenderer(new RendererStyle(280, 1), new SvgImageBackEnd());
        $svg = (new Writer($renderer))->writeString($url);
        $svg = preg_replace('/<\?xml.*?\?>/', '', $svg); // buang baris pembuka XML

        return view('pages.meja.qr', [
            'title' => 'QR Meja ' . $meja->nomor_meja,
            'meja' => $meja,
            'url' => $url,
            'svg' => $svg,
        ]);
    }
}