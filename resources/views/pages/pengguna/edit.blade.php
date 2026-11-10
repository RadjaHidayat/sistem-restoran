@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Edit Pengguna" />

    <x-common.component-card title="Edit Pengguna">
        <form method="POST" action="{{ route('pengguna.update', $pengguna) }}">
            @csrf
            @method('PUT')
            @include('pages.pengguna._form')
        </form>
    </x-common.component-card>
@endsection