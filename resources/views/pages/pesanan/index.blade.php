@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Pesanan" />

    @if (session('success'))
        <div class="mb-6 rounded-lg border border-success-500 bg-success-50 px-4 py-3 text-sm text-success-700">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="mb-6 rounded-lg border border-error-500 bg-error-50 px-4 py-3 text-sm text-error-700">
            {{ session('error') }}
        </div>
    @endif

    <x-common.component-card title="Daftar Pesanan">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex flex-wrap gap-2">
                @foreach (['' => 'Semua', 'baru' => 'Baru', 'dimasak' => 'Dimasak', 'siap' => 'Siap', 'selesai' => 'Selesai', 'dibatalkan' => 'Dibatalkan'] as $nilai => $label)
                    <a href="{{ route('pesanan.index', $nilai ? ['status' => $nilai] : []) }}"
                        class="rounded-lg px-3 py-1.5 text-xs font-medium {{ $statusDipilih == $nilai ? 'bg-brand-500 text-white' : 'border border-gray-300 text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>
            @if (auth()->user()->punyaRole('admin', 'kasir'))
                <a href="{{ route('pesanan.create') }}"
                    class="inline-flex h-11 items-center rounded-lg bg-brand-500 px-5 text-sm font-medium text-white hover:bg-brand-600">
                    + Buat Pesanan
                </a>
            @endif
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-gray-100 text-gray-500 dark:border-gray-800 dark:text-gray-400">
                        <th class="py-3 pr-4 font-medium">Kode</th>
                        <th class="py-3 pr-4 font-medium">Waktu</th>
                        <th class="py-3 pr-4 font-medium">Tipe</th>
                        <th class="py-3 pr-4 font-medium">Meja / Pelanggan</th>
                        <th class="py-3 pr-4 font-medium">Total</th>
                        <th class="py-3 pr-4 font-medium">Status</th>
                        <th class="py-3 font-medium">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pesanan as $item)
                        <tr class="border-b border-gray-100 text-gray-800 dark:border-gray-800 dark:text-white/90">
                            <td class="py-3 pr-4 font-mono text-xs">{{ $item->kode_pesanan }}</td>
                            <td class="py-3 pr-4">{{ $item->created_at->format('d/m/Y H:i') }}</td>
                            <td class="py-3 pr-4">{{ ucwords(str_replace('_', ' ', $item->tipe_pesanan)) }}</td>
                            <td class="py-3 pr-4">
                                {{ $item->meja ? 'Meja ' . $item->meja->nomor_meja : '-' }}
                                @if ($item->user)
                                    <div class="text-xs text-gray-500">{{ $item->user->nama }}</div>
                                @endif
                            </td>
                            <td class="py-3 pr-4">Rp {{ number_format($item->total_harga, 0, ',', '.') }}</td>
                            <td class="py-3 pr-4">@include('pages.pesanan._status', ['status' => $item->status])</td>
                            <td class="py-3">
                                <a href="{{ route('pesanan.show', $item) }}"
                                    class="rounded-lg border border-gray-300 px-3 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300">
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-6 text-center text-gray-500">Belum ada pesanan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @include('pages._paginasi', ['data' => $pesanan])
    </x-common.component-card>
@endsection