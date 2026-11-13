@php
    $metaTitle = ($title ?? 'Dashboard') . ' | ' . config('app.name');
    $metaDescription = 'Sistem layanan kuliner dan restoran ' . config('app.name');
@endphp
<title>{{ $metaTitle }}</title>
<meta name="description" content="{{ $metaDescription }}">
