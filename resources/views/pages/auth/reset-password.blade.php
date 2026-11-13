@extends('layouts.fullscreen-layout')

@section('content')
    <div class="flex min-h-screen items-center justify-center bg-gray-50 px-4 py-10 dark:bg-gray-900">
        <div class="w-full max-w-md rounded-3xl border border-gray-200 bg-white p-6 shadow-theme-sm sm:p-8 dark:border-gray-800 dark:bg-white/[0.03]">
            <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-brand-50 text-2xl dark:bg-brand-500/15">🔒</span>
            <h1 class="mt-4 text-2xl font-semibold text-gray-800 dark:text-white/90">Buat password baru</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Minimal 6 karakter. Setelah ini kamu bisa langsung masuk.</p>

            <form method="POST" action="{{ route('password.update') }}" class="mt-6 space-y-5" x-data="{ lihat: false }">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Email</label>
                    <input type="email" name="email" value="{{ old('email', $email) }}"
                        class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                    @error('email')
                        <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Password baru</label>
                    <input :type="lihat ? 'text' : 'password'" name="password" autocomplete="new-password" autofocus
                        class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                    @error('password')
                        <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Ulangi password baru</label>
                    <input :type="lihat ? 'text' : 'password'" name="password_confirmation" autocomplete="new-password"
                        class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                </div>

                <label class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                    <input type="checkbox" x-model="lihat" class="h-4 w-4 rounded border-gray-300"> Tampilkan password
                </label>

                <button type="submit" class="flex h-11 w-full items-center justify-center rounded-lg bg-brand-500 text-sm font-medium text-white hover:bg-brand-600">
                    Simpan password baru
                </button>
            </form>
        </div>
    </div>
@endsection