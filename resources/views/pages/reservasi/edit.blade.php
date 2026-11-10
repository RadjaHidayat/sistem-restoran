@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Edit Reservasi" />

    <x-common.component-card title="Edit Reservasi">
        <form method="POST" action="{{ route('reservasi.update', $reservasi) }}">
            @csrf
            @method('PUT')
            @include('pages.reservasi._form')
        </form>
    </x-common.component-card>
@endsection