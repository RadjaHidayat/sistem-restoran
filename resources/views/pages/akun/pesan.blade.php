@extends('layouts.fullscreen-layout')

@section('content')
    @php
        // Jumlah awal tiap menu (0, atau isian sebelumnya kalau ada error)
        $jumlahAwal = $hargaMenu->map(fn ($harga, $id) => (int) old('jumlah.' . $id, 0));
    @endphp

    <div class="mx-auto min-h-screen max-w-2xl bg-white pb-32 dark:bg-gray-900">
        <div class="border-b border-gray-200 px-5 py-6 dark:border-gray-800">
            <a href="{{ route('akun.index') }}" class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400">&larr; Kembali ke Akun Saya</a>
            <h1 class="mt-2 text-2xl font-semibold text-gray-800 dark:text-white/90">Pesan Antar / Ambil</h1>
        </div>

        @if (session('error'))
            <div class="mx-5 mt-5 rounded-lg border border-error-500 bg-error-50 px-4 py-3 text-sm text-error-700">
                {{ session('error') }}
            </div>
        @endif

        <form method="POST" action="{{ route('akun.pesan.simpan') }}"
            x-data="{
                tipe: '{{ old('tipe_pesanan', 'takeaway') }}',
                qty: @js($jumlahAwal),
                harga: @js($hargaMenu),
                get total() {
                    return Object.keys(this.qty).reduce((t, id) => t + this.harga[id] * (this.qty[id] || 0), 0);
                },
                get jumlahItem() {
                    return Object.values(this.qty).reduce((t, q) => t + (q || 0), 0);
                },
                rupiah(n) {
                    return 'Rp ' + n.toLocaleString('id-ID');
                }
            }">
            @csrf

            <!-- Pilih cara terima pesanan -->
            <div class="px-5 pt-6">
                <h2 class="mb-3 text-sm font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Cara Terima Pesanan</h2>
                <div class="grid grid-cols-2 gap-3">
                    <label class="cursor-pointer rounded-2xl border-2 p-4"
                        :class="tipe === 'takeaway' ? 'border-brand-500 bg-brand-50 dark:bg-brand-500/10' : 'border-gray-200 dark:border-gray-800'">
                        <input type="radio" name="tipe_pesanan" value="takeaway" x-model="tipe" class="sr-only">
                        <p class="font-medium text-gray-800 dark:text-white/90">Ambil Sendiri</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Bayar saat mengambil di kasir</p>
                    </label>
                    <label class="cursor-pointer rounded-2xl border-2 p-4"
                        :class="tipe === 'delivery' ? 'border-brand-500 bg-brand-50 dark:bg-brand-500/10' : 'border-gray-200 dark:border-gray-800'">
                        <input type="radio" name="tipe_pesanan" value="delivery" x-model="tipe" class="sr-only">
                        <p class="font-medium text-gray-800 dark:text-white/90">Diantar</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Bayar saat pesanan sampai</p>
                    </label>
                </div>

                <div x-show="tipe === 'delivery'" class="mt-4">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Alamat Antar</label>
                    <textarea name="alamat_antar" rows="2" placeholder="Nama jalan, nomor rumah, patokan"
                        class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:outline-hidden dark:border-gray-700 dark:text-white/90">{{ old('alamat_antar', auth()->user()->alamat) }}</textarea>
                    @error('alamat_antar')
                        <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            @forelse ($menuPerKategori as $namaKategori => $daftarMenu)
                <div class="px-5 pt-6">
                    <h2 class="mb-3 text-sm font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ $namaKategori }}</h2>

                    <div class="space-y-3">
                        @foreach ($daftarMenu as $item)
                            <div class="flex gap-4 rounded-2xl border border-gray-200 p-3 dark:border-gray-800">
                                @if ($item->foto)
                                    <img src="{{ asset('storage/' . $item->foto) }}" alt="{{ $item->nama_menu }}"
                                        class="h-20 w-20 shrink-0 rounded-xl object-cover">
                                @else
                                    <div class="flex h-20 w-20 shrink-0 items-center justify-center rounded-xl bg-gray-100 text-xs text-gray-400 dark:bg-gray-800">
                                        Tanpa foto
                                    </div>
                                @endif

                                <div class="flex flex-1 flex-col">
                                    <p class="font-medium text-gray-800 dark:text-white/90">{{ $item->nama_menu }}</p>
                                    @if ($item->deskripsi)
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $item->deskripsi }}</p>
                                    @endif

                                    <div class="mt-auto flex items-center justify-between pt-2">
                                        <span class="text-sm font-semibold text-gray-800 dark:text-white/90">
                                            Rp {{ number_format($item->harga, 0, ',', '.') }}
                                        </span>

                                        <div class="flex items-center gap-2">
                                            <button type="button"
                                                @click="qty[{{ $item->id_menu }}] = Math.max(0, (qty[{{ $item->id_menu }}] || 0) - 1)"
                                                class="flex h-8 w-8 items-center justify-center rounded-full border border-gray-300 text-gray-700 dark:border-gray-700 dark:text-gray-300">
                                                &minus;
                                            </button>
                                            <input type="number" min="0" max="50" name="jumlah[{{ $item->id_menu }}]"
                                                x-model.number="qty[{{ $item->id_menu }}]"
                                                class="h-8 w-12 rounded-lg border border-gray-300 bg-transparent text-center text-sm text-gray-800 dark:border-gray-700 dark:text-white/90">
                                            <button type="button"
                                                @click="qty[{{ $item->id_menu }}] = (qty[{{ $item->id_menu }}] || 0) + 1"
                                                class="flex h-8 w-8 items-center justify-center rounded-full bg-brand-500 text-white">
                                                +
                                            </button>
                                        </div>
                                    </div>

                                    <input type="text" name="catatan[{{ $item->id_menu }}]" x-show="qty[{{ $item->id_menu }}] > 0"
                                        value="{{ old('catatan.' . $item->id_menu) }}" placeholder="Catatan, contoh: tidak pedas"
                                        class="mt-2 h-9 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm text-gray-800 dark:border-gray-700 dark:text-white/90">
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @empty
                <p class="px-5 py-10 text-center text-gray-500">Maaf, belum ada menu yang tersedia.</p>
            @endforelse

            <!-- Bar total di bagian bawah layar -->
            <div class="fixed inset-x-0 bottom-0 border-t border-gray-200 bg-white px-5 py-4 dark:border-gray-800 dark:bg-gray-900">
                <div class="mx-auto flex max-w-2xl items-center justify-between gap-4">
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400"><span x-text="jumlahItem"></span> item</p>
                        <p class="text-lg font-semibold text-gray-800 dark:text-white/90" x-text="rupiah(total)"></p>
                    </div>
                    <button type="submit" :disabled="total === 0"
                        class="h-11 rounded-lg bg-brand-500 px-6 text-sm font-medium text-white hover:bg-brand-600 disabled:opacity-50">
                        Pesan Sekarang
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection