@extends('layouts.fullscreen-layout')

@section('content')
    <div class="mx-auto min-h-screen max-w-3xl bg-white px-5 py-8 dark:bg-gray-900">
        <!-- Kepala halaman -->
        <div class="flex items-start justify-between gap-4">
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Akun Pelanggan</p>
                <h1 class="text-2xl font-semibold text-gray-800 dark:text-white/90">Halo, {{ auth()->user()->nama }}!</h1>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300">
                    Keluar
                </button>
            </form>
        </div>

        @if (session('success'))
            <div class="mt-6 rounded-lg border border-success-500 bg-success-50 px-4 py-3 text-sm text-success-700">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="mt-6 rounded-lg border border-error-500 bg-error-50 px-4 py-3 text-sm text-error-700">
                {{ session('error') }}
            </div>
        @endif

        <!-- Riwayat pesanan -->
        <div class="mt-8">
            <div class="mb-3 flex items-center justify-between">
                <h2 class="font-semibold text-gray-800 dark:text-white/90">Pesanan Saya</h2>
                <a href="{{ route('akun.pesan') }}"
                    class="rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600">
                    + Pesan Antar / Ambil
                </a>
            </div>
            <div class="space-y-3">
                @forelse ($pesanan as $p)
                    <a href="{{ route('akun.pesanan', $p) }}"
                        class="flex items-center justify-between gap-4 rounded-2xl border border-gray-200 p-4 hover:bg-gray-50 dark:border-gray-800 dark:hover:bg-white/[0.03]">
                        <div>
                            <p class="font-mono text-xs text-gray-500">{{ $p->kode_pesanan }}</p>
                            <p class="text-sm font-medium text-gray-800 dark:text-white/90">
                                {{ ucwords(str_replace('_', ' ', $p->tipe_pesanan)) }}{{ $p->meja ? ' · Meja ' . $p->meja->nomor_meja : '' }}
                            </p>
                            <p class="text-xs text-gray-500">{{ $p->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-semibold text-gray-800 dark:text-white/90">Rp {{ number_format($p->total_harga, 0, ',', '.') }}</p>
                            <div class="mt-1">@include('pages.pesanan._status', ['status' => $p->status])</div>
                        </div>
                    </a>
                @empty
                    <p class="rounded-2xl border border-dashed border-gray-300 p-6 text-center text-sm text-gray-500 dark:border-gray-700">
                        Belum ada pesanan. Klik "+ Pesan Antar / Ambil", atau scan QR di meja restoran.
                    </p>
                @endforelse
            </div>
        </div>

        <!-- Riwayat reservasi -->
        <div class="mt-8">
            <div class="mb-3 flex items-center justify-between">
                <h2 class="font-semibold text-gray-800 dark:text-white/90">Reservasi Saya</h2>
                <a href="{{ route('akun.reservasi.buat') }}"
                    class="rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600">
                    + Buat Reservasi
                </a>
            </div>
            <div class="space-y-3">
                @forelse ($reservasi as $r)
                    <div class="flex items-center justify-between gap-4 rounded-2xl border border-gray-200 p-4 dark:border-gray-800">
                        <div>
                            <p class="text-sm font-medium text-gray-800 dark:text-white/90">
                                {{ \Carbon\Carbon::parse($r->tanggal)->format('d/m/Y') }}, pukul {{ substr($r->jam, 0, 5) }}
                            </p>
                            <p class="text-xs text-gray-500">
                                {{ $r->jumlah_orang }} orang{{ $r->meja ? ' · Meja ' . $r->meja->nomor_meja : '' }}
                            </p>
                        </div>
                        <div class="flex flex-col items-end gap-2">
                            @include('pages.reservasi._status', ['status' => $r->status])
                            @if (in_array($r->status, ['menunggu', 'dikonfirmasi']))
                                <form method="POST" action="{{ route('akun.reservasi.batal', $r) }}"
                                    onsubmit="return confirm('Batalkan reservasi ini?')">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="text-xs font-medium text-error-600 hover:underline">Batalkan</button>
                                </form>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="rounded-2xl border border-dashed border-gray-300 p-6 text-center text-sm text-gray-500 dark:border-gray-700">
                        Belum ada reservasi. Klik "+ Buat Reservasi" untuk memesan tempat.
                    </p>
                @endforelse
            </div>
        </div>
    </div>
@endsection