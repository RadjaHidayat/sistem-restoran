@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Reservasi" />

    @if (session('success'))
        <div class="mb-6 rounded-lg border border-success-500 bg-success-50 px-4 py-3 text-sm text-success-700">
            {{ session('success') }}
        </div>
    @endif

    <x-common.component-card title="Daftar Reservasi">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex gap-2">
                @foreach (['mendatang' => 'Mendatang', 'riwayat' => 'Riwayat'] as $nilai => $label)
                    <a href="{{ route('reservasi.index', ['tab' => $nilai]) }}"
                        class="rounded-lg px-3 py-1.5 text-xs font-medium {{ $tab == $nilai ? 'bg-brand-500 text-white' : 'border border-gray-300 text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>
    @include('pages._cari', ['placeholder' => 'Cari nama pelanggan / meja...'])
            <a href="{{ route('reservasi.create') }}"
                class="inline-flex h-11 items-center rounded-lg bg-brand-500 px-5 text-sm font-medium text-white hover:bg-brand-600">
                + Tambah Reservasi
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-gray-100 text-gray-500 dark:border-gray-800 dark:text-gray-400">
                        <th class="py-3 pr-4 font-medium">Tanggal</th>
                        <th class="py-3 pr-4 font-medium">Jam</th>
                        <th class="py-3 pr-4 font-medium">Pelanggan</th>
                        <th class="py-3 pr-4 font-medium">Jumlah Orang</th>
                        <th class="py-3 pr-4 font-medium">Meja</th>
                        <th class="py-3 pr-4 font-medium">Status</th>
                        <th class="py-3 font-medium">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($reservasi as $item)
                        <tr class="border-b border-gray-100 text-gray-800 dark:border-gray-800 dark:text-white/90">
                            <td class="py-3 pr-4">{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                            <td class="py-3 pr-4">{{ substr($item->jam, 0, 5) }}</td>
                            <td class="py-3 pr-4">
                                <div class="font-medium">{{ $item->user->nama }}</div>
                                @if ($item->catatan)
                                    <div class="text-xs text-gray-500">{{ Str::limit($item->catatan, 40) }}</div>
                                @endif
                            </td>
                            <td class="py-3 pr-4">{{ $item->jumlah_orang }} orang</td>
                            <td class="py-3 pr-4">
                                @if ($item->meja)
                                    {{ $item->meja->nomor_meja }}
                                @else
                                    <span class="text-xs text-warning-700">Belum dipilih</span>
                                @endif
                            </td>
                            <td class="py-3 pr-4">@include('pages.reservasi._status', ['status' => $item->status])</td>
                            <td class="py-3">
                                <a href="{{ route('reservasi.edit', $item) }}"
                                    class="rounded-lg border border-gray-300 px-3 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300">
                                    Edit
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-6 text-center text-gray-500">
                                {{ $tab == 'mendatang' ? 'Tidak ada reservasi yang akan datang.' : 'Belum ada riwayat reservasi.' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @include('pages._paginasi', ['data' => $reservasi])
    </x-common.component-card>
@endsection