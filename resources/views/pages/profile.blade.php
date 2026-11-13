@extends('layouts.app')

@section('content')
    @php
        $inisial = collect(explode(' ', $user->nama))->filter()->map(fn ($k) => mb_substr($k, 0, 1))->take(2)->implode('');
        $warnaRole = match ($user->role->nama_role) {
            'admin' => 'bg-brand-50 text-brand-600',
            'kasir' => 'bg-success-50 text-success-700',
            'koki' => 'bg-warning-50 text-warning-700',
            default => 'bg-gray-100 text-gray-700',
        };
        $baris = [
            'Nama lengkap' => $user->nama,
            'Email' => $user->email,
            'No. HP' => $user->no_hp ?: '-',
            'Alamat' => $user->alamat ?: '-',
            'Role' => ucfirst($user->role->nama_role),
            'Terdaftar sejak' => $user->created_at ? $user->created_at->format('d/m/Y') : '-',
        ];
    @endphp

    <x-common.page-breadcrumb pageTitle="Profil Saya" />

    <div class="space-y-6">
        <!-- Kartu identitas -->
        <div class="rounded-2xl border border-gray-200 bg-white p-5 lg:p-6 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex flex-col items-center gap-5 text-center sm:flex-row sm:text-left">
                <span class="flex h-20 w-20 shrink-0 items-center justify-center rounded-full bg-brand-500 text-2xl font-semibold text-white uppercase">
                    {{ $inisial }}
                </span>
                <div class="flex-1">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">{{ $user->nama }}</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $user->email }}</p>
                    <span class="mt-2 inline-block rounded-full px-2.5 py-0.5 text-xs font-medium capitalize {{ $warnaRole }}">
                        {{ $user->role->nama_role }}
                    </span>
                </div>

                <!-- Ringkasan kerja hari ini -->
                <div class="grid w-full grid-cols-2 gap-3 sm:w-auto">
                    <div class="rounded-xl bg-gray-50 px-4 py-3 text-center dark:bg-white/5">
                        <p class="text-xl font-semibold text-gray-800 dark:text-white/90">{{ $pesananDicatat }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Pesanan dicatat hari ini</p>
                    </div>
                    <div class="rounded-xl bg-gray-50 px-4 py-3 text-center dark:bg-white/5">
                        <p class="text-xl font-semibold text-gray-800 dark:text-white/90">{{ $pembayaranDicatat }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Pembayaran dicatat hari ini</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Data akun (hanya-baca) -->
        <x-common.component-card title="Informasi Akun" desc="Data ini hanya bisa dilihat. Perubahan data akun staf dilakukan oleh admin.">
            <dl class="grid grid-cols-1 gap-x-8 gap-y-5 sm:grid-cols-2">
                @foreach ($baris as $label => $nilai)
                    <div>
                        <dt class="mb-1 text-xs text-gray-500 dark:text-gray-400">{{ $label }}</dt>
                        <dd class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $nilai }}</dd>
                    </div>
                @endforeach
            </dl>

            <div class="rounded-lg border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-600 dark:border-gray-800 dark:bg-white/5 dark:text-gray-400">
                @if ($user->punyaRole('admin'))
                    Sebagai admin, kamu bisa mengubah data akunmu sendiri lewat halaman Pengguna.
                    <a href="{{ route('pengguna.edit', $user) }}" class="font-medium text-brand-500 hover:underline">Ubah data di halaman Pengguna &rarr;</a>
                @else
                    Ada data yang salah atau ingin ganti password? Hubungi admin restoran untuk mengubahnya.
                @endif
            </div>
        </x-common.component-card>
    </div>
@endsection