@extends('layouts.app')
@section('content')
@include('layouts.header')
@include('layouts.sidebar')

<main class="app-main">
    <div class="app-content">
        <div class="container-fluid">
            <div class="mt-5">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4 search-actions-container">
                        <form action="{{ route('laporan') }}" method="GET" id="searchForm" class="date--picker--container input-group">
                            <span class="input-group-text">
                                <i class="fa fa-calendar" aria-hidden="true"></i>
                            </span>
                            @php
                            use Carbon\Carbon;
                            $yesterday = Carbon::yesterday()->format('d/m/Y');
                            $today = Carbon::today()->format('d/m/Y');
                            $dateRange = request('date_range', $yesterday . ' - ' . $today);
                            @endphp

                            <input type="text" name="date_range" class="form-control date--picker" placeholder="Search" aria-label="Search"
                                value="{{$dateRange}}" />
                        </form>
                        @if(in_array($roleName, ['admin', 'owner']))
                        <a href="#" id="btnGeneratePDF" class="btn btn-primary">Cetak PDF</a>
                        @endif
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Nomor Transaksi</th>
                                    <th>Nama Pelanggan</th>
                                    <th>Tanggal Pesanan Dibuat</th>
                                    <th>Total Harga</th>
                                    <th>Status</th>
                                    <th>Tanggal Selesai</th>
                                </tr>
                            </thead>
                            <tbody id="reportTableBody">
                                @foreach($transactions as $transaction)
                                <tr>
                                    <td>{{ $transaction->order_number }}</td>
                                    <td>{{ $transaction->customer->name }}</td>
                                    <td>{{ $transaction->created_at->format('d/m/Y') }}</td>
                                    <td>Rp {{ number_format($transaction->total_price, 0, ',', '.') }}</td>
                                    <td>{{ $transaction->status }}</td>
                                    <td>
                                        @if($transaction->status === 'Selesai')
                                        {{ $transaction->updated_at->format('d/m/Y') }}
                                        @else
                                        -
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="card-footer clearfix pagination--container">
                        {{ $transactions->appends(['date_range' => request('date_range')])->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

@endsection

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
@endpush

@push('scripts')
<script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

<script>
    $(function() {
        $('#btnGeneratePDF').on('click', function(e) {
            e.preventDefault(); // Mencegah navigasi default
            const dateRange = $('input[name="date_range"]').val();
            if (dateRange) {
                const pdfUrl = `{{ route('report.generatePDF') }}?date_range=${encodeURIComponent(dateRange)}`;
                window.location.href = pdfUrl; // Navigasi ke URL dengan parameter
            } else {
                alert('Silakan pilih rentang tanggal terlebih dahulu.');
            }
        });

        $('input[name="date_range"]').daterangepicker({
            opens: 'left',
            autoUpdateInput: false,
            locale: {
                format: 'DD/MM/YYYY', // Pastikan format ini sesuai dengan yang diterima backend
                cancelLabel: 'Clear'
            }
        });

        // Event ketika pengguna mengklik "Apply"
        $('input[name="date_range"]').on('apply.daterangepicker', function(ev, picker) {
            // Set nilai input dengan format yang sesuai
            $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
            // Submit form secara otomatis
            $('#searchForm').submit();
        });

        // Event ketika pengguna mengklik "Clear"
        $('input[name="date_range"]').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
            $('#searchForm').submit(); // Reset data saat dibersihkan
        });
    });
</script>
@endpush