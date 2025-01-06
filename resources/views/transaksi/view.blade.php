@extends('layouts.app')
@section('content')
@include('layouts.header', ['showButtons' => false])

<main class="app-main">
    <div class="app-content">
        <h3 class="mb-4 mt-4 ms-4 fw-bold">Detil Transaksi</h3>
        <div class="container-fluid">
            <div class="mt-5 d-flex justify-content-center">
                <div class="p-4 form--container d-flex justify-content-between" style="max-width: 70%; width: 100%;">
                    <!-- Transaction Details -->
                    <div class="transaction-details">
                        <div class="detail-row">
                            <div class="detail-label">Nama Pelanggan</div>
                            <div class="detail-value">{{ $transaction->customer->name ?? '-' }}</div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Kontak</div>
                            <div class="detail-value">
                                <p>{{ $transaction->customer->phone_number ?? '-' }}</p>
                                <p>{{ $transaction->customer->address ?? '-' }}</p>
                            </div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Jumlah Pakaian</div>
                            <div class="detail-value">
                                {{ $transaction->clothes_quantity ?? '-' }} pcs
                            </div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Status</div>
                            <div id="status-section">
                                <div class="status-box disabled--style" id="status-display">
                                    {{ $transaction->status ?? 'Null' }}
                                </div>
                                <select id="status-dropdown" class="form-select" style="display: none;">
                                    @php
                                    $statuses = explode(',', env('STATUS_OPTIONS', 'ERROR'));
                                    @endphp
                                    @foreach($statuses as $status)
                                    <option value="{{ $status }}" {{ $transaction->status === $status ? 'selected' : '' }}>
                                        {{ $status }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mid--section d-flex align-items-end">
                        @if(in_array($roleName, ['admin', 'kepala_operasional', 'kasir', 'staf_laundry']))
                        <button type="button" class="btn btn-primary me-2" id="edit-status-button"
                            onclick="enableStatusEdit()">Ubah Status</button>
                        <button type="button" class="btn btn-secondary me-2" id="back-status-button" style="display: none;" onclick="normal()">Kembali</button>
                        <form id="updateForm" method="POST" action="{{ route('transaksi.updateStatus', $transaction->id) }}" class="status--form--container">
                            @csrf
                            @method('POST')
                            <input type="hidden" name="status" id="status-hidden"> <!-- Pastikan elemen ini ada -->
                            <button type="button" class="btn btn-primary" id="ok-status-button" style="display: none;" onclick="submitForm()">Simpan</button>
                        </form>
                        @endif
                    </div>

                    <!-- Right-Side Section -->
                    <div class="right-section">
                        <div class="notes--container">
                            <label for="notes" class="form-label">Catatan</label>
                            <textarea class="form-control disabled--style" rows="4" disabled>{{ $transaction->notes ?? '' }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-center mt-4">
                <table class="table table-bordered text-center" style="max-width: 80%;">
                    <thead>
                        <tr>
                            <th>Layanan</th>
                            <th>Kuantitas/Satuan</th>
                            <th>Harga</th>
                        </tr>
                    </thead>
                    <tbody id="serviceTableBody">
                        @if(isset($transaction->details) && $transaction->details->isNotEmpty())
                        @foreach($transaction->details as $index => $detail)
                        <tr data-index="{{ $index }}">
                            <td>{{ $detail->service->service_name }}</td>
                            <td>{{ $detail->quantity }} {{ $detail->service->unit }}</td>
                            <td>Rp {{ number_format($detail->price, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
            <!-- Total Harga -->
            <div class="d-flex justify-content-end align-items-center mt-4" style="max-width: 90%;">
                <div class="total-harga">
                    <div class="total--harga--text">
                        @php
                        $existingTotalPrice = isset($transaction->details) ? $transaction->details->sum('price') : 0;
                        @endphp
                        <input type="hidden" id="totalPriceInput" name="total_price" value="{{ $existingTotalPrice }}">
                        Total Harga: <span class="text-primary" id="totalPrice">Rp {{ number_format($existingTotalPrice, 0, ',', '.') }}</span>
                    </div>
                    <div class="transaksi--button--group">
                        <a href="{{ route('transaksi') }}" class="btn btn-danger me-2">Kembali</a>
                        @if(in_array($roleName, ['admin', 'kepala_operasional', 'kasir']))
                        <!-- <button>
                            <a href="{{ route('transaksi.print', $transaction->id) }}" class="btn btn-primary" target="_blank">Cetak Invoice</a>
                        </button> -->
                        <button type="submit" class="btn btn-primary" onclick="cetakInvoice()">Cetak Invoice</button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="print-area" style="display: none;">
        <div class="container">
            <img src="{{ asset('assets/img/CleanLaundryLogo.png') }}" alt="Clean Laundry" width="100">

            <h1>Clean Laundry</h1>
            <span style="white-space: pre-line; font-size: 12px">
                Jl. Istiqomah RT01/RW08 No. 41
                Kelurahan Cipadu, Kecamatan Larangan
                Kota Tangerang, Banten
            </span>

            <div class="customer-data">
                <pre style="font-size: 10px; text-align: left; margin: 0;">
<hr>
OrderNo:   {{$transaction->order_number}}
Nama:      {{$transaction->customer->name}}
Tanggal:   {{$transaction->created_at->format('d/m/Y H:i')}}
Jumlah:    {{$transaction->clothes_quantity}} pcs
                </pre>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Layanan</th>
                        <th>Harga</th>
                        <th>Qty</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transaction->details as $detail)
                    <tr>
                        <td>{{ $detail->service->service_name }}</td>
                        <td>Rp {{ number_format($detail->service->price, 0, ',', '.') }}</td>
                        <td>{{ $detail->quantity.$detail->service->unit }}</td>
                        <td>Rp {{ number_format($detail->price, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="total">
                Total Harga: Rp {{ number_format($transaction->details->sum(fn($d) => $d->price), 0, ',', '.') }}
            </div>

        </div>

        <footer>
            <span style="white-space: pre-line; font-size: 9px">
                Terima kasih sudah memakai jasa Clean Laundry
                Semoga Anda puas dengan layanan kami!
            </span>
        </footer>
    </div>
</main>

@endsection

<style>
    .status--form--container {
        display: flex;
        justify-content: start;
    }

    .form--container {
        background-color: white;
        display: flex;
        gap: 20px;
        min-width: 350px;
    }

    .transaction-details {
        display: flex;
        flex-direction: column;
        max-width: 40%;
        width: 100%;
        font-family: Arial, sans-serif;
        font-size: 1rem;
    }

    .right-section {
        max-width: 25%;
        width: 100%;
    }

    .right-section {
        max-width: 15%;
        width: 100%;
    }

    .detail-row {
        display: flex;
        margin-bottom: 10px;
        align-items: center;
    }

    .detail-label {
        font-size: 1rem;
        font-weight: bold;
        width: 35%;
    }

    .detail-value {
        flex: 1;
        padding-left: 10px;
    }

    .status-box {
        background-color: #c0c0c0;
        color: #333;
        padding: 5px 15px;
        border-radius: 5px;
        font-weight: bold;
        text-align: center;
        display: inline-block;
        margin-left: 10px;
    }

    .mid--section {
        max-width: 50%;
        width: 100%;
    }

    .notes--container {
        margin-bottom: 20px;
    }

    .button--group {
        justify-content: flex-start;
    }

    form#updateForm {
        margin: 0 !important;
        padding: 0;
    }

    /* Responsive adjustments */
    @media (max-width: 733px) {
        .form--container {
            flex-direction: column;
        }

        .transaction-details,
        .right-section,
        .mid--section {
            max-width: 100%;
        }

        .button--group {
            justify-content: center;
        }
    }
</style>

<script>
    function enableStatusEdit() {
        document.getElementById('status-display').style.display = 'none';
        document.getElementById('status-dropdown').style.display = 'inline';
        document.getElementById('status-dropdown').disabled = false;

        document.getElementById('edit-status-button').style.display = 'none';
        document.getElementById('back-status-button').style.display = 'inline';
        document.getElementById('ok-status-button').style.display = 'inline';
    }

    function normal() {
        document.getElementById('status-display').style.display = 'inline';
        document.getElementById('status-dropdown').style.display = 'none';
        document.getElementById('status-dropdown').disabled = true;

        document.getElementById('edit-status-button').style.display = 'inline';
        document.getElementById('back-status-button').style.display = 'none';
        document.getElementById('ok-status-button').style.display = 'none';
    }

    function submitForm() {
        const statusHiddenInput = document.getElementById('status-hidden');
        const statusDropdown = document.getElementById('status-dropdown');

        if (statusHiddenInput && statusDropdown) {
            statusHiddenInput.value = statusDropdown.value;
            document.getElementById('updateForm').submit();
        } else {
            console.error("Elemen 'status-hidden' atau 'status-dropdown' tidak ditemukan.");
        }
    }

    function cetakInvoice() {
        var printContent = document.getElementById('print-area').innerHTML;
        var originalContent = document.body.innerHTML;
        document.body.innerHTML = printContent;
        window.print();
        document.body.innerHTML = originalContent;
        window.location.reload();
    }
</script>