{{-- Tampilan dasar area pelanggan: bar atas + navigasi bawah (HP). Halaman mengisi @section('isi'). --}}
@extends('layouts.fullscreen-layout')

@section('content')
    @php
        $user = auth()->user();
        $inisial = collect(explode(' ', $user->nama))->filter()->map(fn ($k) => mb_substr($k, 0, 1))->take(2)->implode('');

        $menuPelanggan = collect([
            ['route' => 'akun.index', 'label' => 'Beranda', 'ikon' => 'beranda', 'aktif' => request()->routeIs('akun.index')],
            ['route' => 'akun.pesan', 'label' => 'Pesan', 'ikon' => 'pesan', 'aktif' => request()->routeIs('akun.pesan')],
            ['route' => 'akun.reservasi.buat', 'label' => 'Reservasi', 'ikon' => 'reservasi', 'aktif' => request()->routeIs('akun.reservasi.*')],
            ['route' => 'akun.riwayat', 'label' => 'Riwayat', 'ikon' => 'riwayat', 'aktif' => request()->routeIs('akun.riwayat', 'akun.pesanan')],
            ['route' => 'akun.profil', 'label' => 'Profil', 'ikon' => 'profil', 'aktif' => request()->routeIs('akun.profil')],
        ])->filter(fn ($m) => Route::has($m['route'])); // menu yang route-nya belum dibuat otomatis disembunyikan
    @endphp

    <div class="min-h-screen bg-gray-50 dark:bg-gray-950">
        <!-- Bar atas -->
        <header class="sticky top-0 z-40 border-b border-gray-200 bg-white/90 backdrop-blur dark:border-gray-800 dark:bg-gray-900/90">
            <div class="mx-auto flex h-16 max-w-6xl items-center justify-between gap-4 px-4 sm:px-6">
                <a href="{{ route('akun.index') }}" class="flex items-center gap-2">
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-brand-500 font-bold text-white">
                        {{ mb_substr(config('app.name'), 0, 1) }}
                    </span>
                    <span class="hidden font-semibold text-gray-800 sm:block dark:text-white/90">{{ config('app.name') }}</span>
                </a>

                <!-- Menu (tablet & laptop) -->
                <nav class="hidden items-center gap-1 md:flex">
                    @foreach ($menuPelanggan as $m)
                        <a href="{{ route($m['route']) }}"
                            class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm font-medium {{ $m['aktif'] ? 'bg-brand-50 text-brand-600 dark:bg-brand-500/15 dark:text-brand-400' : 'text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-white/5' }}">
                            @include('pages.akun._ikon', ['nama' => $m['ikon'], 'kelas' => 'size-5'])
                            {{ $m['label'] }}
                        </a>
                    @endforeach
                </nav>

                <div class="flex items-center gap-2">
                    <button type="button" @click="$store.theme.toggle()" title="Ganti tema"
                        class="flex h-10 w-10 items-center justify-center rounded-full border border-gray-200 text-gray-500 hover:bg-gray-100 dark:border-gray-800 dark:text-gray-400 dark:hover:bg-white/5">
                        <span class="hidden dark:block"><x-svg.sun /></span>
                        <span class="dark:hidden"><x-svg.moon /></span>
                    </button>

                    <!-- Menu akun -->
                    <div class="relative" x-data="{ buka: false }" @click.outside="buka = false">
                        <button type="button" @click="buka = ! buka"
                            class="flex items-center gap-2 rounded-full border border-gray-200 py-1 pr-3 pl-1 hover:bg-gray-100 dark:border-gray-800 dark:hover:bg-white/5">
                            <span class="flex h-8 w-8 items-center justify-center rounded-full bg-brand-500 text-xs font-semibold text-white uppercase">{{ $inisial }}</span>
                            <span class="hidden max-w-28 truncate text-sm font-medium text-gray-700 sm:block dark:text-gray-300">{{ $user->nama }}</span>
                        </button>
                        <div x-show="buka" x-transition style="display: none"
                            class="absolute right-0 mt-2 w-60 rounded-2xl border border-gray-200 bg-white p-2 shadow-theme-lg dark:border-gray-800 dark:bg-gray-900">
                            <div class="px-3 py-2">
                                <p class="truncate text-sm font-medium text-gray-800 dark:text-white/90">{{ $user->nama }}</p>
                                <p class="truncate text-xs text-gray-500 dark:text-gray-400">{{ $user->email }}</p>
                            </div>
                            <div class="my-1 border-t border-gray-100 dark:border-gray-800"></div>
                            @if (Route::has('akun.profil'))
                                <a href="{{ route('akun.profil') }}" class="block rounded-lg px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/5">Profil Saya</a>
                            @endif
                            <a href="{{ route('akun.riwayat') }}" class="block rounded-lg px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/5">Riwayat Pesanan</a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="block w-full rounded-lg px-3 py-2 text-left text-sm text-error-600 hover:bg-error-50 dark:hover:bg-error-500/10">Keluar</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Isi halaman -->
        <main class="mx-auto w-full max-w-6xl px-4 pt-6 pb-28 sm:px-6 md:pb-12">
            @yield('isi')
        </main>

        <!-- Navigasi bawah (khusus layar HP). Halaman yang punya bar sendiri di bawah bisa menyembunyikannya. -->
        @sectionMissing('tanpa-nav-bawah')
            <nav class="fixed inset-x-0 bottom-0 z-40 border-t border-gray-200 bg-white pb-[env(safe-area-inset-bottom)] md:hidden dark:border-gray-800 dark:bg-gray-900">
                <div class="grid" style="grid-template-columns: repeat({{ $menuPelanggan->count() }}, minmax(0, 1fr));">
                    @foreach ($menuPelanggan as $m)
                        <a href="{{ route($m['route']) }}"
                            class="flex flex-col items-center gap-1 py-2.5 text-[11px] font-medium {{ $m['aktif'] ? 'text-brand-600 dark:text-brand-400' : 'text-gray-500 dark:text-gray-400' }}">
                            @include('pages.akun._ikon', ['nama' => $m['ikon'], 'kelas' => 'size-6'])
                            {{ $m['label'] }}
                        </a>
                    @endforeach
                </div>
            </nav>
        @endif
    </div>
@endsection