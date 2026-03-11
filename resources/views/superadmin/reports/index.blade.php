@extends('layouts.master')

@section('title', 'Kelola Laporan - E-Canteen')

@section('content')
<div class="container py-4">
    <h2><i class="bi bi-flag"></i> Kelola Laporan / Contact CS</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <!-- Stats - Simple Design -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <div class="h3 mb-1">{{ $stats['pending'] }}</div>
                    <div class="text-muted small">Menunggu</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <div class="h3 mb-1">{{ $stats['reviewed'] }}</div>
                    <div class="text-muted small">Ditinjau</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <div class="h3 mb-1">{{ $stats['resolved'] }}</div>
                    <div class="text-muted small">Selesai</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <div class="h3 mb-1">{{ $stats['rejected'] }}</div>
                    <div class="text-muted small">Ditolak</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter -->
    <div class="mb-3">
        <div class="btn-group">
            <a href="?status=all" class="btn btn-sm {{ $status === 'all' ? 'btn-primary' : 'btn-outline-primary' }}">Semua</a>
            <a href="?status=pending" class="btn btn-sm {{ $status === 'pending' ? 'btn-primary' : 'btn-outline-primary' }}">Menunggu</a>
            <a href="?status=reviewed" class="btn btn-sm {{ $status === 'reviewed' ? 'btn-primary' : 'btn-outline-primary' }}">Ditinjau</a>
            <a href="?status=resolved" class="btn btn-sm {{ $status === 'resolved' ? 'btn-primary' : 'btn-outline-primary' }}">Selesai</a>
            <a href="?status=rejected" class="btn btn-sm {{ $status === 'rejected' ? 'btn-primary' : 'btn-outline-primary' }}">Ditolak</a>
        </div>
    </div>

    <!-- Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Pelapor</th>
                            <th>Order ID</th>
                            <th>Masalah</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reports as $report)
                            <tr>
                                <td>#{{ $report->id }}</td>
                                <td>{{ $report->user->name }}</td>
                                <td>
                                    <a href="{{ route('superadmin.reports.show', $report) }}">#{{ $report->order_id }}</a>
                                </td>
                                <td>
                                    @if($report->reason === 'food_quality')
                                        <span class="badge bg-danger">Kualitas Makanan</span>
                                    @elseif($report->reason === 'missing_item')
                                        <span class="badge bg-warning">Item Tidak Lengkap</span>
                                    @elseif($report->reason === 'wrong_order')
                                        <span class="badge bg-info">Pesanan Salah</span>
                                    @elseif($report->reason === 'late_delivery')
                                        <span class="badge bg-secondary">Terlambat</span>
                                    @elseif($report->reason === 'seller_behavior')
                                        <span class="badge bg-dark">Perilaku Penjual</span>
                                    @else
                                        <span class="badge bg-primary">Lainnya</span>
                                    @endif
                                </td>
                                <td>
                                    @if($report->status === 'pending')
                                        <span class="badge bg-warning">Menunggu</span>
                                    @elseif($report->status === 'reviewed')
                                        <span class="badge bg-info">Ditinjau</span>
                                    @elseif($report->status === 'resolved')
                                        <span class="badge bg-success">Selesai</span>
                                    @else
                                        <span class="badge bg-danger">Ditolak</span>
                                    @endif
                                </td>
                                <td>{{ $report->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <a href="{{ route('superadmin.reports.show', $report) }}" class="btn btn-sm btn-primary">
                                        <i class="bi bi-eye"></i> Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">Tidak ada laporan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            {{ $reports->links() }}
        </div>
    </div>
</div>
@endsection

