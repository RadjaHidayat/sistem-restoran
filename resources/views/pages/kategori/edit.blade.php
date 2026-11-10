@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Edit Kategori" />

    <x-common.component-card title="Edit Kategori">
        <form method="POST" action="{{ route('kategori.update', $kategori) }}" class="space-y-5">
            @csrf
            @method('PUT')
            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Nama Kategori</label>
                <input type="text" name="nama_kategori" value="{{ old('nama_kategori', $kategori->nama_kategori) }}"
                    class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                @error('nama_kategori')
                    <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
                @enderror
            </div>
            <div class="flex gap-3">
                <button type="submit" class="h-11 rounded-lg bg-brand-500 px-5 text-sm font-medium text-white hover:bg-brand-600">
                    Simpan Perubahan
                </button>
                <a href="{{ route('kategori.index') }}"
                    class="flex h-11 items-center rounded-lg border border-gray-300 px-5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300">
                    Batal
                </a>
            </div>
        </form>
    </x-common.component-card>
@endsection