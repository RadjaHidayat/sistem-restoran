@extends('layouts.app')

@section('content')
    @php
        $bolehLihatUang = auth()->user()->punyaRole('admin', 'kasir');
    @endphp

    <div class="mb-6">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90">Halo, {{ auth()->user()->nama }}!</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400">
            {{ now()->format('d/m/Y') }} &middot; Login sebagai <span class="capitalize">{{ auth()->user()->role->nama_role }}</span>
        </p>
    </div>

    <!-- Kartu angka ringkasan -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @if ($bolehLihatUang)
            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
                <p class="text-sm text-gray-500 dark:text-gray-400">Pendapatan Hari Ini</p>
                <p class="mt-2 text-2xl font-semibold text-gray-800 dark:text-white/90">Rp {{ number_format($pendapatanHariIni, 0, ',', '.') }}</p>
                <p class="mt-1 text-xs text-gray-500">{{ $transaksiHariIni }} transaksi lunas</p>
            </div>
        @endif

        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
            <p class="text-sm text-gray-500 dark:text-gray-400">Pesanan Hari Ini</p>
            <p class="mt-2 text-2xl font-semibold text-gray-800 dark:text-white/90">{{ $pesananHariIni }}</p>
            <p class="mt-1 text-xs text-gray-500">tidak termasuk yang dibatalkan</p>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
            <p class="text-sm text-gray-500 dark:text-gray-400">Pesanan Aktif</p>
            <p class="mt-2 text-2xl font-semibold text-warning-600">{{ $pesananAktif->count() }}</p>
            <p class="mt-1 text-xs text-gray-500">baru, dimasak, atau siap</p>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
            <p class="text-sm text-gray-500 dark:text-gray-400">Meja Terisi</p>
            <p class="mt-2 text-2xl font-semibold text-gray-800 dark:text-white/90">{{ $mejaTerisi }} / {{ $totalMeja }}</p>
            <p class="mt-1 text-xs text-gray-500">{{ $reservasiHariIni->count() }} reservasi hari ini</p>
        </div>
    </div>

    <div class="mt-6 grid grid-cols-1 gap-6 xl:grid-cols-2">
        <!-- Pesanan aktif -->
        <x-common.component-card title="Pesanan Aktif" desc="Urut dari yang paling lama menunggu">
            <div class="space-y-3">
                @forelse ($pesananAktif->take(8) as $p)
                    <a href="{{ route('pesanan.show', $p) }}"
                        class="flex items-center justify-between rounded-lg border border-gray-100 px-4 py-3 hover:bg-gray-50 dark:border-gray-800 dark:hover:bg-white/[0.03]">
                        <div>
                            <p class="font-mono text-xs text-gray-500">{{ $p->kode_pesanan }}</p>
                            <p class="text-sm font-medium text-gray-800 dark:text-white/90">
                                {{ $p->meja ? 'Meja ' . $p->meja->nomor_meja : ucwords(str_replace('_', ' ', $p->tipe_pesanan)) }}
                                <span class="text-xs font-normal text-gray-500">&middot; pukul {{ $p->created_at->format('H:i') }}</span>
                            </p>
                        </div>
                        @include('pages.pesanan._status', ['status' => $p->status])
                    </a>
                @empty
                    <p class="py-4 text-center text-sm text-gray-500">Tidak ada pesanan yang sedang berjalan.</p>
                @endforelse
            </div>
        </x-common.component-card>

        @if ($bolehLihatUang)
            <!-- Grafik pendapatan 7 hari -->
            <x-common.component-card title="Pendapatan 7 Hari Terakhir">
                <div class="flex h-56 items-end gap-3">
                    @foreach ($grafik as $g)
                        <div class="flex h-full flex-1 flex-col items-center justify-end gap-2">
                            <span class="text-[10px] text-gray-500">
                                {{ $g['total'] > 0 ? number_format($g['total'] / 1000, 0, ',', '.') . 'rb' : '' }}
                            </span>
                            <div class="w-full rounded-t-md bg-brand-500"
                                style="height: {{ max(2, round($g['total'] / $grafikMaks * 100)) }}%"></div>
                            <span class="text-[10px] text-gray-500">{{ $g['label'] }}</span>
                        </div>
                    @endforeach
                </div>
            </x-common.component-card>
        @endif

        <!-- Menu terlaris -->
        <x-common.component-card title="Menu Terlaris" desc="7 hari terakhir">
            <div class="space-y-3">
                @forelse ($menuTerlaris as $m)
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-800 dark:text-white/90">{{ $loop->iteration }}. {{ $m->menu->nama_menu }}</span>
                        <span class="font-medium text-gray-800 dark:text-white/90">{{ $m->total_terjual }} porsi</span>
                    </div>
                @empty
                    <p class="py-4 text-center text-sm text-gray-500">Belum ada data penjualan.</p>
                @endforelse
            </div>
        </x-common.component-card>

        <!-- Reservasi hari ini -->
        <x-common.component-card title="Reservasi Hari Ini">
            <div class="space-y-3">
                @forelse ($reservasiHariIni as $r)
                    <div class="flex items-center justify-between text-sm">
                        <div>
                            <span class="font-medium text-gray-800 dark:text-white/90">{{ substr($r->jam, 0, 5) }}</span>
                            <span class="text-gray-700 dark:text-gray-300">&middot; {{ $r->user->nama }} ({{ $r->jumlah_orang }} orang)</span>
                        </div>
                        <span class="text-gray-500">{{ $r->meja ? 'Meja ' . $r->meja->nomor_meja : 'Meja belum dipilih' }}</span>
                    </div>
                @empty
                    <p class="py-4 text-center text-sm text-gray-500">Tidak ada reservasi hari ini.</p>
                @endforelse
            </div>
        </x-common.component-card>
    </div>
@endsection