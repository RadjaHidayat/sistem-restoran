@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Meja" />

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

    <div class="space-y-6">
        <x-common.component-card title="Tambah Meja">
            <form method="POST" action="{{ route('meja.store') }}" class="flex flex-col gap-3 sm:flex-row sm:items-start">
                @csrf
                <div class="flex-1">
                    <input type="text" name="nomor_meja" value="{{ old('nomor_meja') }}" placeholder="Nomor meja, contoh: A1"
                        class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                    @error('nomor_meja')
                        <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
                    @enderror
                </div>
                <div class="sm:w-48">
                    <input type="number" name="kapasitas" min="1" value="{{ old('kapasitas') }}" placeholder="Kapasitas (orang)"
                        class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                    @error('kapasitas')
                        <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
                    @enderror
                </div>
                <button type="submit" class="h-11 rounded-lg bg-brand-500 px-5 text-sm font-medium text-white hover:bg-brand-600">
                    Simpan
                </button>
            </form>
        </x-common.component-card>

        <x-common.component-card title="Daftar Meja">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 text-gray-500 dark:border-gray-800 dark:text-gray-400">
                            <th class="py-3 pr-4 font-medium">No</th>
                            <th class="py-3 pr-4 font-medium">Nomor Meja</th>
                            <th class="py-3 pr-4 font-medium">Kapasitas</th>
                            <th class="py-3 pr-4 font-medium">Kode QR</th>
                            <th class="py-3 pr-4 font-medium">Status</th>
                            <th class="py-3 font-medium">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($meja as $item)
                            <tr class="border-b border-gray-100 text-gray-800 dark:border-gray-800 dark:text-white/90">
                                <td class="py-3 pr-4">{{ $meja->firstItem() + $loop->index }}</td>
                                <td class="py-3 pr-4 font-medium">{{ $item->nomor_meja }}</td>
                                <td class="py-3 pr-4">{{ $item->kapasitas }} orang</td>
                                <td class="py-3 pr-4 font-mono text-xs">{{ $item->kode_qr }}</td>
                                <td class="py-3 pr-4">
                                    @if ($item->status == 'kosong')
                                        <span class="rounded-full bg-success-50 px-2.5 py-0.5 text-xs font-medium text-success-700">Kosong</span>
                                    @elseif ($item->status == 'terisi')
                                        <span class="rounded-full bg-error-50 px-2.5 py-0.5 text-xs font-medium text-error-700">Terisi</span>
                                    @else
                                        <span class="rounded-full bg-warning-50 px-2.5 py-0.5 text-xs font-medium text-warning-700">Dipesan</span>
                                    @endif
                                </td>
                                <td class="py-3">
                                    <div class="flex gap-2">
                                        <a href="{{ route('meja.qr', $item) }}" target="_blank"
                                            class="rounded-lg border border-brand-300 px-3 py-1.5 text-xs font-medium text-brand-600 hover:bg-brand-50">
                                            QR
                                        </a>
                                        <a href="{{ route('meja.edit', $item) }}"
                                            class="rounded-lg border border-gray-300 px-3 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300">
                                            Edit
                                        </a>
                                        <form method="POST" action="{{ route('meja.destroy', $item) }}"
                                            onsubmit="return confirm('Hapus meja ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="rounded-lg bg-error-500 px-3 py-1.5 text-xs font-medium text-white hover:bg-error-600">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-6 text-center text-gray-500">Belum ada meja.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @include('pages._paginasi', ['data' => $meja])
        </x-common.component-card>
    </div>
@endsection