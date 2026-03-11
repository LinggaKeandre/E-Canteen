@extends('layouts.master')

@section('title', 'Laporan Penjualan - E-Canteen')

@section('styles')
<style>
    .chart-container {
        position: relative;
        height: 250px;
    }
    .filter-card {
        border: none;
        background: #f8f9fa;
        border-radius: 8px;
    }
    .stat-inline {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 16px;
        background: #fff;
        border-radius: 6px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }
    .stat-inline .label {
        color: #6c757d;
        font-size: 13px;
    }
    .stat-inline .value {
        font-weight: 600;
        font-size: 16px;
    }
    .table-card {
        border: none;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }
    .export-btns {
        display: flex;
        gap: 8px;
    }
    .export-btns a {
        padding: 6px 12px;
        font-size: 13px;
    }
</style>
@endsection

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0"><i class="bi bi-bar-chart"></i> Laporan Penjualan</h2>
        <div class="export-btns">
            <a href="{{ route('admin.reports.export', ['period' => $period, 'start_date' => $startDate ?? '', 'end_date' => $endDate ?? '']) }}" class="btn btn-success btn-sm">
                <i class="bi bi-file-earmark-excel"></i> Excel
            </a>
            <a href="{{ route('admin.reports.exportPdf', ['period' => $period, 'start_date' => $startDate ?? '', 'end_date' => $endDate ?? '']) }}" class="btn btn-danger btn-sm">
                <i class="bi bi-file-earmark-pdf"></i> PDF
            </a>
        </div>
    </div>
    
    <!-- Period Filter -->
    <form method="GET" class="card filter-card mb-4">
        <div class="card-body">
            <div class="row align-items-end g-3">
                <div class="col-md-3">
                    <label for="period" class="form-label small fw-medium">Periode</label>
                    <select class="form-select form-select-sm" id="period" name="period" onchange="toggleCustomDates()">
                        <option value="today" {{ $period == 'today' ? 'selected' : '' }}>Hari Ini</option>
                        <option value="week" {{ $period == 'week' ? 'selected' : '' }}>7 Hari Terakhir</option>
                        <option value="month" {{ $period == 'month' ? 'selected' : '' }}>30 Hari Terakhir</option>
                        <option value="year" {{ $period == 'year' ? 'selected' : '' }}>1 Tahun Terakhir</option>
                        <option value="lifetime" {{ $period == 'lifetime' ? 'selected' : '' }}>Semua Waktu</option>
                        <option value="custom" {{ $period == 'custom' ? 'selected' : '' }}>Custom Tanggal</option>
                    </select>
                </div>
                <div class="col-md-3" id="startDateDiv" style="display: {{ $period == 'custom' ? 'block' : 'none' }}">
                    <label for="start_date" class="form-label small fw-medium">Tanggal Mulai</label>
                    <input type="date" class="form-control form-control-sm" id="start_date" name="start_date" value="{{ $startDate ?? '' }}">
                </div>
                <div class="col-md-3" id="endDateDiv" style="display: {{ $period == 'custom' ? 'block' : 'none' }}">
                    <label for="end_date" class="form-label small fw-medium">Tanggal Akhir</label>
                    <input type="date" class="form-control form-control-sm" id="end_date" name="end_date" value="{{ $endDate ?? '' }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary btn-sm w-100">
                        <i class="bi bi-search"></i> Filter
                    </button>
                </div>
            </div>
        </div>
    </form>
    
    <!-- Date Range Info -->
    <p class="text-muted mb-3">
        <i class="bi bi-calendar3"></i> {{ $dateRange }}
    </p>
    
    <!-- Inline Stats -->
    <div class="d-flex flex-wrap gap-3 mb-4">
        <div class="stat-inline">
            <span class="label">Pendapatan</span>
            <span class="value text-success">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</span>
        </div>
        <div class="stat-inline">
            <span class="label">Pesanan</span>
            <span class="value">{{ $totalOrders }}</span>
        </div>
        <div class="stat-inline">
            <span class="label">Porsi Terjual</span>
            <span class="value">{{ $totalSold }}</span>
        </div>
    </div>
    
    <!-- Chart Section -->
    @if($dailySales->isNotEmpty() || $period != 'today')
    <div class="card table-card mb-4">
        <div class="card-body">
            <h6 class="mb-3"><i class="bi bi-graph-up"></i> Grafik Penjualan Harian</h6>
            <div class="chart-container">
                <canvas id="salesChart"></canvas>
            </div>
        </div>
    </div>
    @endif
    
    <!-- Sales by Menu Table -->
    <div class="card table-card">
        <div class="card-body">
            <h6 class="mb-3">Penjualan per Menu</h6>
            @if($salesByMenu->isEmpty())
                <p class="text-muted text-center py-4 mb-0">Tidak ada penjualan pada periode tersebut.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Menu</th>
                                <th>Varian/Addons</th>
                                <th>Harga</th>
                                <th>Qty</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                            $itemsList = [];
                            foreach($salesByMenu as $sale) {
                                $itemsList[] = $sale;
                            }
                            // If we have orderItems available, use them for variant/addons display
                            $orderItems = isset($orders) ? $orders->flatMap->orderItems : collect();
                            @endphp
                            
                            @if($orderItems->isNotEmpty())
                                @foreach($orderItems as $index => $item)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $item->menu->name ?? 'Unknown' }}</td>
                                    <td>
                                        @if($item->variant_name)
                                            <span class="badge bg-warning text-dark">{{ $item->variant_name }}</span>
                                        @endif
                                        @if(!empty($item->addons))
                                            @foreach($item->addons as $addon)
                                                <span class="badge bg-secondary">+{{ $addon['name'] }}</span>
                                            @endforeach
                                        @endif
                                        @if(!$item->variant_name && empty($item->addons))
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                                    <td>{{ $item->qty }}</td>
                                    <td class="text-end">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                </tr>
                                @endforeach
                            @else
                                @foreach($salesByMenu as $index => $sale)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $sale->menu_name }}</td>
                                    <td><span class="text-muted">-</span></td>
                                    <td>-</td>
                                    <td>{{ $sale->total_qty }}</td>
                                    <td class="text-end">Rp {{ number_format($sale->total_subtotal, 0, ',', '.') }}</td>
                                </tr>
                                @endforeach
                            @endif
                        </tbody>
                        <tfoot>
                            <tr class="fw-bold">
                                <td colspan="4">Total</td>
                                <td>{{ $totalSold }}</td>
                                <td class="text-end">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    function toggleCustomDates() {
        const period = document.getElementById('period').value;
        const startDateDiv = document.getElementById('startDateDiv');
        const endDateDiv = document.getElementById('endDateDiv');
        
        if (period === 'custom') {
            startDateDiv.style.display = 'block';
            endDateDiv.style.display = 'block';
        } else {
            startDateDiv.style.display = 'none';
            endDateDiv.style.display = 'none';
        }
    }
    
    @if($dailySales->isNotEmpty() || $period != 'today')
    const ctx = document.getElementById('salesChart').getContext('2d');
    const salesData = {
        labels: {!! json_encode($dailySales->pluck('date')->map(function($date) { return \Carbon\Carbon::parse($date)->format('d M'); })) !!},
        datasets: [{
            label: 'Pendapatan (Rp)',
            data: {!! json_encode($dailySales->pluck('revenue')) !!},
            backgroundColor: 'rgba(40, 167, 69, 0.1)',
            borderColor: 'rgba(40, 167, 69, 1)',
            borderWidth: 2,
            fill: true,
            tension: 0.3
        }, {
            label: 'Pesanan',
            data: {!! json_encode($dailySales->pluck('orders')) !!},
            backgroundColor: 'rgba(0, 123, 255, 0.1)',
            borderColor: 'rgba(0, 123, 255, 1)',
            borderWidth: 2,
            fill: false,
            tension: 0.3,
            yAxisID: 'y1'
        }]
    };
    
    new Chart(ctx, {
        type: 'line',
        data: salesData,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            plugins: {
                legend: {
                    position: 'top',
                }
            },
            scales: {
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    title: {
                        display: true,
                        text: 'Pendapatan'
                    },
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + value.toLocaleString('id-ID');
                        }
                    }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    title: {
                        display: true,
                        text: 'Pesanan'
                    },
                    grid: {
                        drawOnChartArea: false
                    }
                }
            }
        }
    });
    @endif
</script>
@endpush
@endsection

