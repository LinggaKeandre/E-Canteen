<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Penjualan - E-Canteen</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            color: #333;
            margin: 10px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
        }
        .header p {
            margin: 5px 0 0;
            font-size: 11px;
            color: #666;
        }
        .info-section {
            margin-bottom: 15px;
            padding: 10px;
            background-color: #f5f5f5;
            border-radius: 5px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }
        .info-label {
            font-weight: bold;
            color: #555;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            font-size: 10px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 6px 8px;
            text-align: left;
        }
        th {
            background-color: #f5f5f5;
            font-weight: bold;
        }
        .total-row {
            font-weight: bold;
            background-color: #f9f9f9;
        }
        .summary {
            margin: 15px 0;
            padding: 15px;
            background-color: #f9f9f9;
            border-radius: 5px;
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }
        .footer {
            margin-top: 20px;
            text-align: right;
            font-size: 9px;
            color: #999;
        }
        .page-break {
            page-break-before: always;
        }
        .chart-table {
            margin-top: 20px;
        }
        .chart-table th {
            text-align: center;
        }
        .chart-table td {
            text-align: center;
        }
        .chart-table .revenue {
            background-color: rgba(40, 167, 69, 0.1);
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Laporan Penjualan</h1>
        <p>E-Canteen</p>
    </div>
    
    <div class="info-section">
        <div class="info-row">
            <span class="info-label">Periode:</span>
            <span>{{ $dateRangeDisplay }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Total Pendapatan:</span>
            <span>Rp {{ number_format($totalRevenue, 0, ',', '.') }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Total Porsi Terjual:</span>
            <span>{{ $totalSold }}</span>
        </div>
    </div>
    
    <!-- Daily Sales Chart (as table) -->
    <h3 style="margin-top: 20px; font-size: 12px;">Grafik Penjualan Harian</h3>
    <table class="chart-table">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Pendapatan</th>
                <th>Jml Pesanan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($dailySales as $sale)
                <tr class="revenue">
                    <td>{{ \Carbon\Carbon::parse($sale->date)->format('d M Y') }}</td>
                    <td>Rp {{ number_format($sale->revenue, 0, ',', '.') }}</td>
                    <td>{{ $sale->orders }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    
    <h3 style="margin-top: 20px; font-size: 12px;">Penjualan per Menu</h3>
    <table>
        <thead>
            <tr>
                <th style="width: 40px;">No</th>
                <th>Nama Menu</th>
                <th style="width: 120px;">Varian/Addons</th>
                <th style="width: 70px;">Harga</th>
                <th style="width: 60px;">Qty</th>
                <th style="width: 100px;">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($salesData as $index => $sale)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $sale['menu_name'] }}</td>
                    <td>{{ $sale['variant_addons'] }}</td>
                    <td style="text-align: right;">Rp {{ number_format($sale['price'], 0, ',', '.') }}</td>
                    <td style="text-align: center;">{{ $sale['total_qty'] }}</td>
                    <td style="text-align: right;">Rp {{ number_format($sale['total_subtotal'], 0, ',', '.') }}</td>
                </tr>
            @endforeach
            @if(count($salesData) > 0)
                <tr class="total-row">
                    <td colspan="4">Total</td>
                    <td style="text-align: center;">{{ $totalSold }}</td>
                    <td style="text-align: right;">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</td>
                </tr>
            @endif
        </tbody>
    </table>
    
    @if(count($salesData) == 0)
        <p style="text-align: center; color: #666; margin-top: 20px;">Tidak ada penjualan pada periode tersebut.</p>
    @endif
    
    <div class="footer">
        Dicetak pada: {{ date('d M Y H:i:s') }}
    </div>
</body>
</html>

