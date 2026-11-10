@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Detail Pesanan" />

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

    <div class="space-y-6">
        <x-common.component-card title="Pesanan {{ $pesanan->kode_pesanan }}">
            <dl class="grid grid-cols-1 gap-4 text-sm sm:grid-cols-3">
                <div>
                    <dt class="text-gray-500 dark:text-gray-400">Waktu</dt>
                    <dd class="font-medium text-gray-800 dark:text-white/90">{{ $pesanan->created_at->format('d/m/Y H:i') }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500 dark:text-gray-400">Tipe</dt>
                    <dd class="font-medium text-gray-800 dark:text-white/90">{{ ucwords(str_replace('_', ' ', $pesanan->tipe_pesanan)) }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500 dark:text-gray-400">Status</dt>
                    <dd>@include('pages.pesanan._status', ['status' => $pesanan->status])</dd>
                </div>
                <div>
                    <dt class="text-gray-500 dark:text-gray-400">Meja</dt>
                    <dd class="font-medium text-gray-800 dark:text-white/90">{{ $pesanan->meja->nomor_meja ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500 dark:text-gray-400">Pelanggan</dt>
                    <dd class="font-medium text-gray-800 dark:text-white/90">{{ $pesanan->user->nama ?? 'Tamu' }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500 dark:text-gray-400">Dicatat oleh</dt>
                    <dd class="font-medium text-gray-800 dark:text-white/90">{{ $pesanan->petugas->nama ?? '-' }}</dd>
                </div>
                @if ($pesanan->alamat_antar)
                    <div class="sm:col-span-3">
                        <dt class="text-gray-500 dark:text-gray-400">Alamat Antar</dt>
                        <dd class="font-medium text-gray-800 dark:text-white/90">{{ $pesanan->alamat_antar }}</dd>
                    </div>
                @endif
            </dl>
        </x-common.component-card>

        <x-common.component-card title="Rincian Menu">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 text-gray-500 dark:border-gray-800 dark:text-gray-400">
                            <th class="py-3 pr-4 font-medium">Menu</th>
                            <th class="py-3 pr-4 font-medium">Catatan</th>
                            <th class="py-3 pr-4 font-medium">Harga</th>
                            <th class="py-3 pr-4 font-medium">Jumlah</th>
                            <th class="py-3 text-right font-medium">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pesanan->detail as $d)
                            <tr class="border-b border-gray-100 text-gray-800 dark:border-gray-800 dark:text-white/90">
                                <td class="py-3 pr-4 font-medium">{{ $d->menu->nama_menu }}</td>
                                <td class="py-3 pr-4 text-gray-500">{{ $d->catatan ?? '-' }}</td>
                                <td class="py-3 pr-4">Rp {{ number_format($d->harga_satuan, 0, ',', '.') }}</td>
                                <td class="py-3 pr-4">{{ $d->jumlah }}</td>
                                <td class="py-3 text-right">Rp {{ number_format($d->subtotal, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                        <tr class="text-gray-800 dark:text-white/90">
                            <td colspan="4" class="py-3 pr-4 text-right font-semibold">Total</td>
                            <td class="py-3 text-right font-semibold">Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </x-common.component-card>
        
             @if (auth()->user()->punyaRole('admin', 'kasir'))
                <x-common.component-card title="Pembayaran">
            @if ($pesanan->pembayaran)
                <dl class="grid grid-cols-1 gap-4 text-sm sm:grid-cols-4">
                    <div>
                        <dt class="text-gray-500 dark:text-gray-400">Status</dt>
                        <dd><span class="rounded-full bg-success-50 px-2.5 py-0.5 text-xs font-medium text-success-700">Lunas</span></dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 dark:text-gray-400">Metode</dt>
                        <dd class="font-medium uppercase text-gray-800 dark:text-white/90">{{ $pesanan->pembayaran->metode }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 dark:text-gray-400">Uang Diterima</dt>
                        <dd class="font-medium text-gray-800 dark:text-white/90">
                            Rp {{ number_format($pesanan->pembayaran->jumlah_bayar, 0, ',', '.') }}
                            @if ($pesanan->pembayaran->jumlah_bayar > $pesanan->total_harga)
                                <div class="text-xs text-gray-500">
                                    Kembalian Rp {{ number_format($pesanan->pembayaran->jumlah_bayar - $pesanan->total_harga, 0, ',', '.') }}
                                </div>
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 dark:text-gray-400">Waktu / Kasir</dt>
                        <dd class="font-medium text-gray-800 dark:text-white/90">
                            {{ $pesanan->pembayaran->waktu_bayar->format('d/m/Y H:i') }}
                            <div class="text-xs text-gray-500">{{ $pesanan->pembayaran->kasir->nama ?? '-' }}</div>
                        </dd>
                    </div>
                </dl>
            @elseif ($pesanan->status == 'dibatalkan')
                <p class="text-sm text-gray-500 dark:text-gray-400">Pesanan dibatalkan, tidak perlu dibayar.</p>
            @else
                <form method="POST" action="{{ route('pembayaran.store', $pesanan) }}" class="space-y-5"
                    x-data="{ metode: '{{ old('metode', 'tunai') }}', uang: {{ (int) old('jumlah_bayar', 0) }}, total: {{ (int) $pesanan->total_harga }} }">
                    @csrf
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Metode Pembayaran</label>
                            <select name="metode" x-model="metode" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                                <option value="tunai">Tunai</option>
                                <option value="qris">QRIS</option>
                                <option value="transfer">Transfer</option>
                            </select>
                        </div>
                        <div x-show="metode === 'tunai'">
                            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Uang Diterima (Rp)</label>
                            <input type="number" name="jumlah_bayar" min="0" x-model.number="uang" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                            @error('jumlah_bayar')
                                <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
                            @enderror
                            <p class="mt-1.5 text-sm" :class="uang >= total ? 'text-success-600' : 'text-gray-500'">
                                <span x-show="uang >= total">Kembalian: Rp <span x-text="(uang - total).toLocaleString('id-ID')"></span></span>
                                <span x-show="uang < total">Kurang: Rp <span x-text="(total - uang).toLocaleString('id-ID')"></span></span>
                            </p>
                        </div>
                    </div>
                    <button type="submit" class="h-11 rounded-lg bg-success-500 px-5 text-sm font-medium text-white hover:bg-success-600">
                        Bayar Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}
                    </button>
                </form>
            @endif
        </x-common.component-card>
        @endif
        @if (!in_array($pesanan->status, ['selesai', 'dibatalkan']))
            <x-common.component-card title="Ubah Status">
                <div class="flex flex-wrap gap-2">
                    @foreach (['dimasak' => 'Mulai Dimasak', 'siap' => 'Siap Disajikan', 'selesai' => 'Selesai', 'dibatalkan' => 'Batalkan'] as $nilai => $label)
                        @if ($nilai != $pesanan->status)
                            <form method="POST" action="{{ route('pesanan.status', $pesanan) }}"
                                @if ($nilai == 'dibatalkan') onsubmit="return confirm('Yakin batalkan pesanan ini?')" @endif>
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="{{ $nilai }}">
                                <button type="submit"
                                    class="rounded-lg px-4 py-2 text-sm font-medium {{ $nilai == 'dibatalkan' ? 'bg-error-500 text-white hover:bg-error-600' : 'bg-brand-500 text-white hover:bg-brand-600' }}">
                                    {{ $label }}
                                </button>
                            </form>
                        @endif
                    @endforeach
                </div>
            </x-common.component-card>
        @endif

        <a href="{{ route('pesanan.index') }}"
            class="inline-flex h-11 items-center rounded-lg border border-gray-300 px-5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300">
            &larr; Kembali ke daftar
        </a>
    </div>
@endsection