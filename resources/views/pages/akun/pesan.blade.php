@extends('layouts.pelanggan')

{{-- Halaman ini punya bar keranjang sendiri di bawah, jadi navigasi bawah disembunyikan --}}
@section('tanpa-nav-bawah', true)

@section('isi')
    <div class="flex flex-col gap-1">
        <a href="{{ route('akun.index') }}" class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400">&larr; Kembali ke Beranda</a>
        <h1 class="text-2xl font-semibold text-gray-800 dark:text-white/90">Pesan Antar / Ambil</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400">Pilih cara menerima pesanan, lalu pilih menunya.</p>
    </div>

    @if (session('error'))
        <div class="mt-4 rounded-xl border border-error-500 bg-error-50 px-4 py-3 text-sm text-error-700">{{ session('error') }}</div>
    @endif

    <x-katalog-menu :menu-per-kategori="$menuPerKategori" :action="route('akun.pesan.simpan')"
        atas="top-16" grid="sm:grid-cols-2 xl:grid-cols-3" lebar="max-w-6xl"
        keterangan-bayar="Ambil sendiri: bayar di kasir saat mengambil. Diantar: bayar tunai/QRIS saat pesanan sampai.">

        <!-- Cara terima pesanan -->
        <div class="mt-5 rounded-2xl border border-gray-200 bg-white p-4 sm:p-5 dark:border-gray-800 dark:bg-white/[0.03]"
            x-data="{ tipe: '{{ old('tipe_pesanan', 'takeaway') }}' }">
            <h2 class="text-sm font-semibold text-gray-800 dark:text-white/90">Cara terima pesanan</h2>

            <div class="mt-3 grid grid-cols-1 gap-3 sm:grid-cols-2">
                <label class="flex cursor-pointer items-center gap-4 rounded-2xl border-2 p-4 transition"
                    :class="tipe === 'takeaway' ? 'border-brand-500 bg-brand-50 dark:bg-brand-500/10' : 'border-gray-200 hover:border-gray-300 dark:border-gray-800'">
                    <input type="radio" name="tipe_pesanan" value="takeaway" x-model="tipe" class="sr-only">
                    <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-white text-2xl shadow-theme-xs dark:bg-gray-900">🥡</span>
                    <span class="flex-1">
                        <span class="block font-semibold text-gray-800 dark:text-white/90">Ambil Sendiri</span>
                        <span class="block text-xs text-gray-500 dark:text-gray-400">Ambil di restoran, bayar di kasir</span>
                    </span>
                    <span class="flex h-5 w-5 items-center justify-center rounded-full border-2" :class="tipe === 'takeaway' ? 'border-brand-500' : 'border-gray-300 dark:border-gray-700'">
                        <span class="h-2.5 w-2.5 rounded-full bg-brand-500" x-show="tipe === 'takeaway'"></span>
                    </span>
                </label>

                <label class="flex cursor-pointer items-center gap-4 rounded-2xl border-2 p-4 transition"
                    :class="tipe === 'delivery' ? 'border-brand-500 bg-brand-50 dark:bg-brand-500/10' : 'border-gray-200 hover:border-gray-300 dark:border-gray-800'">
                    <input type="radio" name="tipe_pesanan" value="delivery" x-model="tipe" class="sr-only">
                    <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-white text-2xl shadow-theme-xs dark:bg-gray-900">🛵</span>
                    <span class="flex-1">
                        <span class="block font-semibold text-gray-800 dark:text-white/90">Diantar</span>
                        <span class="block text-xs text-gray-500 dark:text-gray-400">Diantar ke alamatmu, bayar saat sampai</span>
                    </span>
                    <span class="flex h-5 w-5 items-center justify-center rounded-full border-2" :class="tipe === 'delivery' ? 'border-brand-500' : 'border-gray-300 dark:border-gray-700'">
                        <span class="h-2.5 w-2.5 rounded-full bg-brand-500" x-show="tipe === 'delivery'"></span>
                    </span>
                </label>
            </div>

            <div x-show="tipe === 'delivery'" x-transition class="mt-4">
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Alamat antar</label>
                <textarea name="alamat_antar" rows="2" placeholder="Nama jalan, nomor rumah, patokan"
                    class="w-full rounded-xl border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:outline-hidden dark:border-gray-700 dark:text-white/90">{{ old('alamat_antar', auth()->user()->alamat) }}</textarea>
                @error('alamat_antar')
                    <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-gray-400">Diisi otomatis dari alamat di Profil. Boleh diubah untuk pesanan ini saja.</p>
            </div>
        </div>
    </x-katalog-menu>
@endsection