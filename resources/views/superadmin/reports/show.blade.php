@extends('layouts.master')

@section('title', 'Detail Laporan - E-Canteen')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-flag"></i> Detail Laporan #{{ $report->id }}</h2>
        <a href="{{ route('superadmin.reports.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Informasi Pelapor</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td width="40%">Nama</td>
                            <td><strong>{{ $report->user->name }}</strong></td>
                        </tr>
                        <tr>
                            <td>Email</td>
                            <td>{{ $report->user->email }}</td>
                        </tr>
                        <tr>
                            <td>Tanggal Laporan</td>
                            <td>{{ $report->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Detail Pesanan</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td width="40%">Order ID</td>
                            <td><strong>#{{ $report->order->id }}</strong></td>
                        </tr>
                        <tr>
                            <td>Total</td>
                            <td>Rp {{ number_format($report->order->total_amount, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td>Status Pesanan</td>
                            <td>
                                @if($report->order->is_completed || $report->order->is_auto_confirmed)
                                    <span class="badge bg-success">Selesai</span>
                                @else
                                    <span class="badge bg-{{ $report->order->status === 'pending' ? 'secondary' : ($report->order->status === 'preparing' ? 'warning' : 'success') }}">
                                        {{ ucfirst($report->order->status) }}
                                    </span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td>Kelas</td>
                            <td>{{ $report->order->classroom }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Isi Laporan</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Masalah</label>
                        <div class="p-2 bg-light rounded">
                            @if($report->reason === 'food_quality')
                                Kualitas Makanan Buruk
                            @elseif($report->reason === 'missing_item')
                                Item Tidak Lengkap
                            @elseif($report->reason === 'wrong_order')
                                Pesanan Salah
                            @elseif($report->reason === 'late_delivery')
                                Pengiriman Terlambat
                            @elseif($report->reason === 'seller_behavior')
                                Perilaku Penjual
                            @else
                                {{ $report->reason }}
                            @endif
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <div class="p-2 bg-light rounded">
                            {{ $report->description ?? 'Tidak ada deskripsi' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Items in the order -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Item Pesanan</h5>
                </div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Menu</th>
                                <th>Varian/Addons</th>
                                <th>Harga</th>
                                <th>Jumlah</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($report->order->orderItems as $item)
                                <tr>
                                    <td>
                                        {{ $item->menu->name ?? 'Unknown' }}
                                        @if($item->notes)
                                            <div class="small text-info mt-1">
                                                <i class="bi bi-sticky"></i> Catatan: {{ $item->notes }}
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        @if($item->variant_name)
                                            <span class="badge bg-warning text-dark">{{ $item->variant_name }}</span>
                                        @endif
                                        @if(!empty($item->addons))
                                            <div class="small text-muted mt-1">
                                                @foreach($item->addons as $addon)
                                                    <span class="badge bg-secondary">+{{ $addon['name'] }} (Rp {{ number_format($addon['price'], 0, ',', '.') }})</span>
                                                @endforeach
                                            </div>
                                        @endif
                                        @if(!$item->variant_name && empty($item->addons))
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                                    <td>{{ $item->qty }}</td>
                                    <td>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Resolution Form -->
    @if($report->status === 'pending')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Tindakan</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('superadmin.reports.update', $report) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select" required>
                                <option value="reviewed">Ditinjau</option>
                                <option value="resolved">Selesai</option>
                                <option value="rejected">Ditolak</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Catatan resolution (Opsional)</label>
                            <textarea name="resolution_notes" class="form-control" rows="3" placeholder="Tuliskan langkah yang diambil..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check"></i> Simpan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @else
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Hasil resolution</h5>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <strong>Status:</strong> 
                        @if($report->status === 'resolved')
                            <span class="badge bg-success">Selesai</span>
                        @elseif($report->status === 'reviewed')
                            <span class="badge bg-info">Ditinjau</span>
                        @else
                            <span class="badge bg-danger">Ditolak</span>
                        @endif
                    </div>
                    @if($report->resolution_notes)
                        <div>
                            <strong>Catatan:</strong>
                            <p class="mb-0 mt-2">{{ $report->resolution_notes }}</p>
                        </div>
                    @endif
                    @if($report->reviewed_by)
                        <div class="mt-2 text-muted">
                            Ditinjau oleh: {{ $report->reviewer->name }} pada {{ $report->reviewed_at->format('d/m/Y H:i') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

