<!DOCTYPE html>
<html>

@php
    $path = public_path('images/logo-text-v2.png');
    $type = pathinfo($path, PATHINFO_EXTENSION);
    $data = file_get_contents($path);
    $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
@endphp

<head>
    <title>Laporan Penjualan</title>
    <style>
        body {
            font-family: sans-serif;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .primary-text {
            color: #451a03;
        }

        /* Warna Primary Anda */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th {
            background-color: #f8f9fa;
            padding: 10px;
            font-size: 12px;
            text-align: left;
            border-bottom: 2px solid #451a03;
        }

        td {
            padding: 10px;
            font-size: 11px;
            border-bottom: 1px solid #eee;
        }

        .total-box {
            margin-top: 30px;
            padding: 15px;
            background: #ffffff;
            border: 1px solid #451a03;
            text-align: right;
        }
    </style>
</head>

<body>

    <div class="header">
        <div style="margin-bottom: 10px;">
            <img src="{{ $base64 }}" style="height: 150px; width: auto;">
        </div>
        <h4 class="primary-text">LAPORAN PENJUALAN</h4>
        <p style="font-size: 12px;">Periode: {{ $startDate }} s/d {{ $endDate }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Invoice</th>
                <th>Tanggal</th>
                <th>Staff</th>
                <th style="text-align: right;">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($orders as $order)
                <tr>
                    <td>#{{ $order->invoice_number }}</td>
                    <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                    <td>{{ $order->staff->name ?? 'Admin' }}</td>
                    <td style="text-align: right;">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total-box">
        <strong>Total Pendapatan: Rp {{ number_format($totalRevenue, 0, ',', '.') }}</strong>
    </div>
</body>

</html>
