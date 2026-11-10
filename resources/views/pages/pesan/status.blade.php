@extends('layouts.fullscreen-layout')

@section('content')
    @php
        $langkah = ['baru' => 'Pesanan diterima', 'dimasak' => 'Sedang dimasak', 'siap' => 'Siap disajikan', 'selesai' => 'Selesai'];
        $urutan = array_keys($langkah);
        $posisi = array_search($pesanan->status, $urutan);
    @endphp

    <div class="mx-auto min-h-screen max-w-2xl bg-white px-5 py-8 dark:bg-gray-900">
        <p class="text-sm text-gray-500 dark:text-gray-400">Meja {{ $pesanan->meja->nomor_meja }}</p>
        <h1 class="text-2xl font-semibold text-gray-800 dark:text-white/90">Terima kasih!</h1>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
            Kode pesanan: <span class="font-mono font-medium text-gray-800 dark:text-white/90">{{ $pesanan->kode_pesanan }}</span>
        </p>

        <!-- Status pesanan -->
        <div class="mt-6 rounded-2xl border border-gray-200 p-5 dark:border-gray-800">
            @if ($pesanan->status == 'dibatalkan')
                <p class="font-medium text-error-600">Pesanan ini dibatalkan. Silakan hubungi pelayan.</p>
            @else
                <ol class="space-y-3">
                    @foreach ($langkah as $kode => $label)
                        @php $sudah = $loop->index <= $posisi; @endphp
                        <li class="flex items-center gap-3">
                            <span class="flex h-7 w-7 items-center justify-center rounded-full text-xs font-semibold {{ $sudah ? 'bg-success-500 text-white' : 'bg-gray-100 text-gray-400 dark:bg-gray-800' }}">
                                {{ $sudah ? '✓' : $loop->iteration }}
                            </span>
                            <span class="text-sm {{ $sudah ? 'font-medium text-gray-800 dark:text-white/90' : 'text-gray-400' }}">{{ $label }}</span>
                        </li>
                    @endforeach
                </ol>
                <p class="mt-4 text-xs text-gray-400">Halaman ini diperbarui otomatis setiap 20 detik.</p>
            @endif
        </div>

        <!-- Rincian -->
        <div class="mt-6 rounded-2xl border border-gray-200 p-5 dark:border-gray-800">
            <h2 class="mb-3 font-medium text-gray-800 dark:text-white/90">Rincian Pesanan</h2>
            <div class="space-y-2 text-sm">
                @foreach ($pesanan->detail as $d)
                    <div class="flex justify-between gap-4 text-gray-700 dark:text-gray-300">
                        <div>
                            {{ $d->jumlah }}x {{ $d->menu->nama_menu }}
                            @if ($d->catatan)
                                <div class="text-xs text-gray-500">{{ $d->catatan }}</div>
                            @endif
                        </div>
                        <span>Rp {{ number_format($d->subtotal, 0, ',', '.') }}</span>
                    </div>
                @endforeach
                <div class="flex justify-between border-t border-gray-200 pt-2 font-semibold text-gray-800 dark:border-gray-800 dark:text-white/90">
                    <span>Total</span>
                    <span>Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}</span>
                </div>
            </div>

            <div class="mt-4">
                @if ($pesanan->pembayaran)
                    <span class="rounded-full bg-success-50 px-3 py-1 text-xs font-medium text-success-700">Sudah dibayar</span>
                @else
                    <p class="text-sm text-gray-500 dark:text-gray-400">Pembayaran dilakukan di kasir.</p>
                @endif
            </div>
        </div>

        <a href="{{ route('pesan.menu', $pesanan->meja->kode_qr) }}"
            class="mt-6 inline-flex h-11 items-center rounded-lg border border-gray-300 px-5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300">
            + Pesan menu lain
        </a>
    </div>

    @if (!in_array($pesanan->status, ['selesai', 'dibatalkan']))
        <script>
            setTimeout(() => window.location.reload(), 20000);
        </script>
    @endif
@endsection