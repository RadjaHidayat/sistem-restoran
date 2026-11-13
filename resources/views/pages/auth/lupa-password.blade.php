@extends('layouts.fullscreen-layout')

@section('content')
    <div class="flex min-h-screen items-center justify-center bg-gray-50 px-4 py-10 dark:bg-gray-900">
        <div class="w-full max-w-md rounded-3xl border border-gray-200 bg-white p-6 shadow-theme-sm sm:p-8 dark:border-gray-800 dark:bg-white/[0.03]">
            <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-brand-50 text-2xl dark:bg-brand-500/15">🔑</span>
            <h1 class="mt-4 text-2xl font-semibold text-gray-800 dark:text-white/90">Lupa password?</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Masukkan email akun pelangganmu. Kami akan mengirim link untuk membuat password baru.
            </p>

            @if (session('success'))
                <div class="mt-5 rounded-lg border border-success-500 bg-success-50 px-4 py-3 text-sm text-success-700">
                    {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}" class="mt-6 space-y-5">
                @csrf
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="contoh@gmail.com" autofocus
                        class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                    @error('email')
                        <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="flex h-11 w-full items-center justify-center rounded-lg bg-brand-500 text-sm font-medium text-white hover:bg-brand-600">
                    Kirim link reset
                </button>
            </form>

            <p class="mt-5 text-center text-sm text-gray-500 dark:text-gray-400">
                Ingat password? <a href="{{ route('signin') }}" class="font-medium text-brand-500 hover:text-brand-600">Kembali masuk</a>
            </p>
            <p class="mt-3 text-center text-xs text-gray-400">
                Untuk akun staf (admin, kasir, koki), minta admin mengganti password lewat halaman Pengguna.
            </p>
        </div>
    </div>
@endsection