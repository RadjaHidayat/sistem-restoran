@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Tambah Menu" />

    <x-common.component-card title="Tambah Menu">
        <form method="POST" action="{{ route('menu.store') }}" enctype="multipart/form-data">
            @csrf
            @include('pages.menu._form', ['menu' => null])
        </form>
    </x-common.component-card>
@endsection