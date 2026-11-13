{{-- Kotak pencarian. Contoh pakai: @include('pages._cari', ['placeholder' => 'Cari nama menu...']) --}}
<form method="GET" class="relative w-full sm:w-72">
    {{-- Bawa filter lain yang sedang aktif (tab, role, kategori, jumlah per halaman) --}}
    @foreach (request()->except(['cari', 'page']) as $nama => $nilai)
        @if (is_string($nilai))
            <input type="hidden" name="{{ $nama }}" value="{{ $nilai }}">
        @endif
    @endforeach

    <input type="text" name="cari" value="{{ request('cari') }}" placeholder="{{ $placeholder ?? 'Cari...' }}"
        class="h-10 w-full rounded-lg border border-gray-300 bg-transparent pr-9 pl-9 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
    <svg class="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35M17 10.5a6.5 6.5 0 1 1-13 0 6.5 6.5 0 0 1 13 0Z" />
    </svg>

    {{-- Tombol x untuk menghapus pencarian --}}
    @if (request()->filled('cari'))
        <a href="{{ request()->fullUrlWithQuery(['cari' => null, 'page' => null]) }}"
            class="absolute top-1/2 right-3 -translate-y-1/2 text-gray-400 hover:text-gray-600" title="Hapus pencarian">&times;</a>
    @endif
</form>