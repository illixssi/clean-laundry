<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Transaksi</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            font-size: 10px;
            /* Atur ukuran font default menjadi 10px */
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h1 {
            margin: 0;
            font-size: 14px;
        }

        .header p {
            margin: 0;
            font-size: 12px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 10px;
            /* Ukuran font tabel */
        }

        .table th,
        .table td {
            border: 1px solid #000;
            padding: 6px;
            text-align: center;
        }

        .table th {
            background-color: #f2f2f2;
            font-size: 10px;
            /* Ukuran font header tabel */
        }

        .total {
            font-weight: bold;
            text-align: right;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Clean Laundry</h1>
        <p>Tanggal: {{ $dateRange }}</p>
    </div>
    <table class="table">
        <thead>
            <tr>
                <th>No</th>
                <th>Nomor Transaksi</th>
                <th>Nama Pelanggan</th>
                <th>Tanggal Pesanan Dibuat</th>
                <th>Tanggal Selesai</th>
                <th>Status</th>
                <th>Total Harga</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transactions as $index => $transaction)
            <tr>
                <td>{{ $index + 1 }}</td> <!-- Tambahkan nomor urut -->
                <td>{{ $transaction->order_number }}</td>
                <td>{{ $transaction->customer->name }}</td>
                <td>{{ $transaction->created_at->format('d/m/Y') }}</td>
                <td>
                    @if($transaction->status === 'Selesai')
                    {{ $transaction->updated_at->format('d/m/Y') }}
                    @else
                    -
                    @endif
                </td>
                <td>{{ $transaction->status }}</td>
                <td>Rp {{ number_format($transaction->total_price, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="6" class="total">Total Keseluruhan</td>
                <td>Rp {{ number_format($transactions->sum('total_price'), 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>
</body>

</html>