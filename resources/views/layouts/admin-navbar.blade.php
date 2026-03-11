<nav class="navbar navbar-expand-lg sticky-top">
    <div class="container-fluid">
        <a class="navbar-brand" href="{{ route('admin.dashboard') }}">
            <i class="bi bi-shop"></i> E-Canteen Admin
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('admin.menus.index') }}">Menu</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('admin.orders.index') }}">Antrean Pesanan</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('admin.reports.index') }}">Laporan</a>
                </li>
            </ul>
            <ul class="navbar-nav">
                <!-- Wallet Icon with Notification Badge -->
                <li class="nav-item">
                    <a class="nav-link position-relative" href="{{ route('admin.withdrawal.index') }}" title="Saldo & Penarikan">
                        <i class="bi bi-coin" style="font-size: 1.2rem;"></i>
                        <span id="notification-badge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="display: none;">
                            <span id="notification-count">0</span>
                        </span>
                    </a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        {{ Auth::user()->name }}
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><span class="dropdown-item-text">Role: Admin</span></li>
                        <li><span class="dropdown-item-text">{{ Auth::user()->sellerProfile->store_name ?? 'Toko' }}</span></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item" href="{{ route('admin.profile.edit') }}">
                                <i class="bi bi-pencil-square"></i> Edit Profil Toko
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('admin.withdrawal.index') }}">
                                <i class="bi bi-cash-stack"></i> Saldo & Tarik
                            </a>
                        </li>
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
</nav>
