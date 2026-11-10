@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Edit Menu" />

    <x-common.component-card title="Edit Menu">
        <form method="POST" action="{{ route('menu.update', $menu) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @include('pages.menu._form')
        </form>
    </x-common.component-card>
@endsection