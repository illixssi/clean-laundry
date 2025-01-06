@extends('layouts.app')
@section('content')
@include('layouts.header', ['showButtons' => false])

<main class="app-main">
    <div class="app-content">
        <h3 class="mb-4 mt-4 ms-4 fw-bold">Ubah Transaksi</h3>
        <div class="container-fluid">
            <div class="mt-5 d-flex justify-content-center">
                <div class="p-4 form--container" style="max-width: 75vw; width: 100%;">
                    @include('transaksi.form', [
                    'action' => route('transaksi.update', $transaction->id),
                    'transaction' => $transaction,
                    'transactionDetails' => $transaction->details,
                    'customers' => $customers,
                    'services' => $services,
                    ])
                </div>
            </div>
        </div>
    </div>
</main>
@endsection