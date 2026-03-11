@extends('layouts.master')

@section('title', 'Lapor Masalah - E-Canteen')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-flag"></i> Laporkan Masalah</h2>
        <a href="{{ route('user.orders.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Pesanan #{{ $order->id }}</h5>
                </div>
                <div class="card-body">
                    <div class="mb-4 p-3 bg-light rounded">
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Tanggal:</strong> {{ $order->created_at->format('d/m/Y H:i') }}</p>
                                <p class="mb-1"><strong>Kelas:</strong> {{ $order->classroom }}</p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Total:</strong> Rp {{ number_format($order->total_amount, 0, ',', '.') }}</p>
                                <p class="mb-0"><strong>Status:</strong> 
                                    @if($order->is_completed || $order->is_auto_confirmed)
                                        <span class="badge bg-success">Selesai</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('user.reports.store', $order) }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label class="form-label">Jenis Masalah <span class="text-danger">*</span></label>
                            <select name="reason" class="form-select @error('reason') is-invalid @enderror" required>
                                <option value="">Pilih jenis masalah...</option>
                                <option value="food_quality">Kualitas Makanan Buruk</option>
                                <option value="missing_item">Item Tidak Lengkap</option>
                                <option value="wrong_order">Pesanan Salah</option>
                                <option value="late_delivery">Pengiriman Terlambat</option>
                                <option value="seller_behavior">Perilaku Penjual</option>
                                <option value="other">Lainnya</option>
                            </select>
                            @error('reason')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Deskripsi (Opsional)</label>
                            <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="4" placeholder="Jelaskan masalah yang Anda alami..."></textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i> Laporan Anda akan ditinjau oleh tim kami. Kami akan menghubungi Anda melalui email jika diperlukan.
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-send"></i> Kirim Laporan
                            </button>
                            <a href="{{ route('user.orders.index') }}" class="btn btn-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

