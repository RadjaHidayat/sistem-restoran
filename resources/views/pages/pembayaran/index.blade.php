@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Pembayaran" />

    <div class="space-y-6">
        <form method="GET" action="{{ route('pembayaran.index') }}" class="flex items-center gap-3">
            <label class="text-sm text-gray-700 dark:text-gray-400">Tanggal:</label>
            <input type="date" name="tanggal" value="{{ $tanggal }}" onchange="this.form.submit()"
                class="h-10 rounded-lg border border-gray-300 bg-transparent px-3 text-sm text-gray-800 dark:border-gray-700 dark:text-white/90">
        </form>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
                <p class="text-sm text-gray-500 dark:text-gray-400">Total Pendapatan</p>
                <p class="mt-2 text-xl font-semibold text-gray-800 dark:text-white/90">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</p>
                <p class="mt-1 text-xs text-gray-500">{{ $jumlahTransaksi }} transaksi</p>
            </div>
            @foreach ($perMetode as $metode => $jumlah)
                <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
                    <p class="text-sm uppercase text-gray-500 dark:text-gray-400">{{ $metode }}</p>
                    <p class="mt-2 text-xl font-semibold text-gray-800 dark:text-white/90">Rp {{ number_format($jumlah, 0, ',', '.') }}</p>
                </div>
            @endforeach
        </div>

        <x-common.component-card title="Belum Dibayar" desc="Semua pesanan yang belum dibayar, dari tanggal berapa pun.">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 text-gray-500 dark:border-gray-800 dark:text-gray-400">
                            <th class="py-3 pr-4 font-medium">Kode</th>
                            <th class="py-3 pr-4 font-medium">Waktu Pesan</th>
                            <th class="py-3 pr-4 font-medium">Meja</th>
                            <th class="py-3 pr-4 font-medium">Total</th>
                            <th class="py-3 font-medium">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($belumBayar as $item)
                            <tr class="border-b border-gray-100 text-gray-800 dark:border-gray-800 dark:text-white/90">
                                <td class="py-3 pr-4 font-mono text-xs">{{ $item->kode_pesanan }}</td>
                                <td class="py-3 pr-4">{{ $item->created_at->format('d/m/Y H:i') }}</td>
                                <td class="py-3 pr-4">{{ $item->meja->nomor_meja ?? '-' }}</td>
                                <td class="py-3 pr-4">Rp {{ number_format($item->total_harga, 0, ',', '.') }}</td>
                                <td class="py-3">
                                    <a href="{{ route('pesanan.show', $item) }}"
                                        class="rounded-lg bg-success-500 px-3 py-1.5 text-xs font-medium text-white hover:bg-success-600">
                                        Bayar
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-6 text-center text-gray-500">Semua pesanan sudah dibayar.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-common.component-card>

        <x-common.component-card title="Riwayat Pembayaran">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 text-gray-500 dark:border-gray-800 dark:text-gray-400">
                            <th class="py-3 pr-4 font-medium">Waktu</th>
                            <th class="py-3 pr-4 font-medium">Kode Pesanan</th>
                            <th class="py-3 pr-4 font-medium">Metode</th>
                            <th class="py-3 pr-4 font-medium">Total</th>
                            <th class="py-3 pr-4 font-medium">Kasir</th>
                            <th class="py-3 font-medium">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pembayaran as $item)
                            <tr class="border-b border-gray-100 text-gray-800 dark:border-gray-800 dark:text-white/90">
                                <td class="py-3 pr-4">{{ $item->waktu_bayar->format('H:i') }}</td>
                                <td class="py-3 pr-4 font-mono text-xs">{{ $item->pesanan->kode_pesanan }}</td>
                                <td class="py-3 pr-4 uppercase">{{ $item->metode }}</td>
                                <td class="py-3 pr-4">Rp {{ number_format($item->pesanan->total_harga, 0, ',', '.') }}</td>
                                <td class="py-3 pr-4">{{ $item->kasir->nama ?? '-' }}</td>
                                <td class="py-3">
                                    <a href="{{ route('pesanan.show', $item->pesanan) }}"
                                        class="rounded-lg border border-gray-300 px-3 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300">
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-6 text-center text-gray-500">Belum ada pembayaran pada tanggal ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @include('pages._paginasi', ['data' => $pembayaran])
        </x-common.component-card>
    </div>
@endsection