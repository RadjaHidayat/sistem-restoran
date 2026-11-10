@extends('layouts.fullscreen-layout')

@section('content')
    <div class="flex min-h-screen flex-col items-center justify-center gap-6 bg-gray-50 p-6 print:bg-white">
        <div class="w-full max-w-sm rounded-3xl border-2 border-gray-200 bg-white p-8 text-center print:border-gray-400">
            <p class="text-sm uppercase tracking-widest text-gray-500">Meja</p>
            <p class="text-5xl font-bold text-gray-800">{{ $meja->nomor_meja }}</p>

            <div class="mx-auto my-6 w-64 [&>svg]:h-auto [&>svg]:w-full">
                {!! $svg !!}
            </div>

            <p class="text-lg font-semibold text-gray-800">Scan untuk pesan</p>
            <p class="mt-1 text-sm text-gray-500">Arahkan kamera HP ke kode di atas, pilih menu, lalu pesan langsung dari meja.</p>
            <p class="mt-4 break-all font-mono text-[10px] text-gray-400">{{ $url }}</p>
        </div>

        <div class="flex gap-3 print:hidden">
            <button type="button" onclick="window.print()"
                class="h-11 rounded-lg bg-brand-500 px-5 text-sm font-medium text-white hover:bg-brand-600">
                Cetak
            </button>
            <a href="{{ $url }}" target="_blank"
                class="flex h-11 items-center rounded-lg border border-gray-300 bg-white px-5 text-sm font-medium text-gray-700 hover:bg-gray-50">
                Coba buka halaman pesan
            </a>
            <a href="{{ route('meja.index') }}"
                class="flex h-11 items-center rounded-lg border border-gray-300 bg-white px-5 text-sm font-medium text-gray-700 hover:bg-gray-50">
                Kembali
            </a>
        </div>
    </div>
@endsection