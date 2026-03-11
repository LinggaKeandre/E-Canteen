@extends('layouts.master')

@section('title', 'Detail Toko - E-Canteen')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-shop"></i> Detail Toko</h2>
        <a href="{{ route('superadmin.sellers.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="row">
        <!-- Store Info -->
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-body text-center">
                    @if($seller->sellerProfile?->store_logo)
                        <img src="{{ asset('storage/' . $seller->sellerProfile->store_logo) }}" alt="Logo" style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover;" class="mb-3">
                    @else
                        <div class="bg-light d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 100px; height: 100px; border-radius: 50%;">
                            <i class="bi bi-shop text-muted" style="font-size: 48px;"></i>
                        </div>
                    @endif
                    <h4>{{ $seller->sellerProfile?->store_name ?? $seller->name }}</h4>
                    <p class="text-muted">{{ $seller->email }}</p>
                    @if($seller->sellerProfile?->store_description)
                        <p>{{ $seller->sellerProfile->store_description }}</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Stats - Simple Design -->
        <div class="col-md-8 mb-4">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <div class="card">
                        <div class="card-body text-center">
                            <div class="h3 mb-1">{{ $stats['total_menus'] }}</div>
                            <div class="text-muted small">Total Menu</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="card">
                        <div class="card-body text-center">
                            <div class="h3 mb-1">{{ $stats['total_orders'] }}</div>
                            <div class="text-muted small">Total Pesanan</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="card">
                        <div class="card-body text-center">
                            <div class="h5 mb-1">Rp {{ number_format($stats['total_revenue'], 0, ',', '.') }}</div>
                            <div class="text-muted small">Total Pendapatan</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="card">
                        <div class="card-body text-center">
                            <div class="h3 mb-1">{{ $stats['today_orders'] }}</div>
                            <div class="text-muted small">Pesanan Hari Ini</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Orders -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Pesanan Terbaru</h5>
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
                        @forelse($recentOrders as $order)
                            <tr>
                                <td>#{{ $order->id }}</td>
                                <td>{{ $order->user->name }}</td>
                                <td>
                                    @foreach($order->orderItems->take(2) as $item)
                                        <div>
                                            {{ $item->menu->name ?? 'Unknown' }}
                                            @if($item->variant_name)
                                                <span class="badge bg-warning text-dark ms-1">{{ $item->variant_name }}</span>
                                            @endif
                                            x{{ $item->qty }}
                                            @if(!empty($item->addons))
                                                <div class="small text-muted">
                                                    @foreach($item->addons as $addon)
                                                        <span class="badge bg-secondary">+{{ $addon['name'] }}</span>
                                                    @endforeach
                                                </div>
                                            @endif
                                            @if($item->notes)
                                                <div class="small text-info">
                                                    <i class="bi bi-sticky"></i> {{ $item->notes }}
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
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
                                <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">Belum ada pesanan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

