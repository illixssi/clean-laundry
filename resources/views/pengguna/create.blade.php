@extends('layouts.app')

@section('content')
@include('layouts.header', ['showButtons' => false])

<main class="app-main">
    <div class="app-content">
        <h3 class="mb-4 mt-4 ms-4 fw-bold">Tambah Data Pengguna</h3>
        <div class="container-fluid">
            <div class="mt-5 d-flex justify-content-center">
                <div class="p-4" style="max-width: 600px; width: 100%;">
                    @include('pengguna.form')
                </div>
            </div>
        </div>
    </div>
</main>
@endsection