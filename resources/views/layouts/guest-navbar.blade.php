<nav class="navbar navbar-expand-lg sticky-top">
    <div class="container">
        <a class="navbar-brand" href="{{ route('home') }}">
            <i class="bi bi-shop"></i> E-Canteen
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('home') }}">Beranda</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('login') }}">Login</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle btn btn-primary text-white ms-2" href="#" role="button" data-bs-toggle="dropdown">
                        Daftar
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('register.user') }}">Daftar Siswa/Guru</a></li>
                        <li><a class="dropdown-item" href="{{ route('register.seller') }}">Daftar Penjual</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>
