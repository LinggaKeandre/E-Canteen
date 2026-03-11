@extends('layouts.master')

@section('title', 'Semua Toko - E-Canteen')

@section('content')
<div class="container py-4">
    <h2><i class="bi bi-shop"></i> Semua Toko</h2>

    <!-- Search -->
    <form method="GET" class="mb-3">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Cari toko..." value="{{ $search }}">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-search"></i> Cari
            </button>
        </div>
    </form>

    <!-- Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Toko</th>
                            <th>Email</th>
                            <th>Menu</th>
                            <th>Pesanan</th>
                            <th>Total Pendapatan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sellers as $seller)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($seller->sellerProfile?->store_logo)
                                            <img src="{{ asset('storage/' . $seller->sellerProfile->store_logo) }}" alt="Logo" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;" class="me-2">
                                        @else
                                            <div class="bg-light d-flex align-items-center justify-content-center me-2" style="width: 40px; height: 40px; border-radius: 50%;">
                                                <i class="bi bi-shop text-muted"></i>
                                            </div>
                                        @endif
                                        <div>
                                            <strong>{{ $seller->sellerProfile?->store_name ?? $seller->name }}</strong>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $seller->email }}</td>
                                <td>{{ $seller->total_menus }}</td>
                                <td>{{ $seller->total_orders }}</td>
                                <td>Rp {{ number_format($seller->total_revenue, 0, ',', '.') }}</td>
                                <td>
                                    <a href="{{ route('superadmin.sellers.show', $seller) }}" class="btn btn-sm btn-primary">
                                        <i class="bi bi-eye"></i> Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">Tidak ada toko.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            {{ $sellers->links() }}
        </div>
    </div>
</div>
@endsection

