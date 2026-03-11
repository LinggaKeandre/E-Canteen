@extends('layouts.master')

@section('title', 'Kelola Top-Up - E-Canteen')

@section('content')
<div class="container py-4">
    <h2><i class="bi bi-wallet2"></i> Kelola Top-Up Saldo</h2>
    
    <div class="mb-2">
        <span class="text-muted"><i class="bi bi-arrow-repeat"></i> Halaman akan refresh otomatis untuk melihat permintaan terbaru.</span>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <!-- Global QR Code Section -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="bi bi-qr-code me-2"></i>QR Code Pembayaran Global</h5>
        </div>
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-3 text-center">
                    @if($globalQrCode && $globalQrCode->topup_qr_code)
                        <img src="{{ asset('storage/' . $globalQrCode->topup_qr_code) }}" alt="Global QR Code" class="img-thumbnail" style="max-width: 150px;">
                    @else
                        <div class="text-muted py-4">
                            <i class="bi bi-qr-code fs-1"></i>
                            <p class="mb-0">Belum ada QR Code</p>
                        </div>
                    @endif
                </div>
                <div class="col-md-9">
                    <p class="text-muted">QR Code ini akan ditampilkan di halaman /balance untuk semua user agar dapat melakukan pembayaran top-up.</p>
                    <form action="{{ route('superadmin.topup.uploadGlobalQr') }}" method="POST" enctype="multipart/form-data" class="d-inline-block me-2">
                        @csrf
                        <div class="input-group" style="max-width: 400px;">
                            <input type="file" name="qr_code" class="form-control" accept="image/*" required>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-upload me-1"></i> Upload QR
                            </button>
                        </div>
                    </form>
                    @if($globalQrCode && $globalQrCode->topup_qr_code)
                        <form action="{{ route('superadmin.topup.deleteGlobalQr') }}" method="POST" class="d-inline-block">
                            @csrf
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Hapus QR Code?')">
                                <i class="bi bi-trash me-1"></i> Hapus
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Stats - Simple Design -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <div class="h3 mb-1">{{ $stats['pending'] }}</div>
                    <div class="text-muted small">Menunggu</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <div class="h3 mb-1">{{ $stats['approved'] }}</div>
                    <div class="text-muted small">Disetujui</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
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
            <a href="?status=approved" class="btn btn-sm {{ $status === 'approved' ? 'btn-primary' : 'btn-outline-primary' }}">Disetujui</a>
            <a href="?status=rejected" class="btn btn-sm {{ $status === 'rejected' ? 'btn-primary' : 'btn-outline-primary' }}">Ditolak</a>
        </div>
        <button class="btn btn-sm btn-outline-secondary" onclick="location.reload()">
            <i class="bi bi-arrow-clockwise"></i> Refresh
        </button>
    </div>

    <!-- Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>User</th>
                            <th>Jumlah</th>
                            <th>Metode</th>
                            <th>Bukti</th>
                            <th>Status</th>
                            <th>Alasan</th>
                            <th>Tanggal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="topup-table-body">
                        @forelse($requests as $request)
                            <tr>
                                <td>#{{ $request->id }}</td>
                                <td>{{ $request->user->name }}</td>
                                <td>Rp {{ number_format($request->amount, 0, ',', '.') }}</td>
                                <td>{{ $request->payment_method ?? '-' }}</td>
                                <td>
                                    @if($request->payment_proof)
                                        <a href="{{ asset('storage/' . $request->payment_proof) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-image"></i> Lihat
                                        </a>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    @if($request->status === 'pending')
                                        <span class="badge bg-warning">Menunggu</span>
                                    @elseif($request->status === 'approved')
                                        <span class="badge bg-success">Disetujui</span>
                                    @else
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
                                <td>{{ $request->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    @if($request->status === 'pending')
                                        <form action="{{ route('superadmin.topup.approve', $request) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Setuju top-up Rp {{ number_format($request->amount, 0, ',', '.') }}?')">
                                                <i class="bi bi-check"></i> Setuju
                                            </button>
                                        </form>
                                        <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $request->id }}">
                                            <i class="bi bi-x"></i> Tolak
                                        </button>
                                        
                                        <!-- Reject Modal -->
                                        <div class="modal fade" id="rejectModal{{ $request->id }}" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Tolak Top-Up</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <form action="{{ route('superadmin.topup.reject', $request) }}" method="POST">
                                                        @csrf
                                                        <div class="modal-body">
                                                            <p>Yakin menolak top-up Rp {{ number_format($request->amount, 0, ',', '.') }} dari {{ $request->user->name }}?</p>
                                                            <div class="mb-3">
                                                                <label class="form-label">Alasan Penolakan (Opsional)</label>
                                                                <textarea name="notes" class="form-control" rows="3"></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                            <button type="submit" class="btn btn-danger">Tolak</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4">Tidak ada data.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            {{ $requests->links() }}
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto refresh every 5 seconds
    setInterval(function() {
        // Check if there are pending requests that need to be shown
        fetch(window.location.href)
            .then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newTableBody = doc.querySelector('#topup-table-body');
                const currentTableBody = document.querySelector('#topup-table-body');
                
                if (newTableBody && currentTableBody) {
                    // Only refresh if content changed
                    if (newTableBody.innerHTML !== currentTableBody.innerHTML) {
                        location.reload();
                    }
                }
            })
            .catch(error => console.log('Error checking for updates:', error));
    }, 5000);
});
</script>
@endsection

