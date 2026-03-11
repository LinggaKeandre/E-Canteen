    @extends('layouts.master')

@section('title', 'Kelola Penarikan - E-Canteen')

@section('content')
<div class="container py-4">
    <h2><i class="bi bi-bank"></i> Kelola Penarikan Saldo</h2>

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
                    <div class="h3 mb-1">{{ $stats['approved'] }}</div>
                    <div class="text-muted small">Disetujui</div>
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
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <div class="h5 mb-1">Rp {{ number_format($stats['total_pending'], 0, ',', '.') }}</div>
                    <div class="text-muted small">Total Menunggu</div>
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
    </div>

    <!-- Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Seller</th>
                            <th>Jumlah</th>
                            <th>Bank</th>
                            <th>No. Rekening</th>
                            <th>Status</th>
                            <th>Alasan</th>
                            <th>Tanggal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($requests as $request)
                            <tr>
                                <td>#{{ $request->id }}</td>
                                <td>
                                    <div>{{ $request->user->name }}</div>
                                    <small class="text-muted">{{ $request->user->email }}</small>
                                </td>
                                <td>Rp {{ number_format($request->amount, 0, ',', '.') }}</td>
                                <td>{{ $request->bank_name ?? '-' }}</td>
                                <td>{{ $request->account_number ?? '-' }}</td>
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
                                        <form action="{{ route('superadmin.withdrawal.approve', $request) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Setuju penarikan Rp {{ number_format($request->amount, 0, ',', '.') }} ke {{ $request->bank_name }}?')">
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
                                                        <h5 class="modal-title">Tolak Penarikan</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <form action="{{ route('superadmin.withdrawal.reject', $request) }}" method="POST">
                                                        @csrf
                                                        <div class="modal-body">
                                                            <p>Yakin menolak penarikan Rp {{ number_format($request->amount, 0, ',', '.') }} dari {{ $request->user->name }}?</p>
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
                                <td colspan="8" class="text-center py-4">Tidak ada data.</td>
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

