@extends('layouts.fullscreen-layout')

@section('content')
    @php
        $labelStatus = ['baru' => 'Diterima', 'dimasak' => 'Sedang dimasak', 'siap' => 'Siap disajikan', 'selesai' => 'Selesai', 'dibatalkan' => 'Dibatalkan'];
    @endphp

    <div class="min-h-screen bg-gray-50 dark:bg-gray-950">
        <div class="mx-auto max-w-2xl px-4 pb-32">
            <!-- Kepala halaman -->
            <header class="relative -mx-4 overflow-hidden bg-brand-500 px-5 pt-8 pb-16 text-white sm:mx-0 sm:mt-4 sm:rounded-3xl">
                <div class="absolute -top-12 -right-12 h-44 w-44 rounded-full bg-white/10"></div>
                <div class="absolute right-20 -bottom-16 h-32 w-32 rounded-full bg-white/10"></div>
                <div class="relative">
                    <p class="text-sm font-medium text-white/80">{{ config('app.name') }}</p>
                    <h1 class="mt-1 text-2xl font-semibold">Selamat datang! 👋</h1>
                    <p class="mt-1 text-sm text-white/80">Pilih menu, pesan, dan makanan diantar ke mejamu.</p>
                </div>
            </header>

            <!-- Kartu meja (menumpuk ke atas header) -->
            <div class="relative -mt-10 flex items-center gap-4 rounded-2xl border border-gray-200 bg-white p-4 shadow-theme-md dark:border-gray-800 dark:bg-gray-900">
                <span class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-brand-50 text-lg font-bold text-brand-600 dark:bg-brand-500/15 dark:text-brand-400">
                    {{ $meja->nomor_meja }}
                </span>
                <div class="flex-1">
                    <p class="text-xs text-gray-500 dark:text-gray-400">Kamu memesan untuk</p>
                    <p class="font-semibold text-gray-800 dark:text-white/90">Meja {{ $meja->nomor_meja }} · {{ $meja->kapasitas }} kursi</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Bayar di kasir setelah selesai makan</p>
                </div>
            </div>

            <!-- Pesanan yang sudah dikirim dari HP ini (hari ini) -->
            @if ($pesananSaya->isNotEmpty())
                <div class="mt-4 rounded-2xl border border-brand-200 bg-brand-50 p-4 dark:border-brand-500/30 dark:bg-brand-500/10">
                    <p class="text-sm font-semibold text-gray-800 dark:text-white/90">Pesananmu</p>
                    <div class="mt-2 space-y-2">
                        @foreach ($pesananSaya as $p)
                            <a href="{{ route('pesan.status', [$meja->kode_qr, $p->kode_pesanan]) }}"
                                class="flex items-center justify-between rounded-xl bg-white px-3 py-2.5 text-sm hover:bg-gray-50 dark:bg-gray-900 dark:hover:bg-white/5">
                                <span>
                                    <span class="font-mono text-xs text-gray-500">{{ $p->kode_pesanan }}</span>
                                    <span class="block font-medium text-gray-800 dark:text-white/90">{{ $labelStatus[$p->status] }}</span>
                                </span>
                                <span class="text-xs font-medium text-brand-600 dark:text-brand-400">Lihat status &rarr;</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="mt-4 rounded-xl border border-error-500 bg-error-50 px-4 py-3 text-sm text-error-700">
                    {{ session('error') }}
                </div>
            @endif

            <div class="mt-2">
                <x-katalog-menu :menu-per-kategori="$menuPerKategori" :action="route('pesan.store', $meja->kode_qr)"
                    judul-tombol="Kirim Pesanan ke Dapur"
                    keterangan-bayar="Pesanan langsung diteruskan ke dapur. Pembayaran di kasir setelah selesai makan." />
            </div>
        </div>
    </div>
@endsection