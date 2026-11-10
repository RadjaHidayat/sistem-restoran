@php
    $warna = match ($status) {
        'menunggu' => 'bg-warning-50 text-warning-700',
        'dikonfirmasi' => 'bg-brand-50 text-brand-600',
        'selesai' => 'bg-success-50 text-success-700',
        'dibatalkan' => 'bg-error-50 text-error-700',
        default => 'bg-gray-100 text-gray-700',
    };
@endphp
<span class="rounded-full px-2.5 py-0.5 text-xs font-medium capitalize {{ $warna }}">{{ $status }}</span>