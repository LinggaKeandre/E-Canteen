@extends('layouts.master')

@section('title', 'Wishlist - E-Canteen')

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

    .wishlist-header {
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

    .section-title {
        font-weight: 700;
        font-size: 1.25rem;
        margin-bottom: 1rem;
    }

    .section-title i {
        color: var(--primary-orange);
    }

    .product-card {
        background: var(--white);
        border-radius: var(--radius-md);
        box-shadow: var(--shadow-sm);
        overflow: hidden;
        transition: all 0.3s ease;
        border: 1px solid #E5E7EB;
        height: 100%;
        display: flex;
        flex-direction: column;
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
        display: flex;
        flex-direction: column;
        flex-grow: 1;
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
        color: var(--primary-orange);
        font-size: 1.1rem;
    }

    .wishlist-icon.in-wishlist i {
        color: #EF4444;
    }

    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        background: var(--light-orange);
        border-radius: var(--radius-md);
    }

    .empty-state i {
        font-size: 4rem;
        color: #E5E7EB;
        margin-bottom: 1rem;
    }

    .empty-state h5 {
        color: #6B7280;
        margin-bottom: 0.5rem;
    }

    .empty-state p {
        color: #9CA3AF;
        margin-bottom: 1.5rem;
    }

    .btn-primary {
        background: var(--primary-orange);
        border-color: var(--primary-orange);
    }

    .btn-primary:hover {
        background: #E56A0D;
        border-color: #E56A0D;
    }

    @media (max-width: 768px) {
        .wishlist-header {
            padding: 1.5rem 0;
            margin-bottom: 1.5rem;
        }
        
        .greeting-text {
            font-size: 1.25rem;
        }
    }
</style>
@endsection

@section('content')
<!-- Hero Section -->
<div class="wishlist-header">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <p class="greeting-text mb-1">Wishlist Saya ❤️</p>
                <p class="greeting-subtitle">Menu favorit yang ingin Anda pesan</p>
            </div>
            <div class="col-md-4 text-md-end">
                <a href="{{ route('user.dashboard') }}" class="quick-action-btn text-decoration-none" style="background: rgba(255,255,255,0.2); color: white;">
                    <i class="bi bi-arrow-left me-1"></i> Kembali ke Menu
                </a>
            </div>
        </div>
    </div>
</div>

<div class="container pb-4">
    <!-- Wishlist Items -->
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="section-title mb-0">
                <i class="bi bi-heart-fill"></i> Daftar Wishlist
            </h5>
            <span class="badge" style="background: var(--light-orange); color: var(--primary-orange);">
                {{ $wishlistMenus->count() }} Menu
            </span>
        </div>

        @if($wishlistMenus->isNotEmpty())
        <div class="row">
            @foreach($wishlistMenus as $menu)
                @php
                    $photoUrl = $menu->photo_path ? asset('storage/' . $menu->photo_path) : null;
                    $sellerName = $menu->user?->sellerProfile?->store_name ?? $menu->user?->name ?? 'Toko';
                @endphp
                <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                    <div class="product-card h-100">
                        <!-- Wishlist Icon -->
                        <button type="button" class="wishlist-icon in-wishlist" onclick="toggleWishlist({{ $menu->id }}, this)" title="Hapus dari wishlist">
                            <i class="bi bi-heart-fill"></i>
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
        <div class="empty-state">
            <i class="bi bi-heart"></i>
            <h5>Wishlist Kosong</h5>
            <p>Anda belum menambahkan menu apapun ke wishlist.</p>
            <a href="{{ route('user.dashboard') }}" class="btn btn-primary">
                <i class="bi bi-shop me-2"></i>Mulai Jelajahi Menu
            </a>
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
            if (!data.inWishlist) {
                // Remove the card from the view
                const card = button.closest('.col-lg-3');
                card.remove();
                
                // Check if there are any remaining items
                const remainingCards = document.querySelectorAll('.product-card');
                if (remainingCards.length === 0) {
                    location.reload();
                }
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
</script>
@endsection

