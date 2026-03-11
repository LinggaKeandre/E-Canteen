@extends('layouts.master')

@section('title', 'Menu - E-Canteen')

@section('content')
<div class="container py-4">
    <h2 class="mb-4"><i class="bi bi-menu-button-wide"></i> Menu Tersedia</h2>
    
    <!-- Category Filter -->
    <div class="mb-4">
        <form method="GET" action="{{ route('user.menus.index') }}" class="d-flex gap-2 align-items-center">
            <label for="category" class="fw-bold">Filter Kategori:</label>
            <select name="category" id="category" class="form-select" style="max-width: 250px;" onchange="this.form.submit()">
                <option value="">Semua Kategori</option>
                @foreach(\App\Models\Menu::getCategories() as $cat)
                    <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                @endforeach
            </select>
            @if(request('category'))
                <a href="{{ route('user.menus.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
            @endif
        </form>
    </div>
    
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    
    @if(session('error'))
        <div class="alert alert-error">{{ session('error') }}</div>
    @endif
    
    @if($menus->isEmpty())
        <div class="text-center py-5">
            <i class="bi bi-emoji-frown display-1 text-muted"></i>
            <p class="mt-3">Belum ada menu tersedia saat ini.</p>
        </div>
    @else
        <div class="row">
            @foreach($menus as $menu)
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card menu-card h-100">
                        @if($menu->photo_path)
                            <img src="{{ asset('storage/' . $menu->photo_path) }}" class="card-img-top menu-img" alt="{{ $menu->name }}">
                        @else
                            <div class="bg-light d-flex align-items-center justify-content-center menu-img">
                                <i class="bi bi-image display-4 text-muted"></i>
                            </div>
                        @endif
                        <div class="card-body">
                            @if($menu->category)
                                <span class="badge bg-info mb-2">{{ $menu->category }}</span>
                            @endif
                            <h5 class="card-title">{{ $menu->name }}</h5>
                            <p class="price-tag">Rp {{ number_format($menu->price, 0, ',', '.') }}</p>
                            
                            <!-- Rating Display -->
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
                                        {{ number_format($menu->average_rating, 1) }} ({{ $menu->rating_count }} rating)
                                    </span>
                                </div>
                            @else
                                <div class="mb-2 text-muted small">
                                    <i class="bi bi-star"></i> Belum ada rating
                                </div>
                            @endif
                            
                            @if($menu->status === 'tersedia')
                                <span class="badge bg-success">Tersedia</span>
                            @else
                                <span class="badge bg-danger">Habis</span>
                            @endif
                        </div>
                        <div class="card-footer bg-white">
                            @if($menu->status === 'tersedia')
                                @if($menu->has_variants || $menu->has_addons)
                                    <button type="button" class="btn btn-primary w-100" onclick="openVariantModal({{ $menu->id }}, '{{ $menu->name }}', {{ $menu->has_variants }}, {{ $menu->has_addons }})">
                                        <i class="bi bi-cart-plus"></i> Pilih
                                    </button>
                                @else
                                    <form action="{{ route('cart.add', $menu) }}" method="POST" class="d-flex gap-2">
                                        @csrf
                                        <input type="number" name="qty" value="1" min="1" class="form-control" style="width: 70px;">
                                        <button type="submit" class="btn btn-primary flex-grow-1">
                                            <i class="bi bi-cart-plus"></i> Pesan
                                        </button>
                                    </form>
                                @endif
                            @else
                                <button class="btn btn-secondary w-100" disabled>Habis</button>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
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
@endsection

@section('scripts')
<script>
// Function to open variant selection modal
function openVariantModal(menuId, menuName, hasVariants, hasAddons) {
    const csrfToken = "{{ csrf_token() }}";
    const variantModal = new bootstrap.Modal(document.getElementById('variantSelectModal'));
    const variantForm = document.getElementById('variantForm');
    const variantModalTitle = document.getElementById('variantModalTitle');
    const variantModalBody = document.getElementById('variantModalBody');
    
    variantForm.action = `/cart/${menuId}/add`;
    variantModalTitle.textContent = `Pilih Varian - ${menuName}`;
    
    // Show loading
    variantModalBody.innerHTML = '<div class="text-center py-4"><div class="spinner-border text-primary"></div></div>';
    
    variantModal.show();
    
    // Fetch menu details
    fetch(`/api/menu/${menuId}/details`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const menu = data.menu;
                let html = `
                    <input type="hidden" name="qty" value="1">
                `;
                
                // Variants section
                if (menu.has_variants && menu.variants && menu.variants.length > 0) {
                    html += `
                        <div class="mb-3">
                            <label class="form-label fw-bold">Pilih Varian <span class="text-danger">*</span></label>
                            <div class="d-flex flex-wrap gap-2">
                    `;
                    menu.variants.forEach(variant => {
                        html += `
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="variant_id" id="variant_${variant.id}" value="${variant.id}" required>
                                <label class="form-check-label" for="variant_${variant.id}">
                                    ${variant.name}
                                    ${variant.price_adjustment > 0 ? `<span class="text-success">(+Rp ${new Intl.NumberFormat('id-ID').format(variant.price_adjustment)})</span>` : ''}
                                </label>
                            </div>
                        `;
                    });
                    html += `</div></div>`;
                }
                
                // Addons section
                if (menu.has_addons && menu.addons && menu.addons.length > 0) {
                    html += `
                        <div class="mb-3">
                            <label class="form-label fw-bold">Pilih Add-On (Opsional)</label>
                            <div class="d-flex flex-wrap gap-2">
                    `;
                    menu.addons.forEach(addon => {
                        html += `
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="addons[]" id="addon_${addon.id}" value="${addon.id}">
                                <label class="form-check-label" for="addon_${addon.id}">
                                    ${addon.name}
                                    <span class="text-success">(+Rp ${new Intl.NumberFormat('id-ID').format(addon.price)})</span>
                                </label>
                            </div>
                        `;
                    });
                    html += `</div></div>`;
                }
                
                // Notes section
                html += `
                    <div class="mb-3">
                        <label class="form-label fw-bold">Catatan Pembeli (Opsional)</label>
                        <textarea class="form-control" name="notes" rows="2" placeholder="Contoh: Saya ingin tanpa bawang"></textarea>
                    </div>
                `;
                
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
