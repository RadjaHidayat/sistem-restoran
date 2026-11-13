@extends('layouts.fullscreen-layout')

@section('content')
    <div class="relative z-1 bg-white p-6 sm:p-0 dark:bg-gray-900">
        <div class="relative flex min-h-screen w-full flex-col justify-center sm:p-0 lg:flex-row dark:bg-gray-900">
            <!-- Form -->
            <div class="flex w-full flex-1 flex-col lg:w-1/2">
                <div class="mx-auto flex w-full max-w-md flex-1 flex-col justify-center py-10">
                    <div class="mb-5 sm:mb-8">
                        <h1 class="text-title-sm sm:text-title-md mb-2 font-semibold text-gray-800 dark:text-white/90">
                            Masuk
                        </h1>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Masukkan email dan password akunmu.
                        </p>
                    </div>

                    @if (session('success'))
                        <div class="mb-5 rounded-lg border border-success-500 bg-success-50 px-4 py-3 text-sm text-success-700">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('signin.proses') }}">
                        @csrf
                        <div class="space-y-5">
                            <!-- Email -->
                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                    Email<span class="text-error-500">*</span>
                                </label>
                                <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="contoh@gmail.com" autofocus
                                    class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
                                @error('email')
                                    <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Password -->
                            <div>
                                <div class="mb-1.5 flex items-center justify-between">
                                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                    Password<span class="text-error-500">*</span>
                                </label>
                                </div>
                                <div x-data="{ showPassword: false }" class="relative">
                                    <input :type="showPassword ? 'text' : 'password'" name="password" placeholder="Password"
                                        class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 pr-16 pl-4 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
                                    <button type="button" @click="showPassword = !showPassword"
                                        class="absolute top-1/2 right-3 -translate-y-1/2 text-xs font-medium text-gray-500 hover:text-gray-700 dark:text-gray-400"
                                        x-text="showPassword ? 'Sembunyikan' : 'Lihat'"></button>
                                </div>
                            </div>

                            <!-- Tombol -->
                            <button type="submit"
                                class="bg-brand-500 shadow-theme-xs hover:bg-brand-600 flex w-full items-center justify-center rounded-lg px-4 py-3 text-sm font-medium text-white transition">
                                Masuk
                            </button>
                        </div>
                    </form>

                    <div class="mt-5 flex flex-wrap items-center justify-between gap-2 text-sm">
                        <p class="font-normal text-gray-700 dark:text-gray-400">
                            Pelanggan baru?
                            <a href="{{ route('signup') }}" class="text-brand-500 hover:text-brand-600 dark:text-brand-400">Daftar akun</a>
                        </p>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-brand-500 hover:text-brand-600 dark:text-brand-400">
                                Lupa password?
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Panel kanan -->
            <div class="bg-brand-950 relative hidden min-h-screen w-full items-center lg:grid lg:w-1/2 dark:bg-white/5">
                <div class="z-1 flex items-center justify-center">
                    <x-common.common-grid-shape />
                    <div class="flex max-w-xs flex-col items-center text-center">
                        <span class="mb-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-brand-500 text-3xl font-bold text-white">
                            {{ mb_substr(config('app.name'), 0, 1) }}
                        </span>
                        <p class="text-2xl font-semibold text-white">{{ config('app.name') }}</p>
                        <p class="mt-2 text-gray-400 dark:text-white/60">
                            Pesan makanan, reservasi meja, dan pantau pesananmu di satu tempat.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Tombol tema gelap/terang -->
            <div class="fixed right-6 bottom-6 z-50">
                <button type="button"
                    class="bg-brand-500 hover:bg-brand-600 inline-flex size-14 items-center justify-center rounded-full text-white transition-colors"
                    @click.prevent="$store.theme.toggle()">
                    <span class="hidden dark:block"><x-svg.sun /></span>
                    <span class="dark:hidden"><x-svg.moon /></span>
                </button>
            </div>
        </div>
    </div>
@endsection