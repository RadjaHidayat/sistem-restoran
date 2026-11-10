@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Tambah Pengguna" />

    <x-common.component-card title="Tambah Pengguna">
        <form method="POST" action="{{ route('pengguna.store') }}">
            @csrf
            @include('pages.pengguna._form', ['pengguna' => null])
        </form>
    </x-common.component-card>
@endsection