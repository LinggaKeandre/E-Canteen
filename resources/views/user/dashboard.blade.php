@extends('layouts.master')

@section('title', 'Dashboard - E-Canteen')

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

    .wishlist-icon {
        position: absolute;
        top: 12px;
        right: 12px;
        width: 36px;
        height: 36px;
        background: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: var(--shadow-sm);
        cursor: pointer;
        transition: all 0.3s ease;
        border: none;
        z-index: 10;
    }

    .wishlist-icon:hover {
        transform: scale(1.1);
    }

    .wishlist-icon i {
        color: #9CA3AF;
        font-size: 1.1rem;
    }

    .wishlist-icon.in-wishlist i {
        color: #EF4444;
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
        text-center: center;
        padding: 3rem;
        color: #6B7280;
    }

    .empty-state i {
        font-size: 4rem;
        color: #E5E7EB;
        margin-bottom: 1rem;
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
                <p class="greeting-text mb-1">Halo, {{ Auth::user()->name }}! 👋</p>
                <p class="greeting-subtitle">Selamat datang di E-Canteen. Mau makan apa hari ini?</p>
            </div>
            <div class="col-md-4 text-md-end">
                <a href="{{ route('user.balance.index') }}" class="quick-action-btn text-decoration-none">
                    <i class="bi bi-plus-circle me-1"></i> Isi Saldo
                </a>
                <a href="{{ route('user.settings.spendingLimit') }}" class="quick-action-btn ms-2 text-decoration-none" style="background: rgba(255,255,255,0.2); color: white;">
                    <i class="bi bi-shield-check me-1"></i> Batas Harian
                </a>
            </div>
        </div>
    </div>
</div>

<div class="container pb-4">
    <!-- Balance Card -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="balance-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="balance-label mb-1">Saldo Virtual</p>
                        <p class="balance-amount mb-2">Rp {{ number_format(Auth::user()->balance, 0, ',', '.') }}</p>
                        <a href="{{ route('user.balance.index') }}" class="text-primary text-decoration-none small">
                            <i class="bi bi-plus-circle me-1"></i> Isi Saldo
                        </a>
                    </div>
                    <div class="text-end">
                        <div class="bg-light d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; border-radius: 12px;">
                            <i class="bi bi-wallet2" style="font-size: 1.5rem; color: var(--primary-orange);"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="balance-card" style="background: var(--primary-orange);">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="balance-label mb-1" style="color: rgba(255,255,255,0.8);">Keranjang Belanja</p>
                        <p class="balance-amount mb-2" style="color: white;">
                            {{ \App\Models\Order::where('user_id', Auth::id())->where('status', 'pending')->count() }}
                            <span style="font-size: 1rem; font-weight: 400;">item</span>
                        </p>
                        <a href="{{ route('cart.index') }}" class="text-white text-decoration-none small" style="color: rgba(255,255,255,0.9) !important;">
                            Lihat Keranjang <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                    <div class="text-end">
                        <div class="bg-white rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="bi bi-cart3" style="font-size: 1.5rem; color: var(--primary-orange);"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Products Today -->
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="section-title mb-0">
                <i class="bi bi-fire"></i> Top Products Hari Ini
            </h5>
            <span class="badge" style="background: var(--light-orange); color: var(--primary-orange);">🔥 Hot Today</span>
        </div>
        @if($topProductsToday->isNotEmpty())
        <div class="row">
            @foreach($topProductsToday as $menu)
                @php
                    $photoUrl = $menu->photo_path ? asset('storage/' . $menu->photo_path) : null;
                    $sellerName = $menu->user?->sellerProfile?->store_name ?? $menu->user?->name ?? 'Toko';
                @endphp
                <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                    <div class="product-card h-100">
                        <!-- Wishlist Icon -->
                        <button type="button" class="wishlist-icon {{ in_array($menu->id, $wishlistMenuIds ?? []) ? 'in-wishlist' : '' }}" onclick="toggleWishlist({{ $menu->id }}, this)" title="{{ in_array($menu->id, $wishlistMenuIds ?? []) ? 'Hapus dari wishlist' : 'Tambah ke wishlist' }}">
                            <i class="bi {{ in_array($menu->id, $wishlistMenuIds ?? []) ? 'bi-heart-fill' : 'bi-heart' }}"></i>
                        </button>
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
                            
                            <button type="button" class="btn btn-primary w-100 mt-auto" onclick="openVariantModal({{ $menu->id }}, '{{ $menu->name }}', {{ $menu->has_variants ?? false }}, {{ $menu->has_addons ?? false }})">
                                <i class="bi bi-cart-plus"></i> Pesan
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-4" style="background: var(--light-orange); border-radius: var(--radius-md);">
            <i class="bi bi-fire" style="font-size: 2rem; color: #9CA3AF;"></i>
            <p class="text-muted mb-0 mt-2">Belum ada top products hari ini</p>
            <p class="small text-muted">Ayo jadi yang pertama memesan!</p>
        </div>
        @endif
    </div>

    <!-- Menu Harian Section -->
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="section-title mb-0">
                <i class="bi bi-calendar-event"></i> Menu Harian {{ $todayName }}
            </h5>
            <span class="badge" style="background: #FEF3C7; color: #92400E;">📅 Spesial Hari Ini</span>
        </div>
        @if($dailyMenus->isNotEmpty())
        <div class="row">
            @foreach($dailyMenus as $menu)
                @php
                    $photoUrl = $menu->photo_path ? asset('storage/' . $menu->photo_path) : null;
                    $sellerName = $menu->user?->sellerProfile?->store_name ?? $menu->user?->name ?? 'Toko';
                @endphp
                <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                    <div class="product-card h-100">
                        <!-- Wishlist Icon -->
                        <button type="button" class="wishlist-icon {{ in_array($menu->id, $wishlistMenuIds ?? []) ? 'in-wishlist' : '' }}" onclick="toggleWishlist({{ $menu->id }}, this)" title="{{ in_array($menu->id, $wishlistMenuIds ?? []) ? 'Hapus dari wishlist' : 'Tambah ke wishlist' }}">
                            <i class="bi {{ in_array($menu->id, $wishlistMenuIds ?? []) ? 'bi-heart-fill' : 'bi-heart' }}"></i>
                        </button>
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
                            
                            <button type="button" class="btn btn-primary w-100 mt-auto" onclick="openVariantModal({{ $menu->id }}, '{{ $menu->name }}', {{ $menu->has_variants ?? false }}, {{ $menu->has_addons ?? false }})">
                                <i class="bi bi-cart-plus"></i> Pesan
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-4" style="background: #FEF3C7; border-radius: var(--radius-md);">
            <i class="bi bi-calendar-event" style="font-size: 2rem; color: #9CA3AF;"></i>
            <p class="text-muted mb-0 mt-2">Belum ada menu harian hari ini</p>
            <p class="small text-muted">Menu harian akan muncul saat admin menambahkannya</p>
        </div>
        @endif
    </div>

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
                        <!-- Wishlist Icon -->
                        <button type="button" class="wishlist-icon {{ in_array($menu->id, $wishlistMenuIds ?? []) ? 'in-wishlist' : '' }}" onclick="toggleWishlist({{ $menu->id }}, this)" title="{{ in_array($menu->id, $wishlistMenuIds ?? []) ? 'Hapus dari wishlist' : 'Tambah ke wishlist' }}">
                            <i class="bi {{ in_array($menu->id, $wishlistMenuIds ?? []) ? 'bi-heart-fill' : 'bi-heart' }}"></i>
                        </button>
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
                            
                            <button type="button" class="btn btn-primary w-100 mt-auto" onclick="openVariantModal({{ $menu->id }}, '{{ $menu->name }}', {{ $menu->has_variants ?? false }}, {{ $menu->has_addons ?? false }})">
                                <i class="bi bi-cart-plus"></i> Pesan
                            </button>
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
            <div class="empty-state text-center">
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
                                @if(($seller->today_order_count ?? 0) >= 7)
                                    <span class="badge bg-danger ms-1"><i class="bi bi-fire"></i> Ramai</span>
                                @endif
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
</div>

<!-- Quick Add to Cart Modal -->
<div class="modal fade" id="quickAddModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title">Tambah ke Keranjang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="quickAddModalBody">
                <!-- Content loaded via JS -->
            </div>
        </div>
    </div>
</div>

<!-- Variant Selection Modal -->
<div class="modal fade" id="variantSelectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title" id="variantModalTitle">Pilih Varian</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="variantForm" method="POST">
                @csrf
                <div class="modal-body" id="variantModalBody">
                    <!-- Variant options will be loaded here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Tambah ke Keranjang</button>
                </div>
            </form>
        </div>
    </div>
</div>

@section('scripts')
<script>
let allMenus = [];

function openVariantModal(menuId, menuName, hasVariants, hasAddons) {
    const csrfToken = "{{ csrf_token() }}";
    const variantModal = new bootstrap.Modal(document.getElementById('variantSelectModal'));
    const variantForm = document.getElementById('variantForm');
    const variantModalTitle = document.getElementById('variantModalTitle');
    const variantModalBody = document.getElementById('variantModalBody');
    
    variantForm.action = `/cart/${menuId}/add`;
    variantModalTitle.textContent = `Pilih Varian - ${menuName}`;
    
    variantModalBody.innerHTML = '<div class="text-center py-4"><div class="spinner-border text-primary"></div></div>';
    
    variantModal.show();
    
    fetch(`/api/menu/${menuId}/details`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const menu = data.menu;
                let html = `<input type="hidden" name="qty" value="1">`;
                
                if (menu.has_variants && menu.variants && menu.variants.length > 0) {
                    html += `<div class="mb-3"><label class="form-label fw-bold">Pilih Varian <span class="text-danger">*</span></label><div class="d-flex flex-wrap gap-2">`;
                    menu.variants.forEach(variant => {
                        html += `<div class="form-check"><input class="form-check-input" type="radio" name="variant_id" id="variant_${variant.id}" value="${variant.id}" required><label class="form-check-label" for="variant_${variant.id}">${variant.name} ${variant.price_adjustment > 0 ? `(+Rp ${new Intl.NumberFormat('id-ID').format(variant.price_adjustment)})` : ''}</label></div>`;
                    });
                    html += `</div></div>`;
                }
                
                if (menu.has_addons && menu.addons && menu.addons.length > 0) {
                    html += `<div class="mb-3"><label class="form-label fw-bold">Pilih Add-On (Opsional)</label><div class="d-flex flex-wrap gap-2">`;
                    menu.addons.forEach(addon => {
                        html += `<div class="form-check"><input class="form-check-input" type="checkbox" name="addons[]" id="addon_${addon.id}" value="${addon.id}"><label class="form-check-label" for="addon_${addon.id}">${addon.name} <span class="text-success">(+Rp ${new Intl.NumberFormat('id-ID').format(addon.price)})</span></label></div>`;
                    });
                    html += `</div></div>`;
                }
                
                html += `<div class="mb-3"><label class="form-label fw-bold">Catatan Pembeli (Opsional)</label><textarea class="form-control" name="notes" rows="2" placeholder="Contoh: Saya ingin tanpa bawang"></textarea></div>`;
                
                variantModalBody.innerHTML = html;
            } else {
                variantModalBody.innerHTML = '<div class="alert alert-danger">Gagal memuat detail menu.</div>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            variantModalBody.innerHTML = '<div class="alert alert-danger">Terjadi kesalahan.</div>';
        });
}

document.addEventListener('DOMContentLoaded', function() {
    const csrfToken = "{{ csrf_token() }}";
    const sellerCards = document.querySelectorAll('.seller-card');
    const sellersGrid = document.getElementById('sellers-grid');
    const productsSection = document.getElementById('products-section');
    const productsGrid = document.getElementById('products-grid');
    const selectedSellerNameEl = document.getElementById('selected-seller-name-dashboard');
    const backBtn = document.getElementById('back-to-sellers-btn');
    const categoryFilter = document.getElementById('category-filter');
    
    const allProductsCategoryFilter = document.getElementById('all-products-category-filter');
    const allProductsGrid = document.getElementById('all-products-grid');
    const allProductCards = document.querySelectorAll('.all-product-card');
    
    allProductsCategoryFilter.addEventListener('change', function() {
        const selectedCategory = this.value;
        
        allProductCards.forEach(card => {
            const cardCategory = card.dataset.category || '';
            card.style.display = (selectedCategory === '' || cardCategory === selectedCategory) ? 'block' : 'none';
        });
    });

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

            let buttonHtml = `<button type="button" class="btn btn-primary flex-grow-1" onclick="openVariantModal(${menu.id}, '${menu.name}', ${menu.has_variants || false}, ${menu.has_addons || false})">Pesan</button>`;

            productsHtml += `<div class="col-lg-3 col-md-4 col-sm-6 mb-4"><div class="product-card h-100">${photoUrl ? `<img src="${photoUrl}" class="product-card-img" alt="${menu.name}">` : '<div class="bg-light d-flex align-items-center justify-content-center product-card-img"><i class="bi bi-image text-muted display-4"></i></div>'}<div class="product-card-body d-flex flex-column">${categoryBadge}<h6 class="product-card-title">${menu.name}</h6><p class="product-card-price mb-2">Rp ${new Intl.NumberFormat('id-ID').format(menu.price)}</p>${menu.rating_count > 0 ? `<div class="mb-2">${starsHtml}<span class="text-muted small"> ${rating} (${menu.rating_count})</span></div>` : '<div class="mb-2 text-muted small"><i class="bi bi-star"></i> Belum ada rating</div>'}${buttonHtml}</div></div></div>`;
        });

        productsGrid.innerHTML = productsHtml;
    }

    categoryFilter.addEventListener('change', function() {
        const selectedCategory = this.value;
        renderMenus(selectedCategory === '' ? allMenus : allMenus.filter(menu => menu.category === selectedCategory));
    });

    sellerCards.forEach(card => {
        card.addEventListener('click', function() {
            const sellerId = this.dataset.sellerId;
            const sellerName = this.dataset.sellerName;

            categoryFilter.value = '';
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

    backBtn.addEventListener('click', function() {
        sellersGrid.classList.remove('d-none');
        productsSection.classList.add('d-none');
        allMenus = [];
    });
});

function quickAddToCart(menuId) {
    const csrfToken = "{{ csrf_token() }}";
    
    fetch(`/cart/${menuId}/add`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-CSRF-TOKEN': csrfToken
        },
        body: 'qty=1'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Berhasil ditambahkan ke keranjang!');
            location.reload();
        } else {
            alert(data.message || 'Gagal menambahkan ke keranjang');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat menambahkan ke keranjang');
    });
}

function toggleWishlist(menuId, button) {
    const csrfToken = "{{ csrf_token() }}";
    
    fetch(`/wishlist/${menuId}/toggle`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            if (data.inWishlist) {
                button.classList.add('in-wishlist');
                button.querySelector('i').classList.remove('bi-heart');
                button.querySelector('i').classList.add('bi-heart-fill');
                button.title = 'Hapus dari wishlist';
            } else {
                button.classList.remove('in-wishlist');
                button.querySelector('i').classList.remove('bi-heart-fill');
                button.querySelector('i').classList.add('bi-heart');
                button.title = 'Tambah ke wishlist';
            }
        } else {
            alert(data.message || 'Terjadi kesalahan');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat memproses wishlist');
    });
}
</script>
@endsection
