@extends('layouts.master')

@section('title', 'Kelola Pengguna - E-Canteen')

@section('content')
<div class="container py-4">
    <h2><i class="bi bi-people"></i> Kelola Pengguna</h2>

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

    <!-- Stats -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <div class="h3 mb-1">{{ $stats['total_users'] }}</div>
                    <div class="text-muted small">Total Pengguna</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <div class="h3 mb-1">{{ $stats['total_sellers'] }}</div>
                    <div class="text-muted small">Total Penjual</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <div class="h3 mb-1 text-danger">{{ $stats['blocked'] }}</div>
                    <div class="text-muted small">Diblokir</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="row mb-3">
        <div class="col-md-6">
            <div class="btn-group">
                <a href="?role=all&status={{ $status }}&search={{ $search }}" class="btn btn-sm {{ $role === 'all' ? 'btn-primary' : 'btn-outline-primary' }}">Semua</a>
                <a href="?role=user&status={{ $status }}&search={{ $search }}" class="btn btn-sm {{ $role === 'user' ? 'btn-primary' : 'btn-outline-primary' }}">Pengguna</a>
                <a href="?role=seller&status={{ $status }}&search={{ $search }}" class="btn btn-sm {{ $role === 'seller' ? 'btn-primary' : 'btn-outline-primary' }}">Penjual</a>
            </div>
        </div>
        <div class="col-md-3">
            <div class="btn-group">
                <a href="?role={{ $role }}&status=all&search={{ $search }}" class="btn btn-sm {{ $status === 'all' ? 'btn-secondary' : 'btn-outline-secondary' }}">Semua</a>
                <a href="?role={{ $role }}&status=active&search={{ $search }}" class="btn btn-sm {{ $status === 'active' ? 'btn-success' : 'btn-outline-success' }}">Aktif</a>
                <a href="?role={{ $role }}&status=blocked&search={{ $search }}" class="btn btn-sm {{ $status === 'blocked' ? 'btn-danger' : 'btn-outline-danger' }}">Diblokir</a>
            </div>
        </div>
        <div class="col-md-3">
            <form method="GET" class="d-flex">
                <input type="hidden" name="role" value="{{ $role }}">
                <input type="hidden" name="status" value="{{ $status }}">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari..." value="{{ $search }}">
                <button type="submit" class="btn btn-sm btn-primary ms-1">
                    <i class="bi bi-search"></i>
                </button>
            </form>
        </div>
    </div>

    <!-- Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Terakhir Aktif</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($user->role === 'admin' && $user->sellerProfile?->store_logo)
                                            <img src="{{ asset('storage/' . $user->sellerProfile->store_logo) }}" alt="Logo" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;" class="me-2">
                                        @else
                                            <div class="bg-light d-flex align-items-center justify-content-center me-2" style="width: 40px; height: 40px; border-radius: 50%;">
                                                <i class="bi bi-person text-muted"></i>
                                            </div>
                                        @endif
                                        <div>
                                            <strong>{{ $user->name }}</strong>
                                            @if($user->role === 'admin')
                                                <div class="small text-muted">{{ $user->sellerProfile?->store_name ?? 'Toko' }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    @if($user->role === 'admin')
                                        <span class="badge bg-warning text-dark">Penjual</span>
                                    @else
                                        <span class="badge bg-primary">Pengguna</span>
                                    @endif
                                </td>
                                <td>
                                    @if($user->isBlocked())
                                        <span class="badge bg-danger">
                                            @if($user->block_type === 'permanent')
                                                Permanen
                                            @else
                                                Blokir ({{ $user->blocked_until->diffForHumans() }})
                                            @endif
                                        </span>
                                        @if($user->blocked_reason)
                                            <div class="small text-muted" title="{{ $user->blocked_reason }}">
                                                <i class="bi bi-info-circle"></i> {{ Str::limit($user->blocked_reason, 20) }}
                                            </div>
                                        @endif
                                    @else
                                        <span class="badge bg-success">Aktif</span>
                                    @endif
                                </td>
                                <td>
                                    <small class="text-muted">{{ $user->updated_at->format('d/m/Y H:i') }}</small>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('superadmin.users.show', $user) }}" class="btn btn-outline-primary">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        
                                        @if($user->isBlocked())
                                            <form action="{{ route('superadmin.users.unblock', $user) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-outline-success" title="Buka Blokir" onclick="return confirm('Buka blokir akun ini?')">
                                                    <i class="bi bi-unlock"></i>
                                                </button>
                                            </form>
                                        @else
                                            <button type="button" class="btn btn-outline-danger" title="Blokir" data-bs-toggle="modal" data-bs-target="#blockModal{{ $user->id }}">
                                                <i class="bi bi-lock"></i>
                                            </button>
                                        @endif
                                        
                                        <button type="button" class="btn btn-outline-dark" title="Hapus" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $user->id }}">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <!-- Block Modal -->
                            @if(!$user->isBlocked())
                                <div class="modal fade" id="blockModal{{ $user->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Blokir Akun - {{ $user->name }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <form action="{{ route('superadmin.users.block', $user) }}" method="POST">
                                                @csrf
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label class="form-label">Tipe Blokir</label>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="radio" name="block_type" id="block_type_temporary{{ $user->id }}" value="temporary" checked>
                                                            <label class="form-check-label" for="block_type_temporary{{ $user->id }}">
                                                                Sementara
                                                            </label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="radio" name="block_type" id="block_type_permanent{{ $user->id }}" value="permanent">
                                                            <label class="form-check-label" for="block_type_permanent{{ $user->id }}">
                                                                Permanen
                                                            </label>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="mb-3" id="durationGroup{{ $user->id }}">
                                                        <label class="form-label">Durasi Blokir</label>
                                                        <select name="block_duration" class="form-select">
                                                            <option value="1">1 Hari</option>
                                                            <option value="7">1 Minggu</option>
                                                            <option value="30">1 Bulan</option>
                                                            <option value="365">1 Tahun</option>
                                                        </select>
                                                    </div>
                                                    
                                                    <div class="mb-3">
                                                        <label class="form-label">Alasan <span class="text-danger">*</span></label>
                                                        <textarea name="reason" class="form-control" rows="3" required placeholder="Alasan pemblokiran..."></textarea>
                                                    </div>
                                                    
                                                    <div class="alert alert-warning">
                                                        <i class="bi bi-exclamation-triangle"></i> Akun yang sedang aktif akan otomatis keluar saat di-refresh.
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-danger">
                                                        <i class="bi bi-lock"></i> Blokir
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Delete Modal -->
                            <div class="modal fade" id="deleteModal{{ $user->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title text-danger">Hapus Akun - {{ $user->name }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <form action="{{ route('superadmin.users.destroy', $user) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <div class="modal-body">
                                                <div class="alert alert-danger">
                                                    <i class="bi bi-exclamation-triangle"></i> <strong>Peringatan!</strong> Tindakan ini tidak dapat dibatalkan.
                                                </div>
                                                
                                                <div class="mb-3">
                                                    <label class="form-label">Ketik <strong>HAPUS</strong> untuk konfirmasi</label>
                                                    <input type="text" name="confirmation" class="form-control" required pattern="HAPUS" placeholder="HAPUS">
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-danger">
                                                    <i class="bi bi-trash"></i> Hapus Akun
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">Tidak ada pengguna.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            {{ $users->links() }}
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle duration dropdown based on block type selection
    document.querySelectorAll('[name="block_type"]').forEach(function(radio) {
        radio.addEventListener('change', function() {
            const userId = this.id.replace('block_type_temporary', '').replace('block_type_permanent', '');
            const durationGroup = document.getElementById('durationGroup' + userId);
            
            if (this.value === 'permanent') {
                durationGroup.style.display = 'none';
            } else {
                durationGroup.style.display = 'block';
            }
        });
    });
});
</script>
@endpush
@endsection

