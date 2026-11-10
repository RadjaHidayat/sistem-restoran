@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Tambah Reservasi" />

    <x-common.component-card title="Tambah Reservasi">
        <form method="POST" action="{{ route('reservasi.store') }}">
            @csrf
            @include('pages.reservasi._form', ['reservasi' => null])
        </form>
    </x-common.component-card>
@endsection