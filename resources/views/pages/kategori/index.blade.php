@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Kategori" />

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
        <x-common.component-card title="Tambah Kategori">
            <form method="POST" action="{{ route('kategori.store') }}" class="flex flex-col gap-3 sm:flex-row sm:items-start">
                @csrf
                <div class="flex-1">
                    <input type="text" name="nama_kategori" value="{{ old('nama_kategori') }}" placeholder="Contoh: Makanan Berat"
                        class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                    @error('nama_kategori')
                        <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
                    @enderror
                </div>
                <button type="submit" class="h-11 rounded-lg bg-brand-500 px-5 text-sm font-medium text-white hover:bg-brand-600">
                    Simpan
                </button>
            </form>
        </x-common.component-card>

        <x-common.component-card title="Daftar Kategori">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 text-gray-500 dark:border-gray-800 dark:text-gray-400">
                            <th class="py-3 pr-4 font-medium">No</th>
                            <th class="py-3 pr-4 font-medium">Nama Kategori</th>
                            <th class="py-3 pr-4 font-medium">Jumlah Menu</th>
                            <th class="py-3 font-medium">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($kategori as $item)
                            <tr class="border-b border-gray-100 text-gray-800 dark:border-gray-800 dark:text-white/90">
                                <td class="py-3 pr-4">{{ $kategori->firstItem() + $loop->index }}</td>
                                <td class="py-3 pr-4">{{ $item->nama_kategori }}</td>
                                <td class="py-3 pr-4">{{ $item->menu_count }}</td>
                                <td class="py-3">
                                    <div class="flex gap-2">
                                        <a href="{{ route('kategori.edit', $item) }}"
                                            class="rounded-lg border border-gray-300 px-3 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300">
                                            Edit
                                        </a>
                                        <form method="POST" action="{{ route('kategori.destroy', $item) }}"
                                            onsubmit="return confirm('Hapus kategori ini?')">
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
                                <td colspan="4" class="py-6 text-center text-gray-500">Belum ada kategori.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @include('pages._paginasi', ['data' => $kategori])
        </x-common.component-card>
    </div>
@endsection