<nav class="navbar navbar-expand-lg sticky-top" style="background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);">
    <div class="container">
        <a class="navbar-brand text-white" href="{{ route('superadmin.dashboard') }}">
            <i class="bi bi-shield-check"></i> E-Canteen Admin
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
<ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link text-white" href="{{ route('superadmin.users.index') }}">
                        <i class="bi bi-people"></i> Kelola Pengguna
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="{{ route('superadmin.sellers.index') }}">
                        <i class="bi bi-shop"></i> Semua Toko
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="{{ route('superadmin.topup.index') }}">
                        <i class="bi bi-wallet2"></i> Top-Up
                        @if(\App\Models\TopUpRequest::where('status', 'pending')->count() > 0)
                            <span class="badge bg-danger">{{ \App\Models\TopUpRequest::where('status', 'pending')->count() }}</span>
                        @endif
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="{{ route('superadmin.withdrawal.index') }}">
                        <i class="bi bi-bank"></i> Penarikan
                        @if(\App\Models\WithdrawalRequest::where('status', 'pending')->count() > 0)
                            <span class="badge bg-danger">{{ \App\Models\WithdrawalRequest::where('status', 'pending')->count() }}</span>
                        @endif
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="{{ route('superadmin.reports.index') }}">
                        <i class="bi bi-flag"></i> Laporan
                        @if(\App\Models\Report::where('status', 'pending')->count() > 0)
                            <span class="badge bg-danger">{{ \App\Models\Report::where('status', 'pending')->count() }}</span>
                        @endif
                    </a>
                </li>
            </ul>
            <ul class="navbar-nav">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-white" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle"></i> {{ Auth::user()->name }}
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><span class="dropdown-item-text">Role: Superadmin</span></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item">Logout</button>
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

