@extends('layouts.master')

@section('title', 'Batas Pengeluaran Harian - E-Canteen')

@section('styles')
<style>
    .limit-hero {
        background-color: var(--primary-orange);
        padding: 3rem 2rem;
        border-radius: 16px;
        color: white;
    }
    
    .limit-icon {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        margin: 0 auto 1rem;
        background: rgba(255,255,255,0.2);
    }
    
    .stats-card {
        background: white;
        border: 1px solid #E5E7EB;
        border-radius: 12px;
        padding: 1.5rem;
        text-align: center;
    }
    
    .stats-value {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--primary-orange);
    }
    
    .stats-label {
        color: #6B7280;
        font-size: 0.875rem;
    }
    
    .progress-limit {
        height: 12px;
        border-radius: 6px;
        background: #E5E7EB;
        overflow: hidden;
    }
    
    .progress-limit-bar {
        height: 100%;
        border-radius: 6px;
        transition: width 0.3s ease;
    }
    
    .enable-card {
        background: white;
        border: 1px solid #E5E7EB;
        border-radius: 12px;
        padding: 2rem;
    }
</style>
@endsection

@section('content')
<div class="container py-4">
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('user.dashboard') }}" class="btn btn-outline-light me-3">
            <i class="bi bi-arrow-left"></i>
        </a>
        <h2 class="mb-0 text-white">Batas Pengeluaran Harian</h2>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-md-6 mx-auto">
            <!-- Status Card -->
            <div class="limit-hero text-center mb-4">
                <div class="limit-icon">
                    <i class="bi bi-{{ $dailyLimitEnabled ? 'shield-check' : 'shield-slash' }}"></i>
                </div>
                
                <h4 class="mb-2">
                    @if($dailyLimitEnabled)
                        <span class="text-white">Batas Aktif</span>
                    @else
                        <span style="color: rgba(255,255,255,0.8);">Batas Tidak Aktif</span>
                    @endif
                </h4>
                
                <p style="opacity: 0.9;">
                    @if($dailyLimitEnabled)
                        Anda tidak dapat checkout jika total pengeluaran hari ini melebihi batas yang ditetapkan.
                    @else
                        Aktifkan batas pengeluaran harian untuk mengontrol pengeluaran Anda setiap hari.
                    @endif
                </p>
            </div>

            @if($dailyLimitEnabled)
                <!-- Stats when enabled -->
                <div class="row mb-4">
                    <div class="col-4">
                        <div class="stats-card">
                            <div class="stats-value">Rp {{ number_format($dailyLimit, 0, ',', '.') }}</div>
                            <div class="stats-label">Batas</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="stats-card">
                            <div class="stats-value">Rp {{ number_format($todaySpending, 0, ',', '.') }}</div>
                            <div class="stats-label">Terpakai</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="stats-card">
                            <div class="stats-value {{ $remainingDailyLimit < 10000 ? 'text-danger' : '' }}" style="color: {{ $remainingDailyLimit < 10000 ? '#EF4444' : '#10B981' }};">
                                Rp {{ number_format($remainingDailyLimit, 0, ',', '.') }}
                            </div>
                            <div class="stats-label">Sisa</div>
                        </div>
                    </div>
                </div>
                
                <!-- Progress bar -->
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <small class="text-muted">Pemakaian Hari Ini</small>
                            <small class="text-muted">{{ round(($todaySpending / $dailyLimit) * 100) }}%</small>
                        </div>
                        <div class="progress-limit">
                            <div class="progress-limit-bar bg-{{ $todaySpending >= $dailyLimit ? 'danger' : ($todaySpending >= $dailyLimit * 0.8 ? 'warning' : 'success') }}" 
                                 style="width: {{ min(($todaySpending / $dailyLimit) * 100, 100) }}%; background-color: {{ $todaySpending >= $dailyLimit ? '#EF4444' : ($todaySpending >= $dailyLimit * 0.8 ? '#F59E0B' : '#10B981') }};"></div>
                        </div>
                    </div>
                </div>
                
                <!-- Disable form -->
                <form action="{{ route('user.settings.spendingLimit.disable') }}" method="POST" class="text-center">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger">
                        <i class="bi bi-x-circle me-2"></i>Nonaktifkan Batas
                    </button>
                </form>
            @else
                <!-- Enable form -->
                <div class="enable-card">
                    <form action="{{ route('user.settings.spendingLimit.enable') }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label for="limit_amount" class="form-label fw-bold fs-5">Masukkan Batas Harian</label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text">Rp</span>
                                <input type="number" class="form-control" id="limit_amount" name="limit_amount" 
                                       placeholder="50000" min="1000" max="10000000" required>
                            </div>
                            <div class="form-text">Minimal Rp 1.000 - Maksimal Rp 10.000.000</div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 btn-lg">
                            <i class="bi bi-shield-check me-2"></i>Aktifkan Batas
                        </button>
                    </form>
                </div>
            @endif
            
            <!-- Info Card -->
            <div class="card mt-4">
                <div class="card-body">
                    <h6><i class="bi bi-info-circle text-primary me-2"></i>Cara Kerja</h6>
                    <ul class="small text-muted mb-0">
                        <li>Batas pengeluaran harian dihitung berdasarkan total pesanan yang sudah dibayar setiap hari.</li>
                        <li>Batas akan otomatis direset setiap hari pukul 00:00 (tengah malam).</li>
                        <li>Anda dapat mengaktifkan atau menonaktifkan batas kapan saja.</li>
                        <li>Jika total pesanan (termasuk di keranjang) melebihi batas, checkout akan diblokir.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
