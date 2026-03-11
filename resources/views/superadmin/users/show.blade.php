@extends('layouts.master')

@section('title', 'Detail Pengguna - E-Canteen')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-person"></i> Detail Pengguna</h2>
        <a href="{{ route('superadmin.users.index') }}" class="btn btn-outline-secondary">
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
                    <h5 class="mb-0">Informasi Akun</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td width="40%">Nama</td>
                            <td><strong>{{ $user->name }}</strong></td>
                        </tr>
                        <tr>
                            <td>Email</td>
                            <td>{{ $user->email }}</td>
                        </tr>
                        <tr>
                            <td>Role</td>
                            <td>
                                @if($user->role === 'admin')
                                    <span class="badge bg-warning text-dark">Penjual</span>
                                @else
                                    <span class="badge bg-primary">Pengguna</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td>Status</td>
                            <td>
                                @if($user->isBlocked())
                                    <span class="badge bg-danger">
                                        @if($user->block_type === 'permanent')
                                            Diblokir Permanen
                                        @else
                                            Diblokir ({{ $user->blocked_until->diffForHumans() }})
                                        @endif
                                    </span>
                                @else
                                    <span class="badge bg-success">Aktif</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td>Tanggal Daftar</td>
                            <td>{{ $user->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Stats</h5>
                </div>
                <div class="card-body">
                    @if($user->role === 'admin')
                        <table class="table table-borderless">
                            <tr>
                                <td width="40%">Total Menu</td>
                                <td><strong>{{ $stats['total_menus'] }}</strong></td>
                            </tr>
                            <tr>
                                <td>Total Pesanan</td>
                                <td><strong>{{ $stats['total_orders'] }}</strong></td>
                            </tr>
                            <tr>
                                <td>Total Pendapatan</td>
                                <td><strong>Rp {{ number_format($stats['total_revenue'], 0, ',', '.') }}</strong></td>
                            </tr>
                        </table>
                    @else
                        <table class="table table-borderless">
                            <tr>
                                <td width="40%">Total Pesanan</td>
                                <td><strong>{{ $stats['total_orders'] }}</strong></td>
                            </tr>
                            <tr>
                                <td>Total Belanja</td>
                                <td><strong>Rp {{ number_format($stats['total_spent'], 0, ',', '.') }}</strong></td>
                            </tr>
                        </table>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if($user->isBlocked())
    <div class="row">
        <div class="col-md-12 mb-4">
            <div class="card border-danger">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">Informasi Blokir</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td width="40%">Diblokir pada</td>
                            <td>{{ $user->blocked_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        <tr>
                            <td>Jenis Blokir</td>
                            <td>
                                @if($user->block_type === 'permanent')
                                    <span class="badge bg-danger">Permanen</span>
                                @else
                                    <span class="badge bg-warning">Sementara - until {{ $user->blocked_until->format('d/m/Y H:i') }}</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td>Alasan</td>
                            <td>{{ $user->blocked_reason ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td>Diblokir oleh</td>
                            <td>{{ $user->blocker->name ?? '-' }}</td>
                        </tr>
                    </table>
                    
                    <form action="{{ route('superadmin.users.unblock', $user) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-success" onclick="return confirm('Buka blokir akun ini?')">
                            <i class="bi bi-unlock"></i> Buka Blokir
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Actions -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Aksi</h5>
                </div>
                <div class="card-body">
                    @if(!$user->isBlocked())
                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#blockModal">
                            <i class="bi bi-lock"></i> Blokir Akun
                        </button>
                    @endif
                    
                    <button type="button" class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#deleteModal">
                        <i class="bi bi-trash"></i> Hapus Akun
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Block Modal -->
    @if(!$user->isBlocked())
    <div class="modal fade" id="blockModal" tabindex="-1">
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
                                <input class="form-check-input" type="radio" name="block_type" id="block_type_temporary" value="temporary" checked>
                                <label class="form-check-label" for="block_type_temporary">
                                    Sementara
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="block_type" id="block_type_permanent" value="permanent">
                                <label class="form-check-label" for="block_type_permanent">
                                    Permanen
                                </label>
                            </div>
                        </div>
                        
                        <div class="mb-3" id="durationGroup">
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
    <div class="modal fade" id="deleteModal" tabindex="-1">
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
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const blockTypeRadios = document.querySelectorAll('[name="block_type"]');
    const durationGroup = document.getElementById('durationGroup');
    
    if (blockTypeRadios && durationGroup) {
        blockTypeRadios.forEach(function(radio) {
            radio.addEventListener('change', function() {
                if (this.value === 'permanent') {
                    durationGroup.style.display = 'none';
                } else {
                    durationGroup.style.display = 'block';
                }
            });
        });
    }
});
</script>
@endpush
@endsection

