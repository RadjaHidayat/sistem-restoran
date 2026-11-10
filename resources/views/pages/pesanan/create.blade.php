@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Buat Pesanan" />

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

    <form method="POST" action="{{ route('pesanan.store') }}" class="space-y-6"
        x-data="{ tipe: '{{ old('tipe_pesanan', 'dine_in') }}' }">
        @csrf

        <x-common.component-card title="Info Pesanan">
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Tipe Pesanan</label>
                    <select name="tipe_pesanan" x-model="tipe" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                        <option value="dine_in">Dine In (makan di tempat)</option>
                        <option value="takeaway">Takeaway (bawa pulang)</option>
                        <option value="delivery">Delivery (diantar)</option>
                    </select>
                    @error('tipe_pesanan')
                        <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
                    @enderror
                </div>

                <div x-show="tipe === 'dine_in'">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Meja</label>
                    <select name="id_meja" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                        <option value="">-- Pilih meja --</option>
                        @foreach ($meja as $m)
                            <option value="{{ $m->id_meja }}" @selected(old('id_meja') == $m->id_meja)>
                                Meja {{ $m->nomor_meja }} ({{ $m->kapasitas }} orang){{ $m->status == 'dipesan' ? ' - sudah direservasi' : '' }}
                            </option>
                        @endforeach
                    </select>
                    @error('id_meja')
                        <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Pelanggan (opsional)</label>
                    <select name="id_user" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                        <option value="">-- Tamu / tidak terdaftar --</option>
                        @foreach ($pelanggan as $p)
                            <option value="{{ $p->id_user }}" @selected(old('id_user') == $p->id_user)>{{ $p->nama }}</option>
                        @endforeach
                    </select>
                </div>

                <div x-show="tipe === 'delivery'" class="sm:col-span-2">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Alamat Antar</label>
                    <textarea name="alamat_antar" rows="2" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">{{ old('alamat_antar') }}</textarea>
                    @error('alamat_antar')
                        <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </x-common.component-card>

        <x-common.component-card title="Pilih Menu" desc="Isi jumlah untuk menu yang dipesan. Biarkan 0 untuk menu yang tidak dipesan.">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 text-gray-500 dark:border-gray-800 dark:text-gray-400">
                            <th class="py-3 pr-4 font-medium">Menu</th>
                            <th class="py-3 pr-4 font-medium">Kategori</th>
                            <th class="py-3 pr-4 font-medium">Harga</th>
                            <th class="w-24 py-3 pr-4 font-medium">Jumlah</th>
                            <th class="py-3 font-medium">Catatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($menu as $item)
                            <tr class="border-b border-gray-100 text-gray-800 dark:border-gray-800 dark:text-white/90">
                                <td class="py-3 pr-4 font-medium">{{ $item->nama_menu }}</td>
                                <td class="py-3 pr-4">{{ $item->kategori->nama_kategori }}</td>
                                <td class="py-3 pr-4">Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                                <td class="py-3 pr-4">
                                    <input type="number" min="0" name="jumlah[{{ $item->id_menu }}]"
                                        value="{{ old('jumlah.' . $item->id_menu, 0) }}" class="h-9 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                                </td>
                                <td class="py-3">
                                    <input type="text" name="catatan[{{ $item->id_menu }}]" placeholder="Contoh: tidak pedas"
                                        value="{{ old('catatan.' . $item->id_menu) }}" class="h-9 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-6 text-center text-gray-500">Belum ada menu yang tersedia.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-common.component-card>

        <div class="flex gap-3">
            <button type="submit" class="h-11 rounded-lg bg-brand-500 px-5 text-sm font-medium text-white hover:bg-brand-600">
                Simpan Pesanan
            </button>
            <a href="{{ route('pesanan.index') }}"
                class="flex h-11 items-center rounded-lg border border-gray-300 px-5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300">
                Batal
            </a>
        </div>
    </form>
@endsection