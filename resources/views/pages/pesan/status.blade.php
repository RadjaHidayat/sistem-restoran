@extends('layouts.fullscreen-layout')

@section('content')
    @php
        $langkah = ['baru' => 'Pesanan diterima', 'dimasak' => 'Sedang dimasak', 'siap' => 'Siap disajikan', 'selesai' => 'Selesai'];
        $keterangan = [
            'baru' => 'Dapur sudah menerima pesananmu.',
            'dimasak' => 'Koki sedang menyiapkan pesananmu.',
            'siap' => 'Pesananmu sedang diantar ke meja.',
            'selesai' => 'Selamat menikmati! Pembayaran di kasir.',
        ];
        $urutan = array_keys($langkah);
        $posisi = array_search($pesanan->status, $urutan);
    @endphp

    <div class="min-h-screen bg-gray-50 dark:bg-gray-950">
        <div class="mx-auto max-w-2xl px-4 py-6">
            <!-- Ringkasan -->
            <div class="rounded-3xl bg-brand-500 p-6 text-center text-white">
                <p class="text-sm text-white/80">Meja {{ $pesanan->meja->nomor_meja }} · {{ $pesanan->created_at->format('H:i') }}</p>
                <h1 class="mt-1 text-2xl font-semibold">
                    {{ $pesanan->status == 'dibatalkan' ? 'Pesanan dibatalkan' : 'Terima kasih!' }}
                </h1>
                <p class="mt-2 inline-block rounded-full bg-white/15 px-3 py-1 font-mono text-sm">{{ $pesanan->kode_pesanan }}</p>
            </div>

            <!-- Status -->
            <div class="mt-4 rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
                @if ($pesanan->status == 'dibatalkan')
                    <p class="font-medium text-error-600">Pesanan ini dibatalkan. Silakan hubungi pelayan.</p>
                @else
                    <p class="text-sm font-semibold text-gray-800 dark:text-white/90">{{ $keterangan[$pesanan->status] }}</p>
                    <ol class="mt-4 space-y-4">
                        @foreach ($langkah as $kode => $label)
                            @php
                                $sudah = $loop->index < $posisi;
                                $sekarang = $loop->index == $posisi;
                            @endphp
                            <li class="flex items-center gap-3">
                                <span class="relative flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-xs font-semibold
                                    {{ $sudah ? 'bg-success-500 text-white' : ($sekarang ? 'bg-brand-500 text-white' : 'bg-gray-100 text-gray-400 dark:bg-gray-800') }}">
                                    @if ($sekarang && $kode != 'selesai')
                                        <span class="absolute inset-0 animate-ping rounded-full bg-brand-500 opacity-30"></span>
                                    @endif
                                    {{ $sudah || ($sekarang && $kode == 'selesai') ? '✓' : $loop->iteration }}
                                </span>
                                <span class="text-sm {{ $sudah || $sekarang ? 'font-medium text-gray-800 dark:text-white/90' : 'text-gray-400' }}">{{ $label }}</span>
                            </li>
                        @endforeach
                    </ol>
                    @if ($pesanan->status != 'selesai')
                        <p class="mt-4 text-xs text-gray-400">Halaman ini diperbarui otomatis setiap 20 detik.</p>
                    @endif
                @endif
            </div>

            <!-- Rincian -->
            <div class="mt-4 rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
                <h2 class="mb-3 font-semibold text-gray-800 dark:text-white/90">Rincian pesanan</h2>
                <div class="space-y-3 text-sm">
                    @foreach ($pesanan->detail as $d)
                        <div class="flex justify-between gap-4 text-gray-700 dark:text-gray-300">
                            <div>
                                <span class="font-medium">{{ $d->jumlah }}x</span> {{ $d->menu->nama_menu }}
                                @if ($d->catatan)
                                    <div class="text-xs text-gray-500">Catatan: {{ $d->catatan }}</div>
                                @endif
                            </div>
                            <span class="whitespace-nowrap">Rp {{ number_format($d->subtotal, 0, ',', '.') }}</span>
                        </div>
                    @endforeach
                    <div class="flex justify-between border-t border-dashed border-gray-200 pt-3 text-base font-semibold text-gray-800 dark:border-gray-800 dark:text-white/90">
                        <span>Total</span>
                        <span>Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}</span>
                    </div>
                </div>

                <div class="mt-4">
                    @if ($pesanan->pembayaran)
                        <span class="rounded-full bg-success-50 px-3 py-1 text-xs font-medium text-success-700">Sudah dibayar</span>
                    @else
                        <p class="text-sm text-gray-500 dark:text-gray-400">Pembayaran di kasir (tunai atau QRIS).</p>
                    @endif
                </div>
            </div>

            <a href="{{ route('pesan.menu', $pesanan->meja->kode_qr) }}"
                class="mt-4 flex h-12 w-full items-center justify-center rounded-2xl border border-brand-500 text-sm font-semibold text-brand-600 hover:bg-brand-50 dark:text-brand-400 dark:hover:bg-brand-500/10">
                + Pesan menu lain
            </a>
        </div>
    </div>

    @if (!in_array($pesanan->status, ['selesai', 'dibatalkan']))
        <script>
            setTimeout(() => window.location.reload(), 20000);
        </script>
    @endif
@endsection