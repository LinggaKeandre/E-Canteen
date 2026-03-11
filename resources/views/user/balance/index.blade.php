@extends('layouts.master')

@section('title', 'Saldo - E-Canteen')

@section('styles')
<style>
    .balance-hero {
        background-color: var(--primary-orange);
        padding: 2rem;
        border-radius: 16px;
        color: white;
    }
    
    .balance-amount-display {
        font-size: 2.5rem;
        font-weight: 700;
    }
    
    .balance-label-display {
        opacity: 0.9;
        font-size: 0.9rem;
    }
    
    .wallet-icon {
        width: 60px;
        height: 60px;
        background: rgba(255,255,255,0.2);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }
    
    .quick-topup-btn {
        background: white;
        color: var(--primary-orange);
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 25px;
        font-weight: 600;
        transition: all 0.2s;
    }
    
    .quick-topup-btn:hover {
        transform: scale(1.02);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    
    .amount-option {
        border: 2px solid #E5E7EB;
        border-radius: 12px;
        padding: 1rem;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s;
        font-weight: 600;
        color: var(--text-dark);
    }
    
    .amount-option:hover {
        border-color: var(--primary-orange);
        background: var(--light-orange);
    }
    
    .amount-option.selected {
        border-color: var(--primary-orange);
        background: var(--light-orange);
        color: var(--primary-orange);
    }
    
    .history-item {
        padding: 1rem;
        border-bottom: 1px solid #E5E7EB;
    }
    
    .history-item:last-child {
        border-bottom: none;
    }
    
    .history-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .history-icon.credit {
        background: #D1FAE5;
        color: #065F46;
    }
    
    .history-icon.debit {
        background: #FEE2E2;
        color: #991B1B;
    }
</style>
@endsection

@section('content')
<div class="container py-4">
    <h2 class="mb-4"><i class="bi bi-wallet2"></i> Kelola Saldo</h2>
    
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    
    <!-- QR Code Display for Top-up -->
    @if($globalQrCode && $globalQrCode->topup_qr_code)
        <div class="card mb-4">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="bi bi-qr-code me-2"></i>QR Code Pembayaran Top-Up</h5>
            </div>
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-3 text-center">
                        <img src="{{ asset('storage/' . $globalQrCode->topup_qr_code) }}" alt="QR Code Pembayaran" class="img-fluid" style="max-width: 200px;">
                    </div>
                    <div class="col-md-9">
                        <p class="mb-0">Silakan scan QR code di atas untuk melakukan pembayaran top-up saldo.</p>
                    </div>
                </div>
            </div>
        </div>
    @endif
    
    <!-- Balance Card -->
    <div class="balance-hero mb-4">
        <div class="row align-items-center">
            <div class="col-md-8">
                <p class="balance-label-display mb-1">Saldo Virtual Anda</p>
                <p class="balance-amount-display mb-0">Rp {{ number_format(Auth::user()->balance, 0, ',', '.') }}</p>
            </div>
            <div class="col-md-4 text-md-end">
                <div class="wallet-icon ms-auto">
                    <i class="bi bi-wallet2"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-lg-8">
            <!-- Top Up Form with Payment Proof -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-plus-circle me-2"></i>Isi Saldo</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('user.balance.topup') }}" enctype="multipart/form-data" id="topupForm">
                        @csrf
                        <div class="mb-3">
                            <label for="amount" class="form-label fw-bold">Jumlah Top Up</label>
                            <select class="form-select @error('amount') is-invalid @enderror" id="amount" name="amount" required>
                                <option value="">Pilih Jumlah</option>
                                <option value="10000" {{ old('amount') == 10000 ? 'selected' : '' }}>Rp 10.000</option>
                                <option value="20000" {{ old('amount') == 20000 ? 'selected' : '' }}>Rp 20.000</option>
                                <option value="25000" {{ old('amount') == 25000 ? 'selected' : '' }}>Rp 25.000</option>
                                <option value="50000" {{ old('amount') == 50000 ? 'selected' : '' }}>Rp 50.000</option>
                                <option value="100000" {{ old('amount') == 100000 ? 'selected' : '' }}>Rp 100.000</option>
                            </select>
                            @error('amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="payment_method" class="form-label fw-bold">Metode Pembayaran</label>
                            <select class="form-select @error('payment_method') is-invalid @enderror" id="payment_method" name="payment_method" required>
                                <option value="">Pilih Metode</option>
                                <option value="Transfer BCA" {{ old('payment_method') == 'Transfer BCA' ? 'selected' : '' }}>Transfer BCA</option>
                                <option value="Transfer BNI" {{ old('payment_method') == 'Transfer BNI' ? 'selected' : '' }}>Transfer BNI</option>
                                <option value="Transfer BRI" {{ old('payment_method') == 'Transfer BRI' ? 'selected' : '' }}>Transfer BRI</option>
                                <option value="Transfer Mandiri" {{ old('payment_method') == 'Transfer Mandiri' ? 'selected' : '' }}>Transfer Mandiri</option>
                                <option value="Dana" {{ old('payment_method') == 'Dana' ? 'selected' : '' }}>Dana</option>
                                <option value="GoPay" {{ old('payment_method') == 'GoPay' ? 'selected' : '' }}>GoPay</option>
                                <option value="OVO" {{ old('payment_method') == 'OVO' ? 'selected' : '' }}>OVO</option>
                            </select>
                            @error('payment_method')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="payment_proof" class="form-label fw-bold">Bukti Pembayaran</label>
                            <input type="file" class="form-control @error('payment_proof') is-invalid @enderror" id="payment_proof" name="payment_proof" accept="image/*" required>
                            @error('payment_proof')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Upload bukti transfer (JPG, PNG)</small>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-send me-2"></i>Kirim Permintaan Top Up
                        </button>
                    </form>
                    <hr>
                    <small class="text-muted">* Permintaan top-up akan diverifikasi oleh superadmin.</small>
                </div>
            </div>
            
            <!-- Quick Top Up (Demo Only) -->
            <div class="card mb-4">
                <div class="card-header" style="background: var(--light-orange);">
                    <h5 class="mb-0" style="color: var(--primary-orange);"><i class="bi bi-lightning me-2"></i>Isi Saldo Instan (Demo)</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('user.balance.quickTopup') }}" id="quickTopupForm">
                        @csrf
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="quick_amount" class="form-label fw-bold">Jumlah</label>
                                    <select class="form-select" id="quick_amount" name="amount" required>
                                        <option value="">Pilih Jumlah</option>
                                        <option value="10000">Rp 10.000</option>
                                        <option value="20000">Rp 20.000</option>
                                        <option value="50000">Rp 50.000</option>
                                        <option value="100000">Rp 100.000</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="bi bi-lightning-fill me-2"></i>Isi Sekarang
                                </button>
                            </div>
                        </div>
                    </form>
                    <small class="text-muted">* Fitur ini untuk demo/testing saja. Saldo akan langsung ditambahkan.</small>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <!-- Transaction Summary -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Ringkasan Transaksi</h5>
                </div>
                <div class="card-body">
                    @php
                        $totalTopup = $transactions->where('type', 'credit')->sum('amount');
                        $totalSpend = $transactions->where('type', 'debit')->sum('amount');
                    @endphp
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Total Top Up:</span>
                        <span class="fw-bold text-success">+Rp {{ number_format($totalTopup, 0, ',', '.') }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Total Pengeluaran:</span>
                        <span class="fw-bold text-danger">-Rp {{ number_format($totalSpend, 0, ',', '.') }}</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <span class="fw-bold">Saldo Saat Ini:</span>
                        <span class="fw-bold" style="color: var(--primary-orange);">Rp {{ number_format(Auth::user()->balance, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Transaction History -->
    <div class="card mt-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-list-ul me-2"></i>Riwayat Transaksi</h5>
        </div>
        <div class="card-body p-0">
            @if($transactions->isEmpty())
                <p class="text-center text-muted py-4">Belum ada transaksi.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Jenis</th>
                                <th>Jumlah</th>
                                <th>Saldo Sebelum</th>
                                <th>Saldo Sesudah</th>
                                <th>Catatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($transactions as $transaction)
                                <tr>
                                    <td>{{ $transaction->created_at->format('d M Y H:i') }}</td>
                                    <td>
                                        @if($transaction->type === 'credit')
                                            <span class="badge bg-success">Top Up</span>
                                        @else
                                            <span class="badge bg-danger">Pembayaran</span>
                                        @endif
                                    </td>
                                    <td class="{{ $transaction->type === 'credit' ? 'text-success' : 'text-danger' }}">
                                        {{ $transaction->type === 'credit' ? '+' : '-' }}Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                                    </td>
                                    <td>Rp {{ number_format($transaction->balance_before, 0, ',', '.') }}</td>
                                    <td>Rp {{ number_format($transaction->balance_after, 0, ',', '.') }}</td>
                                    <td>{{ $transaction->notes ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="p-3 border-top">
                    {{ $transactions->onEachSide(0)->links('pagination::bootstrap-4') }}
                </div>
            @endif
        </div>
    </div>

    <!-- Top-Up Request History -->
    <div class="card mt-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="bi bi-file-earmark-text me-2"></i>Riwayat Permintaan Top-Up</h5>
        </div>
        <div class="card-body p-0">
            @if(isset($topups) && $topups->isEmpty())
                <p class="text-center text-muted py-4">Belum ada permintaan top-up.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Jumlah</th>
                                <th>Status</th>
                                <th>Alasan</th>
                                <th>Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($topups as $req)
                                <tr>
                                    <td>#{{ $req->id }}</td>
                                    <td>Rp {{ number_format($req->amount, 0, ',', '.') }}</td>
                                    <td>
                                        @if($req->status === 'pending')
                                            <span class="badge bg-warning text-dark">Menunggu</span>
                                        @elseif($req->status === 'approved')
                                            <span class="badge bg-success">Disetujui</span>
                                        @else
                                            <span class="badge bg-danger">Ditolak</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($req->notes)
                                            <span class="text-danger">{{ $req->notes }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>{{ $req->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if(isset($topups) && $topups->total() > $topups->perPage())
                <div class="p-3 border-top">
                    {{ $topups->links('pagination::bootstrap-4') }}
                </div>
                @endif
            @endif
        </div>
    </div>
@endsection
