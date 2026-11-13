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
        <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                @include('pages._cari', ['placeholder' => 'Cari nama, email, no. HP...'])

                <form method="GET">
                    @foreach (request()->except(['role', 'page']) as $nama => $nilai)
                        @if (is_string($nilai))
                            <input type="hidden" name="{{ $nama }}" value="{{ $nilai }}">
                        @endif
                    @endforeach
                    <select name="role" onchange="this.form.submit()"
                        class="h-10 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm text-gray-800 capitalize sm:w-40 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                        <option value="">Semua role</option>
                        @foreach ($roles as $r)
                            <option value="{{ $r->id_role }}" @selected(request('role') == $r->id_role)>{{ $r->nama_role }}</option>
                        @endforeach
                    </select>
                </form>
                 <form method="GET">
                    @foreach (request()->except(['status', 'page']) as $nama => $nilai)
                        @if (is_string($nilai))
                            <input type="hidden" name="{{ $nama }}" value="{{ $nilai }}">
                        @endif
                    @endforeach
                    <select name="status" onchange="this.form.submit()"
                        class="h-10 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm text-gray-800 sm:w-40 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                        <option value="">Semua status</option>
                        <option value="aktif" @selected(request('status') == 'aktif')>Aktif</option>
                        <option value="nonaktif" @selected(request('status') == 'nonaktif')>Nonaktif</option>
                    </select>
                </form>
            </div>
            </div>

            <a href="{{ route('pengguna.create') }}"
                class="inline-flex h-10 items-center justify-center rounded-lg bg-brand-500 px-5 text-sm font-medium text-white hover:bg-brand-600">
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
                        <th class="py-3 pr-4 font-medium">Status</th>
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
                            <td class="py-3 pr-4">{{ $item->aktif ? $item->email : $item->email_lama }}</td>
                            <td class="py-3 pr-4">{{ $item->no_hp ?? '-' }}</td>
                            <td class="py-3 pr-4">
                                <span class="rounded-full px-2.5 py-0.5 text-xs font-medium capitalize {{ $warnaRole }}">
                                    {{ $item->role->nama_role }}
                                </span>
                            </td>
                                                        <td class="py-3 pr-4">
                                @if ($item->aktif)
                                    <span class="rounded-full bg-success-50 px-2.5 py-0.5 text-xs font-medium text-success-700">Aktif</span>
                                @else
                                    <span class="rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-600 dark:bg-white/10 dark:text-gray-400">Nonaktif</span>
                                    <div class="mt-1 text-xs text-gray-500">sejak {{ $item->dinonaktifkan_pada?->format('d/m/Y') }}</div>
                                @endif
                            </td>
                            <td class="py-3">
                                <div class="flex gap-2">
                                @if ($item->aktif)
                                        <a href="{{ route('pengguna.edit', $item) }}"
                                            class="rounded-lg border border-gray-300 px-3 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300">
                                            Edit
                                        </a>
                              @endif
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
                            <td colspan="7" class="py-6 text-center text-gray-500">{{ request('cari') || request('role') ? 'Tidak ada pengguna yang cocok.' : 'Belum ada pengguna.' }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @include('pages._paginasi', ['data' => $pengguna])
    </x-common.component-card>
@endsection