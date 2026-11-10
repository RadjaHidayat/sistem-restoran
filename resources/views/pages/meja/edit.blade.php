@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Edit Meja" />

    <x-common.component-card title="Edit Meja {{ $meja->nomor_meja }}">
        <form method="POST" action="{{ route('meja.update', $meja) }}" class="space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Nomor Meja</label>
                <input type="text" name="nomor_meja" value="{{ old('nomor_meja', $meja->nomor_meja) }}" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                @error('nomor_meja')
                    <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Kapasitas (orang)</label>
                <input type="number" name="kapasitas" min="1" value="{{ old('kapasitas', $meja->kapasitas) }}" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                @error('kapasitas')
                    <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Status</label>
                <select name="status" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                    @foreach (['kosong' => 'Kosong', 'terisi' => 'Terisi', 'dipesan' => 'Dipesan'] as $nilai => $label)
                        <option value="{{ $nilai }}" @selected(old('status', $meja->status) == $nilai)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('status')
                    <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Kode QR</label>
                <p class="font-mono text-sm text-gray-800 dark:text-white/90">{{ $meja->kode_qr }}</p>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Dibuat otomatis, tidak bisa diubah.</p>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="h-11 rounded-lg bg-brand-500 px-5 text-sm font-medium text-white hover:bg-brand-600">
                    Simpan Perubahan
                </button>
                <a href="{{ route('meja.index') }}"
                    class="flex h-11 items-center rounded-lg border border-gray-300 px-5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300">
                    Batal
                </a>
            </div>
        </form>
    </x-common.component-card>
@endsection