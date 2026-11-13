@extends('layouts.pelanggan')

@section('isi')
    @php
        $jam = now()->hour;
        $salam = $jam < 11 ? 'Selamat pagi' : ($jam < 15 ? 'Selamat siang' : ($jam < 18 ? 'Selamat sore' : 'Selamat malam'));
        $namaDepan = explode(' ', auth()->user()->nama)[0];
        $rupiah = fn ($n) => 'Rp ' . number_format($n, 0, ',', '.');
        $langkah = ['baru' => 1, 'dimasak' => 2, 'siap' => 3];
        $labelStatus = ['baru' => 'Pesanan diterima', 'dimasak' => 'Sedang dimasak', 'siap' => 'Siap'];
    @endphp

    @if (session('success'))
        <div class="mb-5 rounded-xl border border-success-500 bg-success-50 px-4 py-3 text-sm text-success-700">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="mb-5 rounded-xl border border-error-500 bg-error-50 px-4 py-3 text-sm text-error-700">{{ session('error') }}</div>
    @endif

    <!-- Sapaan + tombol utama -->
    <section class="relative overflow-hidden rounded-3xl bg-brand-500 p-6 text-white sm:p-8">
        <div class="absolute -top-16 -right-16 h-56 w-56 rounded-full bg-white/10"></div>
        <div class="absolute -bottom-20 right-24 h-40 w-40 rounded-full bg-white/10"></div>

        <div class="relative">
            <p class="text-sm text-white/80">{{ now()->translatedFormat('l, d F Y') }}</p>
            <h1 class="mt-1 text-2xl font-semibold sm:text-3xl">{{ $salam }}, {{ $namaDepan }}!</h1>
            <p class="mt-1 max-w-lg text-sm text-white/80">Mau makan apa hari ini? Pesan untuk diantar, ambil sendiri, atau pesan meja dulu.</p>

            <div class="mt-5 flex flex-col gap-3 sm:flex-row">
                <a href="{{ route('akun.pesan') }}"
                    class="inline-flex h-11 items-center justify-center gap-2 rounded-xl bg-white px-5 text-sm font-semibold text-brand-600 hover:bg-brand-50">
                    @include('pages.akun._ikon', ['nama' => 'pesan', 'kelas' => 'size-5'])
                    Pesan Antar / Ambil
                </a>
                <a href="{{ route('akun.reservasi.buat') }}"
                    class="inline-flex h-11 items-center justify-center gap-2 rounded-xl border border-white/40 px-5 text-sm font-semibold text-white hover:bg-white/10">
                    @include('pages.akun._ikon', ['nama' => 'reservasi', 'kelas' => 'size-5'])
                    Reservasi Meja
                </a>
            </div>
        </div>
    </section>

    <!-- Ringkasan hari ini -->
    <section class="mt-6 grid grid-cols-2 gap-3 lg:grid-cols-4 lg:gap-4">
        <a href="{{ route('akun.riwayat') }}" class="rounded-2xl border border-gray-200 bg-white p-4 hover:border-brand-300 dark:border-gray-800 dark:bg-white/[0.03]">
            <p class="text-xs text-gray-500 dark:text-gray-400">Pesanan hari ini</p>
            <p class="mt-1 text-2xl font-semibold text-gray-800 dark:text-white/90">{{ $jumlahHariIni }}</p>
            <p class="mt-1 text-xs font-medium text-brand-500">Lihat riwayat &rarr;</p>
        </a>
        <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03]">
            <p class="text-xs text-gray-500 dark:text-gray-400">Sedang diproses</p>
            <p class="mt-1 text-2xl font-semibold text-gray-800 dark:text-white/90">{{ $pesananAktif->count() }}</p>
            <p class="mt-1 text-xs text-gray-400">pesanan</p>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03]">
            <p class="text-xs text-gray-500 dark:text-gray-400">Belanja hari ini</p>
            <p class="mt-1 truncate text-2xl font-semibold text-gray-800 dark:text-white/90">{{ $rupiah($belanjaHariIni) }}</p>
            <p class="mt-1 text-xs text-gray-400">tidak termasuk yang dibatalkan</p>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03]">
            <p class="text-xs text-gray-500 dark:text-gray-400">Reservasi mendatang</p>
            <p class="mt-1 text-2xl font-semibold text-gray-800 dark:text-white/90">{{ $reservasiMendatang->count() }}</p>
            <p class="mt-1 text-xs text-gray-400">menunggu / dikonfirmasi</p>
        </div>
    </section>

    <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Kolom kiri: pesanan berjalan + ringkasan riwayat -->
        <div class="space-y-6 lg:col-span-2">
            <section class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
                <h2 class="font-semibold text-gray-800 dark:text-white/90">Pesanan berjalan</h2>
                <p class="text-xs text-gray-500 dark:text-gray-400">Klik pesanan untuk melihat detail dan statusnya.</p>

                <div class="mt-4 space-y-3">
                    @forelse ($pesananAktif as $p)
                        @php $posisi = $langkah[$p->status]; @endphp
                        <a href="{{ route('akun.pesanan', $p) }}"
                            class="block rounded-xl border border-gray-200 p-4 hover:border-brand-300 hover:bg-brand-50/40 dark:border-gray-800 dark:hover:bg-white/5">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="font-mono text-xs text-gray-500">{{ $p->kode_pesanan }}</p>
                                    <p class="text-sm font-medium text-gray-800 dark:text-white/90">
                                        {{ $p->meja ? 'Makan di tempat · Meja ' . $p->meja->nomor_meja : ($p->tipe_pesanan == 'delivery' ? 'Diantar' : 'Ambil sendiri') }}
                                    </p>
                                    <p class="text-xs text-gray-500">{{ $p->created_at->format('d/m H:i') }}</p>
                                </div>
                                <p class="text-sm font-semibold text-gray-800 dark:text-white/90">{{ $rupiah($p->total_harga) }}</p>
                            </div>
                            <!-- Bar kemajuan 4 langkah -->
                            <div class="mt-3 grid grid-cols-4 gap-1.5">
                                @for ($i = 1; $i <= 4; $i++)
                                    <span class="h-1.5 rounded-full {{ $i <= $posisi ? 'bg-brand-500' : 'bg-gray-200 dark:bg-gray-800' }}"></span>
                                @endfor
                            </div>
                            <p class="mt-2 text-xs font-medium text-brand-600 dark:text-brand-400">{{ $labelStatus[$p->status] }}</p>
                        </a>
                    @empty
                        <div class="rounded-xl border border-dashed border-gray-300 p-6 text-center dark:border-gray-700">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Tidak ada pesanan yang sedang diproses.</p>
                            <a href="{{ route('akun.pesan') }}" class="mt-2 inline-block text-sm font-medium text-brand-500 hover:underline">Pesan sekarang &rarr;</a>
                        </div>
                    @endforelse
                </div>
            </section>

            <!-- Ringkasan riwayat: hanya angka, daftar lengkapnya di halaman Riwayat -->
            <a href="{{ route('akun.riwayat') }}"
                class="flex items-center justify-between gap-4 rounded-2xl border border-gray-200 bg-white p-5 hover:border-brand-300 dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="flex items-center gap-4">
                    <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-brand-50 text-brand-600 dark:bg-brand-500/15 dark:text-brand-400">
                        @include('pages.akun._ikon', ['nama' => 'riwayat', 'kelas' => 'size-6'])
                    </span>
                    <div>
                        <p class="font-semibold text-gray-800 dark:text-white/90">Riwayat pesanan</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Hari ini {{ $jumlahHariIni }} pesanan · Total {{ $jumlahSemua }} pesanan sejak daftar
                        </p>
                    </div>
                </div>
                @include('pages.akun._ikon', ['nama' => 'kanan', 'kelas' => 'size-5 text-gray-400'])
            </a>
        </div>

        <!-- Kolom kanan: reservasi + info -->
        <div class="space-y-6">
            <section class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="flex items-center justify-between">
                    <h2 class="font-semibold text-gray-800 dark:text-white/90">Reservasi mendatang</h2>
                    <a href="{{ route('akun.reservasi.buat') }}" class="text-sm font-medium text-brand-500 hover:underline">+ Buat</a>
                </div>

                <div class="mt-4 space-y-3">
                    @forelse ($reservasiMendatang as $r)
                        <div class="rounded-xl border border-gray-200 p-4 dark:border-gray-800">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="text-sm font-medium text-gray-800 dark:text-white/90">
                                        {{ \Carbon\Carbon::parse($r->tanggal)->translatedFormat('D, d M') }} · {{ substr($r->jam, 0, 5) }}
                                    </p>
                                    <p class="text-xs text-gray-500">{{ $r->jumlah_orang }} orang{{ $r->meja ? ' · Meja ' . $r->meja->nomor_meja : '' }}</p>
                                </div>
                                @include('pages.reservasi._status', ['status' => $r->status])
                            </div>
                            <form method="POST" action="{{ route('akun.reservasi.batal', $r) }}" class="mt-3"
                                onsubmit="return confirm('Batalkan reservasi ini?')">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="text-xs font-medium text-error-600 hover:underline">Batalkan reservasi</button>
                            </form>
                        </div>
                    @empty
                        <p class="rounded-xl border border-dashed border-gray-300 p-5 text-center text-sm text-gray-500 dark:border-gray-700">
                            Belum ada reservasi.
                        </p>
                    @endforelse
                </div>
            </section>

            <section class="rounded-2xl border border-gray-200 bg-white p-5 text-sm dark:border-gray-800 dark:bg-white/[0.03]">
                <h2 class="font-semibold text-gray-800 dark:text-white/90">Info restoran</h2>
                <ul class="mt-3 space-y-2 text-gray-600 dark:text-gray-400">
                    <li>🕙 Buka setiap hari, 10.00 – 21.00</li>
                    <li>🛵 Delivery: bayar tunai / QRIS saat pesanan sampai</li>
                    <li>🥡 Ambil sendiri: bayar di kasir saat mengambil</li>
                    <li>🍽️ Makan di tempat: scan QR di meja</li>
                </ul>
            </section>
        </div>
    </div>
@endsection