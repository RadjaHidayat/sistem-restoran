@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Pengguna" />

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

    <x-common.component-card title="Daftar Pengguna">
        <div>
            <a href="{{ route('pengguna.create') }}"
                class="inline-flex h-11 items-center rounded-lg bg-brand-500 px-5 text-sm font-medium text-white hover:bg-brand-600">
                + Tambah Pengguna
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-gray-100 text-gray-500 dark:border-gray-800 dark:text-gray-400">
                        <th class="py-3 pr-4 font-medium">No</th>
                        <th class="py-3 pr-4 font-medium">Nama</th>
                        <th class="py-3 pr-4 font-medium">Email</th>
                        <th class="py-3 pr-4 font-medium">No. HP</th>
                        <th class="py-3 pr-4 font-medium">Role</th>
                        <th class="py-3 font-medium">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pengguna as $item)
                        @php
                            $warnaRole = match ($item->role->nama_role) {
                                'admin' => 'bg-brand-50 text-brand-600',
                                'kasir' => 'bg-success-50 text-success-700',
                                'koki' => 'bg-warning-50 text-warning-700',
                                default => 'bg-gray-100 text-gray-700',
                            };
                        @endphp
                        <tr class="border-b border-gray-100 text-gray-800 dark:border-gray-800 dark:text-white/90">
                            <td class="py-3 pr-4">{{ $pengguna->firstItem() + $loop->index }}</td>
                            <td class="py-3 pr-4 font-medium">
                                {{ $item->nama }}
                                @if ($item->id_user == auth()->id())
                                    <span class="text-xs text-gray-500">(kamu)</span>
                                @endif
                            </td>
                            <td class="py-3 pr-4">{{ $item->email }}</td>
                            <td class="py-3 pr-4">{{ $item->no_hp ?? '-' }}</td>
                            <td class="py-3 pr-4">
                                <span class="rounded-full px-2.5 py-0.5 text-xs font-medium capitalize {{ $warnaRole }}">
                                    {{ $item->role->nama_role }}
                                </span>
                            </td>
                            <td class="py-3">
                                <div class="flex gap-2">
                                    <a href="{{ route('pengguna.edit', $item) }}"
                                        class="rounded-lg border border-gray-300 px-3 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300">
                                        Edit
                                    </a>
                                    @if ($item->id_user != auth()->id())
                                        <form method="POST" action="{{ route('pengguna.destroy', $item) }}"
                                            onsubmit="return confirm('Hapus pengguna ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="rounded-lg bg-error-500 px-3 py-1.5 text-xs font-medium text-white hover:bg-error-600">
                                                Hapus
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-6 text-center text-gray-500">Belum ada pengguna.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @include('pages._paginasi', ['data' => $pengguna])
    </x-common.component-card>
@endsection