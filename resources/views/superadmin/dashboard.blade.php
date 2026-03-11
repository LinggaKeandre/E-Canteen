@extends('layouts.master')

@section('title', 'Dashboard Superadmin - E-Canteen')

@section('styles')
<style>
    .stat-card {
        border: 1px solid #dee2e6;
        border-radius: 8px;
    }
    .stat-card:hover {
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    .table-card {
        border: 1px solid #dee2e6;
        border-radius: 8px;
    }
    .badge-pending { background-color: #ffc107; color: #000; }
    .badge-approved { background-color: #28a745; color: #fff; }
    .badge-rejected { background-color: #dc3545; color: #fff; }
</style>
@endsection

@section('content')
<div class="container-fluid py-4 px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-speedometer2"></i> Dashboard Superadmin</h2>
        <div class="btn-group">
            <a href="?period=day" class="btn btn-sm {{ $period === 'day' ? 'btn-primary' : 'btn-outline-primary' }}">Hari Ini</a>
            <a href="?period=week" class="btn btn-sm {{ $period === 'week' ? 'btn-primary' : 'btn-outline-primary' }}">Minggu Ini</a>
            <a href="?period=month" class="btn btn-sm {{ $period === 'month' ? 'btn-primary' : 'btn-outline-primary' }}">Bulan Ini</a>
            <a href="?period=year" class="btn btn-sm {{ $period === 'year' ? 'btn-primary' : 'btn-outline-primary' }}">Tahun Ini</a>
        </div>
    </div>

    <!-- Stats Cards - Simple Design -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="text-muted small mb-1">Total Pengguna</div>
                    <h3 class="mb-0">{{ number_format($totalUsers) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="text-muted small mb-1">Total Toko</div>
                    <h3 class="mb-0">{{ number_format($totalSellers) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="text-muted small mb-1">Pesanan (Periode)</div>
                    <h3 class="mb-0">{{ number_format($ordersInPeriod) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="text-muted small mb-1">Pendapatan (Periode)</div>
                    <h3 class="mb-0">Rp {{ number_format($revenueInPeriod, 0, ',', '.') }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Today's Stats & Pending Requests -->
    <div class="row mb-4">
        <div class="col-md-6 mb-3">
            <div class="card table-card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-calendar-check"></i> Hari Ini</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-3">
                        <div class="text-center">
                            <div class="h4 mb-1">{{ $todayOrders }}</div>
                            <div class="text-muted small">Pesanan</div>
                        </div>
                        <div class="text-center">
                            <div class="h4 mb-1">Rp {{ number_format($todayRevenue, 0, ',', '.') }}</div>
                            <div class="text-muted small">Pendapatan</div>
                        </div>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted">Status Pesanan Hari Ini:</span>
                        <div class="d-flex gap-2">
                            <span class="badge bg-secondary">{{ $ordersByStatus['pending'] }} Pending</span>
                            <span class="badge" style="background-color: #fd7e14;">{{ $ordersByStatus['preparing'] }} Preparing</span>
                            <span class="badge bg-success">{{ $ordersByStatus['completed'] }} Selesai</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="card table-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-bell"></i> Permintaan Tertunda</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('superadmin.topup.index') }}" class="text-decoration-none text-dark">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <i class="bi bi-wallet2" style="font-size: 24px;"></i>
                                </div>
                                <div>
                                    <div class="h5 mb-0">{{ $pendingTopUps }}</div>
                                    <div class="text-muted small">Top-Up</div>
                                </div>
                            </div>
                        </a>
                        <a href="{{ route('superadmin.withdrawal.index') }}" class="text-decoration-none text-dark">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <i class="bi bi-bank" style="font-size: 24px;"></i>
                                </div>
                                <div>
                                    <div class="h5 mb-0">{{ $pendingWithdrawals }}</div>
                                    <div class="text-muted small">Penarikan</div>
                                </div>
                            </div>
                        </a>
                        <a href="{{ route('superadmin.reports.index') }}" class="text-decoration-none text-dark">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <i class="bi bi-flag" style="font-size: 24px;"></i>
                                </div>
                                <div>
                                    <div class="h5 mb-0">{{ $pendingReports }}</div>
                                    <div class="text-muted small">Laporan</div>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Revenue Chart -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card table-card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-graph-up"></i> Pendapatan Bulanan</h5>
                </div>
                <div class="card-body">
                    <div style="height: 300px;">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Stores & Products -->
    <div class="row mb-4">
        <div class="col-md-6 mb-3">
            <div class="card table-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-trophy"></i> Toko Teratas (30 Hari)</h5>
                    <a href="{{ route('superadmin.sellers.index') }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
                </div>
                <div class="card-body">
                    @if($topStores->isEmpty())
                        <p class="text-muted text-center">Belum ada data.</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Toko</th>
                                        <th>Pesanan</th>
                                        <th class="text-end">Pendapatan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($topStores as $index => $store)
                                        <tr>
                                            <td>
                                                <span class="badge bg-secondary me-2">{{ $index + 1 }}</span>
                                                {{ $store['store_name'] }}
                                            </td>
                                            <td>{{ $store['order_count'] }}</td>
                                            <td class="text-end">Rp {{ number_format($store['revenue'], 0, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="card table-card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-star"></i> Produk Terlaris (30 Hari)</h5>
                </div>
                <div class="card-body">
                    @if($topProducts->isEmpty())
                        <p class="text-muted text-center">Belum ada data.</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Produk</th>
                                        <th>Terjual</th>
                                        <th class="text-end">Pendapatan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($topProducts as $product)
                                        <tr>
                                            <td>{{ $product['menu']->name ?? 'Unknown' }}</td>
                                            <td>{{ $product['total_sold'] }}</td>
                                            <td class="text-end">Rp {{ number_format($product['total_revenue'], 0, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Orders -->
    <div class="row">
        <div class="col-12">
            <div class="card table-card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-clock-history"></i> Pesanan Terbaru</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Pemesan</th>
                                    <th>Items</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Tanggal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentOrders as $order)
                                    <tr>
                                        <td>#{{ $order->id }}</td>
                                        <td>{{ $order->user->name }}</td>
                                        <td>
                                            @foreach($order->orderItems->take(2) as $item)
                                                <div>{{ $item->menu->name ?? 'Unknown' }} x{{ $item->qty }}</div>
                                            @endforeach
                                            @if($order->orderItems->count() > 2)
                                                <div class="text-muted small">+{{ $order->orderItems->count() - 2 }} lagi</div>
                                            @endif
                                        </td>
                                        <td>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                                        <td>
                                            @if($order->is_completed || $order->is_auto_confirmed)
                                                <span class="badge bg-success">Selesai</span>
                                            @elseif($order->status === 'ready')
                                                <span class="badge bg-success">Siap</span>
                                            @elseif($order->status === 'preparing')
                                                <span class="badge" style="background-color: #fd7e14;">Preparing</span>
                                            @else
                                                <span class="badge bg-secondary">Pending</span>
                                            @endif
                                        </td>
                                        <td>{{ $order->created_at->format('d/m H:i') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('revenueChart').getContext('2d');
    const monthlyData = @json($monthlyRevenue);
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: monthlyData.map(item => item.month),
            datasets: [{
                label: 'Pendapatan (Rp)',
                data: monthlyData.map(item => item.revenue),
                backgroundColor: 'rgba(108, 117, 125, 0.2)',
                borderColor: 'rgba(108, 117, 125, 1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4,
                pointRadius: 4,
                pointBackgroundColor: 'rgba(108, 117, 125, 1)',
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                        }
                    }
                }
            }
        }
    });
});
</script>
@endsection

