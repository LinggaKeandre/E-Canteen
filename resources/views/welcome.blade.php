@extends('layouts.master')

@section('title', 'E-Canteen - Pre-Order Kantin Sekolah')

@section('styles')
<style>
    :root {
        --primary-orange: #FF750F;
        --light-orange: #FFF3E6;
        --white: #FFFFFF;
        --shadow-sm: 0 1px 2px rgba(0,0,0,0.05);
        --shadow-md: 0 4px 6px rgba(0,0,0,0.07);
        --radius-lg: 16px;
        --radius-md: 12px;
    }

    .dashboard-header {
        background-color: var(--primary-orange);
        padding: 2rem 0;
        margin-bottom: 2rem;
    }

    .greeting-text {
        color: white;
        font-size: 1.5rem;
        font-weight: 600;
    }

    .greeting-subtitle {
        color: rgba(255,255,255,0.9);
        font-size: 0.95rem;
    }

    .balance-card {
        background: var(--white);
        border-radius: var(--radius-md);
        box-shadow: var(--shadow-sm);
        padding: 1.5rem;
        border: 1px solid #E5E7EB;
    }

    .balance-amount {
        font-size: 2rem;
        font-weight: 700;
        color: var(--primary-orange);
    }

    .balance-label {
        color: #6B7280;
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .top-product-card {
        background: var(--white);
        border-radius: var(--radius-md);
        box-shadow: var(--shadow-sm);
        overflow: hidden;
        transition: all 0.3s ease;
        cursor: pointer;
        height: 100%;
        border: 1px solid #E5E7EB;
    }

    .top-product-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-md);
    }

    .top-product-img {
        width: 100%;
        height: 140px;
        object-fit: cover;
    }

    .top-product-badge {
        position: absolute;
        top: 12px;
        left: 12px;
        background: var(--primary-orange);
        color: white;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .top-product-body {
        padding: 1rem;
    }

    .top-product-title {
        font-weight: 600;
        font-size: 0.95rem;
        margin-bottom: 0.5rem;
    }

    .top-product-price {
        color: var(--primary-orange);
        font-weight: 700;
        font-size: 1.1rem;
    }

    .top-product-sold {
        font-size: 0.75rem;
        color: #10B981;
        font-weight: 500;
    }

    .seller-card {
        background: var(--white);
        border-radius: var(--radius-md);
        box-shadow: var(--shadow-sm);
        overflow: hidden;
        transition: all 0.3s ease;
        cursor: pointer;
        height: 100%;
        border: 1px solid #E5E7EB;
    }

    .seller-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-md);
        border-color: var(--primary-orange);
    }

    .seller-banner {
        height: 100px;
        object-fit: cover;
        width: 100%;
        background: var(--primary-orange);
    }

    .seller-logo {
        width: 56px;
        height: 56px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid white;
        margin-top: -28px;
        margin-left: 1rem;
        background: white;
        box-shadow: var(--shadow-sm);
    }

    .seller-name {
        font-weight: 700;
        font-size: 1rem;
        margin-bottom: 0.25rem;
    }

    .seller-desc {
        color: #6B7280;
        font-size: 0.8rem;
        margin-bottom: 0.75rem;
    }

    .seller-stats {
        display: flex;
        gap: 1rem;
        font-size: 0.8rem;
        color: #6B7280;
    }

    .seller-stats i {
        color: var(--primary-orange);
    }

    .section-title {
        font-weight: 700;
        font-size: 1.25rem;
        margin-bottom: 1rem;
    }

    .section-title i {
        color: var(--primary-orange);
    }

    .quick-action-btn {
        background: white;
        border: none;
        color: var(--primary-orange);
        padding: 0.6rem 1.25rem;
        border-radius: 25px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .quick-action-btn:hover {
        background: var(--light-orange);
        color: var(--primary-orange);
    }

    .top-products-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
        gap: 1rem;
    }

    .sellers-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 1.25rem;
    }

    .product-card {
        background: var(--white);
        border-radius: var(--radius-md);
        box-shadow: var(--shadow-sm);
        overflow: hidden;
        transition: all 0.3s ease;
        border: 1px solid #E5E7EB;
        position: relative;
    }

    .product-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-md);
    }

    .product-card-img {
        width: 100%;
        height: 150px;
        object-fit: cover;
    }

    .product-card-body {
        padding: 1rem;
    }

    .product-card-title {
        font-weight: 600;
        font-size: 0.95rem;
        margin-bottom: 0.5rem;
    }

    .product-card-price {
        color: var(--primary-orange);
        font-weight: 700;
        font-size: 1.1rem;
    }

    .empty-state {
        text-align: center;
        padding: 3rem;
        color: #6B7280;
    }

    .empty-state i {
        font-size: 4rem;
        color: #E5E7EB;
        margin-bottom: 1rem;
    }

    .hero-cta-btn {
        background: white;
        color: var(--primary-orange);
        padding: 0.75rem 1.5rem;
        border-radius: 25px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .hero-cta-btn:hover {
        background: var(--light-orange);
        color: var(--primary-orange);
    }

    @media (max-width: 768px) {
        .dashboard-header {
            padding: 1.5rem 0;
            margin-bottom: 1.5rem;
        }
        
        .greeting-text {
            font-size: 1.25rem;
        }
        
        .balance-amount {
            font-size: 1.5rem;
        }
        
        .top-products-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
</style>
@endsection

@section('content')
<!-- Hero Section -->
<div class="dashboard-header">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <p class="greeting-text mb-1">Selamat Datang di E-Canteen! 👋</p>
                <p class="greeting-subtitle">Pre-Order Kantin Sekarang Lebih Mudah & Praktis</p>
            </div>
            <div class="col-md-4 text-md-end">
                <a href="{{ route('login') }}" class="hero-cta-btn text-decoration-none">
                    <i class="bi bi-box-arrow-in-right me-1"></i> Login
                </a>
            </div>
        </div>
    </div>
</div>

<div class="container pb-4">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <!-- Top Products Today -->
    @if($topProductsToday->isNotEmpty())
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="section-title mb-0">
                <i class="bi bi-fire"></i> Top Products Hari Ini
            </h5>
            <span class="badge" style="background: var(--light-orange); color: var(--primary-orange);">🔥 Hot Today</span>
        </div>
        <div class="row">
            @foreach($topProductsToday as $menu)
                @php
                    $photoUrl = $menu->photo_path ? asset('storage/' . $menu->photo_path) : null;
                    $sellerName = $menu->user?->sellerProfile?->store_name ?? $menu->user?->name ?? 'Toko';
                @endphp
                <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                    <div class="product-card h-100">
                        @if($photoUrl)
                            <img src="{{ $photoUrl }}" class="product-card-img" alt="{{ $menu->name }}">
                        @else
                            <div class="bg-light d-flex align-items-center justify-content-center product-card-img">
                                <i class="bi bi-image text-muted display-4"></i>
                            </div>
                        @endif
                        <div class="product-card-body d-flex flex-column">
                            @if($menu->category)
                                <span class="badge mb-2" style="background: var(--light-orange); color: var(--primary-orange);">{{ $menu->category }}</span>
                            @endif
                            <h6 class="product-card-title">{{ $menu->name }}</h6>
                            <p class="text-muted small mb-1">
                                <i class="bi bi-shop me-1"></i>{{ $sellerName }}
                            </p>
                            <p class="product-card-price mb-2">Rp {{ number_format($menu->price, 0, ',', '.') }}</p>
                            
                            @if($menu->rating_count > 0)
                                <div class="mb-2">
                                    <span class="text-warning">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= floor($menu->average_rating))
                                                <i class="bi bi-star-fill"></i>
                                            @elseif($i <= ceil($menu->average_rating) && $i > floor($menu->average_rating))
                                                <i class="bi bi-star-half"></i>
                                            @else
                                                <i class="bi bi-star"></i>
                                            @endif
                                        @endfor
                                    </span>
                                    <span class="text-muted small">
                                        {{ number_format($menu->average_rating, 1) }} ({{ $menu->rating_count }})
                                    </span>
                                </div>
                            @else
                                <div class="mb-2 text-muted small">
                                    <i class="bi bi-star"></i> Belum ada rating
                                </div>
                            @endif
                            
                            <a href="{{ route('login') }}" class="btn btn-outline-primary w-100 mt-auto">
                                <i class="bi bi-box-arrow-in-right"></i> Login untuk Pesan
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Menu Harian Section -->
    @if($dailyMenus->isNotEmpty())
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="section-title mb-0">
                <i class="bi bi-calendar-event"></i> Menu Harian {{ $todayName }}
            </h5>
            <span class="badge" style="background: #FEF3C7; color: #92400E;">📅 Spesial Hari Ini</span>
        </div>
        <div class="row">
            @foreach($dailyMenus as $menu)
                @php
                    $photoUrl = $menu->photo_path ? asset('storage/' . $menu->photo_path) : null;
                    $sellerName = $menu->user?->sellerProfile?->store_name ?? $menu->user?->name ?? 'Toko';
                @endphp
                <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                    <div class="product-card h-100">
                        @if($photoUrl)
                            <img src="{{ $photoUrl }}" class="product-card-img" alt="{{ $menu->name }}">
                        @else
                            <div class="bg-light d-flex align-items-center justify-content-center product-card-img">
                                <i class="bi bi-image text-muted display-4"></i>
                            </div>
                        @endif
                        <div class="product-card-body d-flex flex-column">
                            @if($menu->category)
                                <span class="badge mb-2" style="background: var(--light-orange); color: var(--primary-orange);">{{ $menu->category }}</span>
                            @endif
                            <h6 class="product-card-title">{{ $menu->name }}</h6>
                            <p class="text-muted small mb-1">
                                <i class="bi bi-shop me-1"></i>{{ $sellerName }}
                            </p>
                            <p class="product-card-price mb-2">Rp {{ number_format($menu->price, 0, ',', '.') }}</p>
                            
                            @if($menu->rating_count > 0)
                                <div class="mb-2">
                                    <span class="text-warning">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= floor($menu->average_rating))
                                                <i class="bi bi-star-fill"></i>
                                            @elseif($i <= ceil($menu->average_rating) && $i > floor($menu->average_rating))
                                                <i class="bi bi-star-half"></i>
                                            @else
                                                <i class="bi bi-star"></i>
                                            @endif
                                        @endfor
                                    </span>
                                    <span class="text-muted small">
                                        {{ number_format($menu->average_rating, 1) }} ({{ $menu->rating_count }})
                                    </span>
                                </div>
                            @else
                                <div class="mb-2 text-muted small">
                                    <i class="bi bi-star"></i> Belum ada rating
                                </div>
                            @endif
                            
                            <a href="{{ route('login') }}" class="btn btn-outline-primary w-100 mt-auto">
                                <i class="bi bi-box-arrow-in-right"></i> Login untuk Pesan
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- All Products Section -->
    @if($allMenus->isNotEmpty())
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="section-title mb-0">
                <i class="bi bi-grid"></i> Semua Produk
            </h5>
            <span class="badge bg-primary">{{ $allMenus->count() }} Menu</span>
        </div>
        
        <!-- Category Filter for All Products -->
        <div class="mb-3">
            <label for="all-products-category-filter" class="form-label fw-bold">Filter Kategori:</label>
            <select id="all-products-category-filter" class="form-select" style="max-width: 300px;">
                <option value="">Semua Kategori</option>
                @foreach(\App\Models\Menu::getCategories() as $category)
                    <option value="{{ $category }}">{{ $category }}</option>
                @endforeach
            </select>
        </div>
        
        <div class="row" id="all-products-grid">
            @foreach($allMenus as $menu)
                @php
                    $photoUrl = $menu->photo_path ? asset('storage/' . $menu->photo_path) : null;
                    $sellerName = $menu->user?->sellerProfile?->store_name ?? $menu->user?->name ?? 'Toko';
                @endphp
                <div class="col-lg-3 col-md-4 col-sm-6 mb-4 all-product-card" data-category="{{ $menu->category ?? '' }}">
                    <div class="product-card h-100">
                        @if($photoUrl)
                            <img src="{{ $photoUrl }}" class="product-card-img" alt="{{ $menu->name }}">
                        @else
                            <div class="bg-light d-flex align-items-center justify-content-center product-card-img">
                                <i class="bi bi-image text-muted display-4"></i>
                            </div>
                        @endif
                        <div class="product-card-body d-flex flex-column">
                            @if($menu->category)
                                <span class="badge mb-2" style="background: var(--light-orange); color: var(--primary-orange);">{{ $menu->category }}</span>
                            @endif
                            <h6 class="product-card-title">{{ $menu->name }}</h6>
                            <p class="text-muted small mb-1">
                                <i class="bi bi-shop me-1"></i>{{ $sellerName }}
                            </p>
                            <p class="product-card-price mb-2">Rp {{ number_format($menu->price, 0, ',', '.') }}</p>
                            
                            @if($menu->rating_count > 0)
                                <div class="mb-2">
                                    <span class="text-warning">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= floor($menu->average_rating))
                                                <i class="bi bi-star-fill"></i>
                                            @elseif($i <= ceil($menu->average_rating) && $i > floor($menu->average_rating))
                                                <i class="bi bi-star-half"></i>
                                            @else
                                                <i class="bi bi-star"></i>
                                            @endif
                                        @endfor
                                    </span>
                                    <span class="text-muted small">
                                        {{ number_format($menu->average_rating, 1) }} ({{ $menu->rating_count }})
                                    </span>
                                </div>
                            @else
                                <div class="mb-2 text-muted small">
                                    <i class="bi bi-star"></i> Belum ada rating
                                </div>
                            @endif
                            
                            <a href="{{ route('login') }}" class="btn btn-outline-primary w-100 mt-auto">
                                <i class="bi bi-box-arrow-in-right"></i> Login untuk Pesan
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Sellers Section -->
    <div class="mb-4">
        <h5 class="section-title">
            <i class="bi bi-shop"></i> Pilih Toko
        </h5>
        
        @if($sellers->isEmpty())
            <div class="empty-state">
                <i class="bi bi-shop-window"></i>
                <p>Belum ada toko yang buka hari ini.</p>
            </div>
        @else
            <!-- Sellers Grid View -->
            <div id="sellers-grid" class="sellers-grid">
                @foreach($sellers as $seller)
                    <div class="seller-card" data-seller-id="{{ $seller->id }}" data-seller-name="{{ $seller->sellerProfile?->store_name ?? $seller->name }}">
                        @if($seller->sellerProfile?->store_banner)
                            <img src="{{ asset('storage/' . $seller->sellerProfile->store_banner) }}" class="seller-banner" alt="Banner">
                        @else
                            <div class="seller-banner"></div>
                        @endif
                        
                        @if($seller->sellerProfile?->store_logo)
                            <img src="{{ asset('storage/' . $seller->sellerProfile->store_logo) }}" class="seller-logo" alt="Logo">
                        @else
                            <div class="seller-logo d-flex align-items-center justify-content-center">
                                <i class="bi bi-shop text-muted"></i>
                            </div>
                        @endif
                        
                        <div class="px-3 pb-3">
                            <h6 class="seller-name mt-2">{{ $seller->sellerProfile?->store_name ?? $seller->name }}</h6>
                            <p class="seller-desc">{{ $seller->sellerProfile?->store_description ?? 'Toko dengan berbagai menu pilihan' }}</p>
                            <div class="seller-stats">
                                <span><i class="bi bi-menu-button"></i> {{ $seller->menus->count() }} Menu</span>
                                <span><i class="bi bi-star-fill"></i> {{ number_format($seller->sellerProfile?->average_rating ?? 0, 1) }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Products Section (Hidden by default) -->
            <div id="products-section" class="d-none">
                <div class="d-flex align-items-center mb-4">
                    <button class="btn btn-outline-secondary btn-sm" id="back-to-sellers-btn">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </button>
                    <h5 class="mb-0 ms-3 fw-bold" id="selected-seller-name-dashboard"></h5>
                </div>
                
                <!-- Category Filter -->
                <div class="mb-3">
                    <label for="category-filter" class="form-label fw-bold">Filter Kategori:</label>
                    <select id="category-filter" class="form-select" style="max-width: 300px;">
                        <option value="">Semua Kategori</option>
                        @foreach(\App\Models\Menu::getCategories() as $category)
                            <option value="{{ $category }}">{{ $category }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="row" id="products-grid"></div>
            </div>
        @endif
    </div>
    
    <!-- CTA Section for Guests -->
    <div class="text-center mt-5 py-5 bg-light rounded">
        <h4 class="mb-3">Mulai Pre-Order Sekarang!</h4>
        <p class="text-muted mb-4">Daftar sekarang untuk memesan makanan favorit Anda dengan mudah</p>
        <div class="d-flex gap-3 justify-content-center flex-wrap">
            <a href="{{ route('register.user') }}" class="btn btn-primary btn-lg">
                <i class="bi bi-person-plus"></i> Daftar Siswa/Guru
            </a>
            <a href="{{ route('register.seller') }}" class="btn btn-outline-primary btn-lg">
                <i class="bi bi-shop"></i> Daftar Penjual
            </a>
        </div>
    </div>
</div>

@section('scripts')
<script>
let allMenus = [];

document.addEventListener('DOMContentLoaded', function() {
    const csrfToken = "{{ csrf_token() }}";
    
    // Category filter for all products
    const allProductsCategoryFilter = document.getElementById('all-products-category-filter');
    const allProductCards = document.querySelectorAll('.all-product-card');
    
    if (allProductsCategoryFilter) {
        allProductsCategoryFilter.addEventListener('change', function() {
            const selectedCategory = this.value;
            
            allProductCards.forEach(card => {
                const cardCategory = card.dataset.category || '';
                card.style.display = (selectedCategory === '' || cardCategory === selectedCategory) ? 'block' : 'none';
            });
        });
    }

    // Seller cards functionality
    const sellerCards = document.querySelectorAll('.seller-card');
    const sellersGrid = document.getElementById('sellers-grid');
    const productsSection = document.getElementById('products-section');
    const productsGrid = document.getElementById('products-grid');
    const selectedSellerNameEl = document.getElementById('selected-seller-name-dashboard');
    const backBtn = document.getElementById('back-to-sellers-btn');
    const categoryFilter = document.getElementById('category-filter');

    function renderMenus(menus) {
        if (menus.length === 0) {
            productsGrid.innerHTML = '<div class="col-12 text-center"><p class="text-muted py-5">Tidak ada menu tersedia.</p></div>';
            return;
        }

        let productsHtml = '';
        menus.forEach(menu => {
            const photoUrl = menu.photo_path ? `/storage/${menu.photo_path}` : null;
            const rating = menu.average_rating ? parseFloat(menu.average_rating).toFixed(1) : 'Belum';
            const starsFull = Math.floor(menu.average_rating || 0);
            let starsHtml = '';
            for (let i = 0; i < starsFull; i++) starsHtml += '<i class="bi bi-star-fill text-warning"></i>';

            const categoryBadge = menu.category ? `<span class="badge mb-2" style="background: var(--light-orange); color: var(--primary-orange);">${menu.category}</span>` : '';

            productsHtml += `<div class="col-lg-3 col-md-4 col-sm-6 mb-4"><div class="product-card h-100">${photoUrl ? `<img src="${photoUrl}" class="product-card-img" alt="${menu.name}">` : '<div class="bg-light d-flex align-items-center justify-content-center product-card-img"><i class="bi bi-image text-muted display-4"></i></div>'}<div class="product-card-body d-flex flex-column">${categoryBadge}<h6 class="product-card-title">${menu.name}</h6><p class="product-card-price mb-2">Rp ${new Intl.NumberFormat('id-ID').format(menu.price)}</p>${menu.rating_count > 0 ? `<div class="mb-2">${starsHtml}<span class="text-muted small"> ${rating} (${menu.rating_count})</span></div>` : '<div class="mb-2 text-muted small"><i class="bi bi-star"></i> Belum ada rating</div>'}<a href="{{ route('login') }}" class="btn btn-outline-primary w-100 mt-auto"><i class="bi bi-box-arrow-in-right"></i> Login untuk Pesan</a></div></div></div>`;
        });

        productsGrid.innerHTML = productsHtml;
    }

    if (categoryFilter) {
        categoryFilter.addEventListener('change', function() {
            const selectedCategory = this.value;
            renderMenus(selectedCategory === '' ? allMenus : allMenus.filter(menu => menu.category === selectedCategory));
        });
    }

    sellerCards.forEach(card => {
        card.addEventListener('click', function() {
            const sellerId = this.dataset.sellerId;
            const sellerName = this.dataset.sellerName;

            if (categoryFilter) categoryFilter.value = '';
            productsGrid.innerHTML = '<div class="col-12 text-center py-5"><div class="spinner-border text-primary" role="status"></div></div>';

            fetch(`/api/seller/${sellerId}/menus`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.menus.length > 0) {
                        selectedSellerNameEl.textContent = sellerName;
                        allMenus = data.menus;
                        renderMenus(allMenus);
                        sellersGrid.classList.add('d-none');
                        productsSection.classList.remove('d-none');
                        window.scrollTo({ top: productsSection.offsetTop - 100, behavior: 'smooth' });
                    } else {
                        allMenus = [];
                        productsGrid.innerHTML = '<div class="col-12 text-center"><p class="text-muted py-5">Tidak ada menu tersedia untuk toko ini.</p></div>';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    allMenus = [];
                    productsGrid.innerHTML = '<div class="col-12 text-center"><p class="text-danger py-5">Terjadi kesalahan saat memuat menu.</p></div>';
                });
        });
    });

    if (backBtn) {
        backBtn.addEventListener('click', function() {
            sellersGrid.classList.remove('d-none');
            productsSection.classList.add('d-none');
            allMenus = [];
        });
    }
});
</script>
@endsection
@endsection

