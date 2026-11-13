@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Menu Makanan" />

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

    <x-common.component-card title="Daftar Menu">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                @include('pages._cari', ['placeholder' => 'Cari nama menu...'])

                <form method="GET">
                    @if (request('cari'))
                        <input type="hidden" name="cari" value="{{ request('cari') }}">
                    @endif
                    <select name="kategori" onchange="this.form.submit()"
                        class="h-10 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm text-gray-800 sm:w-48 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                        <option value="">Semua kategori</option>
                        @foreach ($daftarKategori as $k)
                            <option value="{{ $k->id_kategori }}" @selected(request('kategori') == $k->id_kategori)>{{ $k->nama_kategori }}</option>
                        @endforeach
                    </select>
                </form>
            </div>

            <a href="{{ route('menu.create') }}"
                class="inline-flex h-10 items-center justify-center rounded-lg bg-brand-500 px-5 text-sm font-medium text-white hover:bg-brand-600">
                + Tambah Menu
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-gray-100 text-gray-500 dark:border-gray-800 dark:text-gray-400">
                        <th class="py-3 pr-4 font-medium">No</th>
                        <th class="py-3 pr-4 font-medium">Foto</th>
                        <th class="py-3 pr-4 font-medium">Nama Menu</th>
                        <th class="py-3 pr-4 font-medium">Kategori</th>
                        <th class="py-3 pr-4 font-medium">Harga</th>
                        <th class="py-3 pr-4 font-medium">Status</th>
                        <th class="py-3 font-medium">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($menu as $item)
                        <tr class="border-b border-gray-100 text-gray-800 dark:border-gray-800 dark:text-white/90">
                            <td class="py-3 pr-4">{{ $menu->firstItem() + $loop->index }}</td>
                            <td class="py-3 pr-4">
                                @if ($item->foto)
                                    <img src="{{ asset('storage/' . $item->foto) }}" alt="{{ $item->nama_menu }}"
                                        class="h-12 w-12 rounded-lg object-cover">
                                @else
                                    <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-gray-100 text-xs text-gray-400 dark:bg-gray-800">
                                        -
                                    </div>
                                @endif
                            </td>
                            <td class="py-3 pr-4">
                                <div class="font-medium">{{ $item->nama_menu }}</div>
                                @if ($item->deskripsi)
                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ Str::limit($item->deskripsi, 50) }}</div>
                                @endif
                            </td>
                            <td class="py-3 pr-4">{{ $item->kategori->nama_kategori }}</td>
                            <td class="py-3 pr-4">Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                            <td class="py-3 pr-4">
                                @if ($item->tersedia)
                                    <span class="rounded-full bg-success-50 px-2.5 py-0.5 text-xs font-medium text-success-700">Tersedia</span>
                                @else
                                    <span class="rounded-full bg-error-50 px-2.5 py-0.5 text-xs font-medium text-error-700">Habis</span>
                                @endif
                            </td>
                            <td class="py-3">
                                <div class="flex gap-2">
                                    <a href="{{ route('menu.edit', $item) }}"
                                        class="rounded-lg border border-gray-300 px-3 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300">
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ route('menu.destroy', $item) }}"
                                        onsubmit="return confirm('Hapus menu ini?')">
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
                            <td colspan="7" class="py-6 text-center text-gray-500">{{ request('cari') || request('kategori') ? 'Tidak ada menu yang cocok.' : 'Belum ada menu.' }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @include('pages._paginasi', ['data' => $menu])
    </x-common.component-card>
@endsection