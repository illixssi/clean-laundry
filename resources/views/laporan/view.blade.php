@extends('layouts.app')

@section('content')
<div class="container">
    <h3 class="mb-4 mt-4 fw-bold">Laporan</h3>
    <div class="mb-4">
        <label for="dateRange" class="form-label">Rentang Tanggal</label>
        <input type="text" name="daterange" class="form-control" placeholder="Pilih rentang tanggal" autocomplete="off" />
    </div>
    <div class="mb-3 text-end">
        <a href="{{ route('report.generatePDF') }}" class="btn btn-primary">Cetak PDF</a>
    </div>
</div>
@endsection

@push('styles')
<!-- Tambahkan CSS daterangepicker -->
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@endpush

@push('scripts')
<!-- Panggil jQuery dan moment.js terlebih dahulu -->
<script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<!-- Panggil daterangepicker setelah jQuery dan moment.js -->
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

<script>
    $(document).ready(function() {
        $('input[name="daterange"]').daterangepicker({
            opens: 'left',
            locale: {
                format: 'MM/DD/YYYY',
                applyLabel: "Terapkan",
                cancelLabel: "Batal"
            }
        });
    });
</script>
@endpush