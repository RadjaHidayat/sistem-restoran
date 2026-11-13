@extends('layouts.pelanggan')

@section('isi')
    <div class="mx-auto max-w-xl rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
        <a href="{{ route('akun.index') }}" class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400">&larr; Kembali ke Beranda</a>

        <h1 class="mt-4 text-2xl font-semibold text-gray-800 dark:text-white/90">Buat Reservasi</h1>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
            Restoran buka pukul 10.00 sampai 21.00. Meja akan dipilihkan oleh staf kami, dan statusnya bisa kamu lihat di Akun Saya.
        </p>

        <form method="POST" action="{{ route('akun.reservasi.simpan') }}" class="mt-6 space-y-5">
            @csrf

            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Tanggal</label>
                    <input type="date" name="tanggal" value="{{ old('tanggal') }}"
                        min="{{ now()->toDateString() }}" max="{{ now()->addDays(30)->toDateString() }}" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                    @error('tanggal')
                        <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Jam</label>
                    <input type="time" name="jam" value="{{ old('jam', '19:00') }}" min="10:00" max="21:00" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                    @error('jam')
                        <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Jumlah Orang</label>
                <input type="number" name="jumlah_orang" min="1" max="20" value="{{ old('jumlah_orang', 2) }}" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                @error('jumlah_orang')
                    <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Catatan (opsional)</label>
                <textarea name="catatan" rows="2" placeholder="Contoh: ulang tahun, minta dekat jendela" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">{{ old('catatan') }}</textarea>
            </div>

            <button type="submit" class="h-11 w-full rounded-lg bg-brand-500 text-sm font-medium text-white hover:bg-brand-600">
                Kirim Reservasi
            </button>
        </form>
    </div>
@endsection