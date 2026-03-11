<nav class="navbar navbar-expand-lg sticky-top">
    <div class="container">
        <a class="navbar-brand" href="{{ route('user.dashboard') }}">
            <i class="bi bi-shop"></i> E-Canteen
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('user.wishlist.index') }}">
                        <i class="bi bi-heart"></i> Wishlist
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('cart.index') }}">
                        <i class="bi bi-cart3"></i> Keranjang
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('user.orders.index') }}">
                        <i class="bi bi-receipt"></i> Pesanan
                    </a>
                </li>
            </ul>
            <ul class="navbar-nav align-items-center">
                <li class="nav-item me-3">
                    <a class="nav-link" href="{{ route('user.balance.index') }}" style="color: var(--primary-orange);">
                        <i class="bi bi-wallet2"></i> Rp {{ number_format(Auth::user()->balance, 0, ',', '.') }}
                    </a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
                        <div class="bg-light-orange rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                            <i class="bi bi-person-fill" style="color: var(--primary-orange);"></i>
                        </div>
                        {{ Auth::user()->name }}
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><span class="dropdown-item-text text-muted">Role: {{ Auth::user()->role }}</span></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item">
                                    <i class="bi bi-box-arrow-right me-2"></i>Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

<style>
    .bg-light-orange {
        background-color: var(--light-orange);
    }
</style>
