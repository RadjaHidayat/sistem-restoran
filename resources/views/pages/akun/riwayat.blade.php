@extends('layouts.pelanggan')

@section('isi')
    @php
        $hari = \Carbon\Carbon::parse($tanggal);
        $rupiah = fn ($n) => 'Rp ' . number_format($n, 0, ',', '.');
        $tombolTab = 'rounded-lg px-4 py-2 text-sm font-medium';
        $tabAktif = 'bg-white text-gray-800 shadow-theme-xs dark:bg-gray-800 dark:text-white/90';
        $tabBiasa = 'text-gray-500 hover:text-gray-700 dark:text-gray-400';
    @endphp

    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-800 dark:text-white/90">Riwayat</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">Semua pesanan dan reservasimu tersimpan di sini.</p>
        </div>
        <div class="inline-flex rounded-xl bg-gray-100 p-1 dark:bg-white/5">
            <a href="{{ route('akun.riwayat') }}" class="{{ $tombolTab }} {{ $tab == 'pesanan' ? $tabAktif : $tabBiasa }}">Pesanan</a>
            <a href="{{ route('akun.riwayat', ['tab' => 'reservasi']) }}" class="{{ $tombolTab }} {{ $tab == 'reservasi' ? $tabAktif : $tabBiasa }}">Reservasi</a>
        </div>
    </div>

    @if (session('success'))
        <div class="mt-5 rounded-xl border border-success-500 bg-success-50 px-4 py-3 text-sm text-success-700">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="mt-5 rounded-xl border border-error-500 bg-error-50 px-4 py-3 text-sm text-error-700">{{ session('error') }}</div>
    @endif

    @if ($tab == 'pesanan')
        <!-- Pilih hari -->
        <div class="mt-6 flex flex-wrap items-center gap-2">
            <a href="{{ route('akun.riwayat', ['tanggal' => $hari->copy()->subDay()->toDateString()]) }}"
                class="flex h-10 w-10 items-center justify-center rounded-xl border border-gray-200 bg-white text-gray-700 hover:bg-gray-50 dark:border-gray-800 dark:bg-white/[0.03] dark:text-gray-300">&lsaquo;</a>
            <form method="GET" action="{{ route('akun.riwayat') }}">
                <input type="date" name="tanggal" value="{{ $tanggal }}" max="{{ now()->toDateString() }}" onchange="this.form.submit()"
                    class="h-10 rounded-xl border border-gray-200 bg-white px-3 text-sm text-gray-800 dark:border-gray-800 dark:bg-gray-900 dark:text-white/90">
            </form>
            @if (! $hari->isToday())
                <a href="{{ route('akun.riwayat', ['tanggal' => $hari->copy()->addDay()->toDateString()]) }}"
                    class="flex h-10 w-10 items-center justify-center rounded-xl border border-gray-200 bg-white text-gray-700 hover:bg-gray-50 dark:border-gray-800 dark:bg-white/[0.03] dark:text-gray-300">&rsaquo;</a>
                <a href="{{ route('akun.riwayat') }}" class="flex h-10 items-center rounded-xl px-3 text-sm font-medium text-brand-600 hover:bg-brand-50 dark:text-brand-400">Hari ini</a>
            @endif
        </div>

        <!-- Lompat ke hari lain yang ada pesanannya -->
        @if ($hariLain->isNotEmpty())
            <div class="no-scrollbar mt-3 flex gap-2 overflow-x-auto pb-1">
                @foreach ($hariLain as $h)
                    @php $tgl = \Carbon\Carbon::parse($h->tgl); @endphp
                    <a href="{{ route('akun.riwayat', ['tanggal' => $tgl->toDateString()]) }}"
                        class="shrink-0 rounded-full border px-3 py-1.5 text-xs font-medium {{ $tgl->toDateString() == $tanggal ? 'border-brand-500 bg-brand-500 text-white' : 'border-gray-200 bg-white text-gray-600 hover:border-brand-300 dark:border-gray-800 dark:bg-white/[0.03] dark:text-gray-400' }}">
                        {{ $tgl->isToday() ? 'Hari ini' : $tgl->translatedFormat('d M') }} · {{ $h->jumlah }}
                    </a>
                @endforeach
            </div>
        @endif

        <!-- Ringkasan hari terpilih -->
        <div class="mt-5 grid grid-cols-2 gap-3 sm:max-w-md">
            <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03]">
                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $hari->isToday() ? 'Pesanan hari ini' : 'Pesanan ' . $hari->translatedFormat('d M Y') }}</p>
                <p class="mt-1 text-2xl font-semibold text-gray-800 dark:text-white/90">{{ $pesanan->count() }}</p>
            </div>
            <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03]">
                <p class="text-xs text-gray-500 dark:text-gray-400">Total belanja</p>
                <p class="mt-1 truncate text-2xl font-semibold text-gray-800 dark:text-white/90">{{ $rupiah($belanja) }}</p>
            </div>
        </div>

        <!-- Daftar pesanan hari itu -->
        <div class="mt-5 grid grid-cols-1 gap-3 md:grid-cols-2">
            @forelse ($pesanan as $p)
                <a href="{{ route('akun.pesanan', $p) }}"
                    class="flex flex-col rounded-2xl border border-gray-200 bg-white p-4 hover:border-brand-300 dark:border-gray-800 dark:bg-white/[0.03]">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="font-mono text-xs text-gray-500">{{ $p->kode_pesanan }} · {{ $p->created_at->format('H:i') }}</p>
                            <p class="text-sm font-medium text-gray-800 dark:text-white/90">
                                {{ $p->meja ? 'Makan di tempat · Meja ' . $p->meja->nomor_meja : ($p->tipe_pesanan == 'delivery' ? 'Diantar' : 'Ambil sendiri') }}
                            </p>
                        </div>
                        @include('pages.pesanan._status', ['status' => $p->status])
                    </div>
                    <p class="mt-2 line-clamp-2 text-xs text-gray-500 dark:text-gray-400">
                        {{ $p->detail->map(fn ($d) => $d->jumlah . 'x ' . $d->menu->nama_menu)->implode(', ') }}
                    </p>
                    <p class="mt-auto pt-3 text-sm font-semibold text-gray-800 dark:text-white/90">{{ $rupiah($p->total_harga) }}</p>
                </a>
            @empty
                <div class="rounded-2xl border border-dashed border-gray-300 p-8 text-center md:col-span-2 dark:border-gray-700">
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        {{ $hari->isToday() ? 'Belum ada pesanan hari ini.' : 'Tidak ada pesanan pada tanggal ini.' }}
                    </p>
                    @if ($hari->isToday())
                        <a href="{{ route('akun.pesan') }}" class="mt-2 inline-block text-sm font-medium text-brand-500 hover:underline">Pesan sekarang &rarr;</a>
                    @endif
                </div>
            @endforelse
        </div>
    @else
        <!-- Riwayat reservasi -->
        <div class="mt-6 grid grid-cols-1 gap-3 md:grid-cols-2">
            @forelse ($reservasi as $r)
                <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03]">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-sm font-medium text-gray-800 dark:text-white/90">
                                {{ \Carbon\Carbon::parse($r->tanggal)->translatedFormat('l, d M Y') }} · {{ substr($r->jam, 0, 5) }}
                            </p>
                            <p class="text-xs text-gray-500">{{ $r->jumlah_orang }} orang{{ $r->meja ? ' · Meja ' . $r->meja->nomor_meja : '' }}</p>
                            @if ($r->catatan)
                                <p class="mt-1 text-xs text-gray-400">"{{ $r->catatan }}"</p>
                            @endif
                        </div>
                        @include('pages.reservasi._status', ['status' => $r->status])
                    </div>
                    @if (in_array($r->status, ['menunggu', 'dikonfirmasi']) && $r->tanggal >= now()->toDateString())
                        <form method="POST" action="{{ route('akun.reservasi.batal', $r) }}" class="mt-3" onsubmit="return confirm('Batalkan reservasi ini?')">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="text-xs font-medium text-error-600 hover:underline">Batalkan reservasi</button>
                        </form>
                    @endif
                </div>
            @empty
                <p class="rounded-2xl border border-dashed border-gray-300 p-8 text-center text-sm text-gray-500 md:col-span-2 dark:border-gray-700">
                    Belum ada reservasi.
                </p>
            @endforelse
        </div>
        <div class="mt-4">
            {{ $reservasi->links() }}
        </div>
    @endif
@endsection