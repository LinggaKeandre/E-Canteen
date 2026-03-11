@extends('layouts.master')

@section('title', 'Dashboard Admin - E-Canteen')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0"><i class="bi bi-speedometer2"></i> Dashboard Admin</h2>
        <a href="{{ route('admin.profile.edit') }}" class="btn btn-outline-primary btn-sm">
            <i class="bi bi-shop"></i> Edit Profil Toko
        </a>
    </div>
    
    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h6 class="text-muted mb-1">Total Menu</h6>
                    <h3 class="mb-0">{{ $totalMenus }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h6 class="text-muted mb-1">Pesanan Hari Ini</h6>
                    <h3 class="mb-0">{{ $todayOrders }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h6 class="text-muted mb-1">Pendapatan Hari Ini</h6>
                    <h3 class="mb-0">Rp {{ number_format($todayRevenue, 0, ',', '.') }}</h3>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <!-- Top Products Section -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="bi bi-star-fill"></i> Top Product</h5>
                        <div class="btn-group" role="group">
                            <a href="{{ route('admin.dashboard', ['period' => 'day']) }}" class="btn btn-sm {{ $timeFilter === 'day' ? 'btn-primary' : 'btn-outline-primary' }}">1 Hari</a>
                            <a href="{{ route('admin.dashboard', ['period' => 'week']) }}" class="btn btn-sm {{ $timeFilter === 'week' ? 'btn-primary' : 'btn-outline-primary' }}">1 Minggu</a>
                            <a href="{{ route('admin.dashboard', ['period' => 'month']) }}" class="btn btn-sm {{ $timeFilter === 'month' ? 'btn-primary' : 'btn-outline-primary' }}">1 Bulan</a>
                            <a href="{{ route('admin.dashboard', ['period' => 'lifetime']) }}" class="btn btn-sm {{ $timeFilter === 'lifetime' ? 'btn-primary' : 'btn-outline-primary' }}">Lifetime</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if($topProducts->isEmpty())
                        <p class="text-muted text-center">Belum ada data penjualan</p>
                    @else
                        <div class="row">
                            @foreach($topProducts as $index => $product)
                                <div class="col-12 mb-3">
                                    <div class="card bg-light">
                                        <div class="card-body py-2">
                                            <div class="d-flex align-items-center">
                                                <span class="badge bg-secondary me-2" style="width: 24px; height: 24px; display: flex; align-items: center; justify-content: center;">{{ $index + 1 }}</span>
                                                @if($product['menu'] && $product['menu']->photo_path)
                                                    <img src="{{ asset('storage/' . $product['menu']->photo_path) }}" alt="{{ $product['menu']->name }}" style="width: 50px; height: 50px; object-fit: cover;" class="rounded me-2">
                                                @else
                                                    <div class="bg-secondary rounded me-2" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                                                        <i class="bi bi-image text-white"></i>
                                                    </div>
                                                @endif
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-1">{{ $product['menu'] ? $product['menu']->name : 'Menu Dihapus' }}</h6>
                                                    <div class="d-flex flex-wrap gap-2">
                                                        <span class="badge bg-success">Terjual: {{ $product['total_sold'] }}</span>
                                                        <span class="badge bg-info">Omzet: Rp {{ number_format($product['total_revenue'], 0, ',', '.') }}</span>
                                                        @if($product['rating_count'] > 0)
                                                            <span class="badge bg-warning text-dark">
                                                                <i class="bi bi-star-fill"></i> {{ number_format($product['average_rating'], 1) }} ({{ $product['rating_count'] }})
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Latest Pending Orders Section -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-hourglass-split"></i> Pesanan Terbaru (Pending)</h5>
                </div>
                <div class="card-body">
                    @if($pendingOrders->isEmpty())
                        <p class="text-muted text-center">Tidak ada pesanan pending</p>
                    @else
                        <div class="row">
                            @foreach($pendingOrders as $order)
                                <div class="col-12 mb-2">
                                    <a href="{{ route('admin.orders.index', ['highlight' => $order->id]) }}" class="text-decoration-none">
                                        <div class="card bg-light highlight-card" data-order-id="{{ $order->id }}">
                                            <div class="card-body py-2">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <div class="flex-grow-1">
                                                        <h6 class="mb-1">
                                                            @foreach($order->orderItems as $item)
                                                                {{ $item->menu->name ?? 'Menu Dihapus' }}@if(!$loop->last), @endif
                                                            @endforeach
                                                        </h6>
                                                        <p class="mb-1 small">
                                                            <i class="bi bi-person"></i> {{ $order->user->name ?? 'User Dihapus' }} | 
                                                            <i class="bi bi-door"></i> {{ $order->classroom }}
                                                        </p>
                                                        <div class="d-flex flex-wrap gap-1">
                                                            <span class="badge bg-secondary">Qty: {{ $order->orderItems->sum('qty') }}</span>
                                                            <span class="badge bg-primary">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                                                            <span class="badge bg-info">{{ $order->pickup_slot === 'istirahat_1' ? 'Istirahat 1' : 'Istirahat 2' }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="text-end">
                                                        <small class="text-muted">{{ $order->created_at->format('H:i') }}</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                        @if($pendingOrders->count() >= 10)
                            <div class="text-center mt-2">
                                <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-outline-primary">Lihat Semua Pesanan</a>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

