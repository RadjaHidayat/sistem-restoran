@extends('layouts.app')

@section('content')
    @php
        $hari = \Carbon\Carbon::parse($tanggal);
        $hariIni = $hari->isToday();
        $daftarTab = [
            'semua' => 'Semua',
            'baru' => 'Baru',
            'dimasak' => 'Dimasak',
            'siap' => 'Siap',
            'selesai' => 'Selesai',
            'dibatalkan' => 'Dibatalkan',
        ];
        $warnaAngka = [
            'semua' => 'bg-gray-100 text-gray-700 dark:bg-white/10 dark:text-gray-300',
            'baru' => 'bg-gray-100 text-gray-700 dark:bg-white/10 dark:text-gray-300',
            'dimasak' => 'bg-warning-50 text-warning-700',
            'siap' => 'bg-brand-50 text-brand-600',
            'selesai' => 'bg-success-50 text-success-700',
            'dibatalkan' => 'bg-error-50 text-error-700',
        ];
        $judulKartu = $cari !== '' ? 'Hasil pencarian' : 'Daftar Pesanan';
        $keteranganKartu = $cari !== ''
            ? 'Mencari “' . $cari . '” di semua tanggal'
            : ($hariIni ? 'Hari ini, ' : '') . $hari->translatedFormat('l, d F Y');
    @endphp

    <x-common.page-breadcrumb pageTitle="Pesanan" />

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

    {{-- Pengingat pesanan hari sebelumnya yang belum selesai --}}
    @if ($jumlahTertunda > 0 && $cari === '')
        <div class="mb-6 flex flex-col gap-2 rounded-lg border border-warning-500 bg-warning-50 px-4 py-3 text-sm text-warning-700 sm:flex-row sm:items-center sm:justify-between">
            <span>Ada <b>{{ $jumlahTertunda }}</b> pesanan dari hari sebelumnya yang belum selesai.</span>
            <a href="{{ route('pesanan.index', ['tanggal' => $tanggalTertunda]) }}" class="font-medium underline">Lihat pesanan itu</a>
        </div>
    @endif

       <x-common.component-card :title="$judulKartu" :desc="$keteranganKartu">

        {{-- Baris 1: pilih tanggal + pencarian + tombol buat pesanan --}}
        <div class="flex flex-col gap-3 xl:flex-row xl:items-center xl:justify-between">
            <div class="flex flex-wrap items-center gap-2">
                @if ($cari === '')
                    <a href="{{ route('pesanan.index', ['tanggal' => $hari->copy()->subDay()->toDateString(), 'status' => $statusDipilih]) }}"
                        class="flex h-10 w-10 items-center justify-center rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300"
                        title="Hari sebelumnya">&lsaquo;</a>

                    <form method="GET" action="{{ route('pesanan.index') }}">
                        <input type="hidden" name="status" value="{{ $statusDipilih }}">
                        <input type="date" name="tanggal" value="{{ $tanggal }}" max="{{ now()->toDateString() }}" onchange="this.form.submit()"
                            class="h-10 rounded-lg border border-gray-300 bg-transparent px-3 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                    </form>

                    @if (! $hariIni)
                        <a href="{{ route('pesanan.index', ['tanggal' => $hari->copy()->addDay()->toDateString(), 'status' => $statusDipilih]) }}"
                            class="flex h-10 w-10 items-center justify-center rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300"
                            title="Hari berikutnya">&rsaquo;</a>
                        <a href="{{ route('pesanan.index') }}"
                            class="flex h-10 items-center rounded-lg border border-brand-500 px-3 text-sm font-medium text-brand-600 hover:bg-brand-50">
                            Kembali ke hari ini
                        </a>
                    @endif
                @else
                    <a href="{{ route('pesanan.index') }}"
                        class="flex h-10 items-center rounded-lg border border-gray-300 px-3 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300">
                        &larr; Hapus pencarian
                    </a>
                @endif
            </div>

            <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                <form method="GET" action="{{ route('pesanan.index') }}" class="relative">
                    <input type="hidden" name="tanggal" value="{{ $tanggal }}">
                    <input type="text" name="cari" value="{{ $cari }}" placeholder="Cari kode, pelanggan, meja, menu..."
                        class="h-10 w-full rounded-lg border border-gray-300 bg-transparent pr-3 pl-9 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden sm:w-72 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                    <svg class="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35M17 10.5a6.5 6.5 0 1 1-13 0 6.5 6.5 0 0 1 13 0Z" />
                    </svg>
                </form>

                @if (auth()->user()->punyaRole('admin', 'kasir'))
                    <a href="{{ route('pesanan.create') }}"
                        class="inline-flex h-10 items-center justify-center rounded-lg bg-brand-500 px-5 text-sm font-medium text-white hover:bg-brand-600">
                        + Buat Pesanan
                    </a>
                @endif
            </div>
        </div>

        {{-- Baris 2: tab status dengan angka --}}
        <div class="flex gap-2 overflow-x-auto pb-1">
            @foreach ($daftarTab as $nilai => $label)
                <a href="{{ route('pesanan.index', array_filter(['status' => $nilai, 'tanggal' => $tanggal, 'cari' => $cari])) }}"
                    class="flex shrink-0 items-center gap-2 rounded-lg px-3 py-1.5 text-xs font-medium {{ $statusDipilih == $nilai ? 'bg-brand-500 text-white' : 'border border-gray-300 text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300' }}">
                    {{ $label }}
                    <span class="rounded-full px-1.5 py-0.5 text-[11px] leading-none {{ $statusDipilih == $nilai ? 'bg-white/20 text-white' : $warnaAngka[$nilai] }}">
                        {{ $jumlah[$nilai] }}
                    </span>
                </a>
            @endforeach
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-gray-100 text-gray-500 dark:border-gray-800 dark:text-gray-400">
                        <th class="py-3 pr-4 font-medium">Kode</th>
                        <th class="py-3 pr-4 font-medium">Waktu</th>
                        <th class="py-3 pr-4 font-medium">Tipe</th>
                        <th class="py-3 pr-4 font-medium">Meja / Pelanggan</th>
                        <th class="py-3 pr-4 font-medium">Total</th>
                        <th class="py-3 pr-4 font-medium">Status</th>
                        <th class="py-3 font-medium">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pesanan as $item)
                        <tr class="border-b border-gray-100 text-gray-800 dark:border-gray-800 dark:text-white/90">
                            <td class="py-3 pr-4 font-mono text-xs">{{ $item->kode_pesanan }}</td>
                            <td class="py-3 pr-4 whitespace-nowrap">
                                {{ $cari !== '' ? $item->created_at->format('d/m/Y H:i') : $item->created_at->format('H:i') }}
                            </td>
                            <td class="py-3 pr-4">{{ ucwords(str_replace('_', ' ', $item->tipe_pesanan)) }}</td>
                            <td class="py-3 pr-4">
                                {{ $item->meja ? 'Meja ' . $item->meja->nomor_meja : '-' }}
                                @if ($item->user)
                                    <div class="text-xs text-gray-500">{{ $item->user->nama }}</div>
                                @endif
                            </td>
                            <td class="py-3 pr-4 whitespace-nowrap">Rp {{ number_format($item->total_harga, 0, ',', '.') }}</td>
                            <td class="py-3 pr-4">@include('pages.pesanan._status', ['status' => $item->status])</td>
                            <td class="py-3">
                                <a href="{{ route('pesanan.show', $item) }}"
                                    class="rounded-lg border border-gray-300 px-3 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300">
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-10 text-center text-gray-500">
                                @if ($cari !== '')
                                    Tidak ada pesanan yang cocok dengan "{{ $cari }}".
                                @elseif ($hariIni)
                                    Belum ada pesanan hari ini.
                                @else
                                    Tidak ada pesanan pada tanggal ini.
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @include('pages._paginasi', ['data' => $pesanan])
    </x-common.component-card>
@endsection