{{-- Bagian bawah tabel: pilihan jumlah data per halaman + tombol pindah halaman --}}
@if ($data->total() > 0)
    <div class="flex flex-col gap-3 border-t border-gray-100 pt-4 text-sm text-gray-600 sm:flex-row sm:items-center sm:justify-between dark:border-gray-800 dark:text-gray-400">
        <form method="GET" class="flex items-center gap-2">
            {{-- Bawa filter yang sedang aktif (status, tanggal, tab) supaya tidak hilang --}}
            @foreach (request()->except(['per_halaman', 'page']) as $nama => $nilai)
                @if (is_string($nilai))
                    <input type="hidden" name="{{ $nama }}" value="{{ $nilai }}">
                @endif
            @endforeach
            Tampilkan
            <select name="per_halaman" onchange="this.form.submit()"
                class="h-9 rounded-lg border border-gray-300 bg-transparent px-2 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                @foreach ([15, 50, 100] as $n)
                    <option value="{{ $n }}" @selected($data->perPage() == $n)>{{ $n }}</option>
                @endforeach
            </select>
            data per halaman
        </form>

        <div class="flex flex-wrap items-center gap-3">
            <span>{{ $data->firstItem() }}–{{ $data->lastItem() }} dari {{ $data->total() }} data</span>

            @if ($data->lastPage() > 1)
                @php
                    $tombol = 'flex h-9 min-w-9 items-center justify-center rounded-lg border px-3';
                    $biasa = 'border-gray-300 text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300';
                    $aktif = 'border-brand-500 bg-brand-500 text-white';
                    $mati = 'border-gray-200 text-gray-300 dark:border-gray-800 dark:text-gray-600';
                    $mulai = max(1, $data->currentPage() - 2);
                    $akhir = min($data->lastPage(), $data->currentPage() + 2);
                @endphp
                <div class="flex items-center gap-1">
                    @if ($data->onFirstPage())
                        <span class="{{ $tombol }} {{ $mati }}">&lsaquo;</span>
                    @else
                        <a href="{{ $data->previousPageUrl() }}" class="{{ $tombol }} {{ $biasa }}">&lsaquo;</a>
                    @endif

                    @for ($i = $mulai; $i <= $akhir; $i++)
                        <a href="{{ $data->url($i) }}" class="{{ $tombol }} {{ $i == $data->currentPage() ? $aktif : $biasa }}">{{ $i }}</a>
                    @endfor

                    @if ($data->hasMorePages())
                        <a href="{{ $data->nextPageUrl() }}" class="{{ $tombol }} {{ $biasa }}">&rsaquo;</a>
                    @else
                        <span class="{{ $tombol }} {{ $mati }}">&rsaquo;</span>
                    @endif
                </div>
            @endif
        </div>
    </div>
@endif