@extends('layouts.master')

@section('title', 'Penarikan Saldo - E-Canteen')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0"><i class="bi bi-cash-stack"></i> Penarikan Saldo</h2>
    </div>

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

    <!-- Wallet Stats -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h6 class="mb-1 opacity-75">Saldo Anda</h6>
                    <h4 class="mb-0">Rp {{ number_format($availableBalance, 0, ',', '.') }}</h4>
                </div>
            </div>
        </div>
        
    </div>

    @if($availableBalance >= 10000)
    <!-- Withdrawal Form -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="bi bi-send"></i> Ajukan Penarikan</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.withdrawal.store') }}" method="POST" id="withdrawalForm">
                @csrf
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="amount" class="form-label">Jumlah Penarikan</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" class="form-control" id="amount" name="amount" 
                                    min="10000" max="{{ $availableBalance }}" required 
                                    placeholder="Min: 10.000">
                            </div>
                            <div class="form-text">Saldo tersedia: Rp {{ number_format($availableBalance, 0, ',', '.') }}</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="appointment_date" class="form-label">Tanggal Janji Temu</label>
                            <input type="date" class="form-control" id="appointment_date" name="appointment_date" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="appointment_time" class="form-label">Waktu Janji Temu (07:00 - 15:00)</label>
                            <select class="form-select" id="appointment_time" name="appointment_time" required>
                                <option value="">Pilih Jam</option>
                                <option value="07:00">07:00</option>
                                <option value="07:30">07:30</option>
                                <option value="08:00">08:00</option>
                                <option value="08:30">08:30</option>
                                <option value="09:00">09:00</option>
                                <option value="09:30">09:30</option>
                                <option value="10:00">10:00</option>
                                <option value="10:30">10:30</option>
                                <option value="11:00">11:00</option>
                                <option value="11:30">11:30</option>
                                <option value="12:00">12:00</option>
                                <option value="12:30">12:30</option>
                                <option value="13:00">13:00</option>
                                <option value="13:30">13:30</option>
                                <option value="14:00">14:00</option>
                                <option value="14:30">14:30</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label for="notes" class="form-label">Catatan (Opsional)</label>
                            <textarea class="form-control" id="notes" name="notes" rows="2" placeholder="Catatan tambahan..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> 
                    <strong>Cara Penarikan:</strong> Setelah penarikan disetujui, Anda perlu bertemu langsung dengan superadmin untuk mengambil uang cash. Silakan pilih jadwal janji temu di atas (minimal H+1 dari hari ini, jam 07:00 - 15:00).
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-send"></i> Ajukan Penarikan
                </button>
            </form>
        </div>
    @else
    <div class="alert alert-warning">
        <i class="bi bi-exclamation-triangle"></i> 
        Saldo tersedia tidak mencukupi untuk penarikan. Minimal penarikan adalah Rp 10.000.
    </div>
    @endif

    <!-- Withdrawal History -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="bi bi-clock-history"></i> Riwayat Penarikan</h5>
        </div>
        <div class="card-body">
            @if($withdrawalRequests->isEmpty())
                <p class="text-muted text-center">Belum ada permintaan penarikan.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Tanggal</th>
                                <th>Jumlah</th>
                                <th>Status</th>
                                <th>Alasan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($withdrawalRequests as $request)
                                <tr>
                                    <td>#{{ $request->id }}</td>
                                    <td>{{ $request->created_at->format('d/m/Y H:i') }}</td>
                                    <td>Rp {{ number_format($request->amount, 0, ',', '.') }}</td>
                                    <td>
                                        @if($request->status === 'pending')
                                            <span class="badge bg-warning">Menunggu</span>
                                        @elseif($request->status === 'approved')
                                            <span class="badge bg-success">Disetujui</span>
                                        @elseif($request->status === 'rejected')
                                            <span class="badge bg-danger">Ditolak</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($request->notes)
                                            <span class="text-danger">{{ $request->notes }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center mt-3">
                    {{ $withdrawalRequests->links() }}
                </div>
            @endif
        </div>
    </div> <!-- end container -->
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Set minimum date to tomorrow (H+1)
    const dateInput = document.getElementById('appointment_date');
    if (dateInput) {
        const today = new Date();
        const tomorrow = new Date(today);
        tomorrow.setDate(tomorrow.getDate() + 1);
        const tomorrowStr = tomorrow.toISOString().split('T')[0];
        dateInput.setAttribute('min', tomorrowStr);
    }
});
</script>
@endsection
