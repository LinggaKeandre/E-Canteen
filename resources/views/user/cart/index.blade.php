@extends('layouts.master')

@section('title', 'Keranjang - E-Canteen')

@section('styles')
<style>
    .cart-header {
        background-color: var(--primary-orange);
        padding: 1.5rem 0;
        margin-bottom: 1.5rem;
    }
    
    .cart-item {
        background: white;
        border: 1px solid #E5E7EB;
        border-radius: 12px;
        padding: 1rem;
        margin-bottom: 1rem;
        transition: all 0.2s;
    }
    
    .cart-item:hover {
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }
    
    .cart-item.unavailable {
        opacity: 0.6;
        background: #F9FAFB;
    }
    
    .cart-item-img {
        width: 70px;
        height: 70px;
        object-fit: cover;
        border-radius: 8px;
    }
    
    .checkout-card {
        background: white;
        border: 1px solid #E5E7EB;
        border-radius: 12px;
        position: sticky;
        top: 80px;
    }
    
    .summary-row {
        display: flex;
        justify-content: space-between;
        padding: 0.5rem 0;
    }
    
    .summary-row.total {
        border-top: 2px solid #E5E7EB;
        margin-top: 0.5rem;
        padding-top: 1rem;
        font-weight: 700;
        font-size: 1.1rem;
    }
    
    .empty-cart-icon {
        font-size: 5rem;
        color: #E5E7EB;
    }
</style>
@endsection

@section('content')
<div class="cart-header">
    <div class="container">
        <h2 class="mb-0 text-white"><i class="bi bi-cart3 me-2"></i>Keranjang Belanja</h2>
    </div>
</div>

<div class="container pb-4">
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
    
    @if(empty($cart))
        <div class="text-center py-5">
            <i class="bi bi-cart-x empty-cart-icon"></i>
            <p class="mt-3 fs-5 text-muted">Keranjang Anda kosong.</p>
            <a href="{{ route('user.menus.index') }}" class="btn btn-primary">
                <i class="bi bi-shop me-2"></i>Lihat Menu
            </a>
        </div>
    @else
        <div class="row">
            <div class="col-md-8">
                <!-- Select All -->
                <div class="d-flex align-items-center mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="selectAll" checked style="width: 20px; height: 20px;">
                    </div>
                    <label for="selectAll" class="ms-2 fw-bold">Pilih Semua</label>
                </div>
                
                @php $total = 0; @endphp
                @foreach($cart as $id => $item)
                    @php 
                        $subtotal = $item['price'] * $item['qty']; 
                        $total += $subtotal;
                        $isAvailableToday = isset($item['is_available_today']) ? $item['is_available_today'] : true;
                        $photoPath = isset($item['photo_path']) ? $item['photo_path'] : null;
                    @endphp
                    <div class="cart-item {{ !$isAvailableToday ? 'unavailable' : '' }}" data-available="{{ $isAvailableToday ? 'true' : 'false' }}">
                        <div class="d-flex align-items-start">
                            <div class="form-check me-3 mt-2">
                                <input class="form-check-input cart-checkbox" type="checkbox" name="selected_items[]" value="{{ $id }}" {{ $isAvailableToday ? 'checked' : 'disabled' }} data-subtotal="{{ $subtotal }}" data-available="{{ $isAvailableToday ? 'true' : 'false' }}" style="width: 20px; height: 20px;">
                            </div>
                            
                            @if($photoPath)
                                <img src="{{ asset('storage/' . $photoPath) }}" alt="{{ $item['name'] }}" class="cart-item-img me-3">
                            @else
                                <div class="bg-light d-flex align-items-center justify-content-center cart-item-img me-3">
                                    <i class="bi bi-image text-muted"></i>
                                </div>
                            @endif
                            
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="mb-1 fw-bold">{{ $item['name'] }}</h6>
                                        @if(!$isAvailableToday)
                                            <span class="badge bg-secondary mb-2">Tidak Tersedia Hari Ini</span>
                                        @endif
                                        @if(!empty($item['variant_name']))
                                            <span class="badge" style="background: var(--light-orange); color: var(--primary-orange);">{{ $item['variant_name'] }}</span>
                                        @endif
                                    </div>
                                    <form action="{{ route('cart.remove', ['menu' => $item['menu_id']]) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                                
                                @if(!empty($item['addons']))
                                    <div class="small text-muted mb-2">
                                        @foreach($item['addons'] as $addon)
                                            <span class="badge bg-light text-dark me-1">+{{ $addon['name'] }} (Rp {{ number_format($addon['price'], 0, ',', '.') }})</span>
                                        @endforeach
                                    </div>
                                @endif
                                
                                @if(!empty($item['notes']))
                                    <div class="small text-info mb-2">
                                        <i class="bi bi-sticky me-1"></i>Catatan: {{ $item['notes'] }}
                                    </div>
                                @endif
                                
                                @if(isset($item['category']) && $item['category'])
                                    <span class="badge bg-light text-dark mb-2">{{ $item['category'] }}</span>
                                @endif
                                
                                <div class="d-flex justify-content-between align-items-center mt-2">
                                    <span class="fw-bold" style="color: var(--primary-orange);">Rp {{ number_format($item['price'], 0, ',', '.') }}</span>
                                    
                                    <form action="{{ route('cart.update', ['menu' => $item['menu_id']]) }}" method="POST" class="d-flex gap-2">
                                        @csrf
                                        <input type="number" name="qty" value="{{ $item['qty'] }}" min="0" class="form-control" style="width: 70px;" {{ !$isAvailableToday ? 'disabled' : '' }}>
                                        <button type="submit" class="btn btn-sm btn-outline-primary" {{ !$isAvailableToday ? 'disabled' : '' }}>Update</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <div class="col-md-4">
                <div class="checkout-card p-3">
                    <h5 class="mb-3"><i class="bi bi-receipt me-2"></i>Checkout</h5>
                    
                    <div class="summary-row">
                        <span class="text-muted">Total Dipilih:</span>
                        <span class="fw-bold" id="selectedTotal">Rp {{ number_format($total, 0, ',', '.') }}</span>
                    </div>
                    <div class="summary-row">
                        <span class="text-muted">Saldo Anda:</span>
                        <span class="fw-bold">Rp {{ number_format(Auth::user()->balance, 0, ',', '.') }}</span>
                    </div>
                    
                    @if($dailyLimitEnabled)
                    <hr>
                    <div class="mb-3" id="limitInfo">
                        <div class="summary-row">
                            <span class="text-muted">Batas Harian:</span>
                            <span class="fw-bold text-danger">Rp {{ number_format($dailyLimit, 0, ',', '.') }}</span>
                        </div>
                        <div class="summary-row">
                            <span class="text-muted">Terpakai Hari Ini:</span>
                            <span class="fw-bold">Rp {{ number_format($todaySpending, 0, ',', '.') }}</span>
                        </div>
                        <div class="summary-row">
                            <span class="text-muted">Sisa Batas:</span>
                            <span class="fw-bold" id="remainingLimitDisplay">Rp {{ number_format($remainingDailyLimit, 0, ',', '.') }}</span>
                        </div>
                        <div class="summary-row">
                            <span class="text-muted">Setelah Checkout:</span>
                            <span class="fw-bold" id="afterCheckoutTotal">Rp {{ number_format($todaySpending + $cartTotal, 0, ',', '.') }}</span>
                        </div>
                    </div>
                    @endif
                    
                    <hr>
                    <form method="POST" action="{{ route('checkout') }}" id="checkoutForm">
                        @csrf
                        <input type="hidden" name="selected_items" id="selectedItemsInput" value="">
                        <div class="mb-3">
                            <label for="pickup_slot" class="form-label fw-bold">Waktu Pengambilan</label>
                            <select class="form-select" id="pickup_slot" name="pickup_slot" required>
                                <option value="">Pilih Waktu</option>
                                <option value="istirahat_1">Istirahat 1</option>
                                <option value="istirahat_2">Istirahat 2</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="classroom" class="form-label fw-bold">Kelas</label>
                            <input type="text" class="form-control" id="classroom" name="classroom" placeholder="Contoh: 9A" required>
                        </div>
                        @if(Auth::user()->balance < $total)
                            <div class="alert alert-warning">
                                <i class="bi bi-exclamation-triangle me-2"></i>Saldo tidak cukup!
                            </div>
                            <a href="{{ route('user.balance.index') }}" class="btn btn-primary w-100">
                                <i class="bi bi-plus-circle me-2"></i>Isi Saldo
                            </a>
                        @else
                            <button type="submit" class="btn btn-primary w-100" id="checkoutBtn">
                                <i class="bi bi-check-circle me-2"></i>Checkout
                            </button>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('selectAll');
    const cartCheckboxes = document.querySelectorAll('.cart-checkbox');
    const selectedTotalEl = document.getElementById('selectedTotal');
    const selectedItemsInput = document.getElementById('selectedItemsInput');
    const checkoutForm = document.getElementById('checkoutForm');
    const checkoutBtn = document.getElementById('checkoutBtn');
    
    @if($dailyLimitEnabled)
    const dailyLimit = {{ $dailyLimit }};
    const todaySpending = {{ $todaySpending }};
    const remainingDailyLimit = {{ $remainingDailyLimit }};
    const afterCheckoutTotal = document.getElementById('afterCheckoutTotal');
    const remainingLimitDisplay = document.getElementById('remainingLimitDisplay');
    @endif
    
    function updateSelectedTotal() {
        let total = 0;
        const selectedIds = [];
        
        cartCheckboxes.forEach(checkbox => {
            if (checkbox.checked && checkbox.dataset.available === 'true') {
                const subtotal = parseInt(checkbox.dataset.subtotal);
                total += subtotal;
                selectedIds.push(checkbox.value);
            }
        });
        
        selectedTotalEl.textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(total);
        selectedItemsInput.value = selectedIds.join(',');
        
        @if($dailyLimitEnabled)
        const newTotal = todaySpending + total;
        const newRemaining = dailyLimit - newTotal;
        
        afterCheckoutTotal.textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(newTotal);
        remainingLimitDisplay.textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(Math.max(0, newRemaining));
        
        if (newTotal > dailyLimit) {
            afterCheckoutTotal.classList.add('text-danger');
            remainingLimitDisplay.classList.remove('text-success');
            remainingLimitDisplay.classList.add('text-danger');
            
            if (checkoutBtn && !checkoutBtn.classList.contains('btn-secondary')) {
                checkoutBtn.classList.remove('btn-primary');
                checkoutBtn.classList.add('btn-secondary');
                checkoutBtn.type = 'button';
                checkoutBtn.setAttribute('data-bs-toggle', 'tooltip');
                checkoutBtn.setAttribute('data-bs-placement', 'top');
                checkoutBtn.title = 'Melebihi batas harian!';
                checkoutBtn.innerHTML = '<i class="bi bi-lock-fill me-2"></i>Checkout Terblokir';
                new bootstrap.Tooltip(checkoutBtn);
            }
        } else {
            afterCheckoutTotal.classList.remove('text-danger');
            remainingLimitDisplay.classList.remove('text-danger');
            remainingLimitDisplay.classList.add('text-success');
            
            if (checkoutBtn && checkoutBtn.classList.contains('btn-secondary')) {
                checkoutBtn.classList.remove('btn-secondary');
                checkoutBtn.classList.add('btn-primary');
                checkoutBtn.type = 'submit';
                checkoutBtn.removeAttribute('data-bs-toggle');
                checkoutBtn.removeAttribute('data-bs-placement');
                checkoutBtn.removeAttribute('title');
                checkoutBtn.innerHTML = '<i class="bi bi-check-circle me-2"></i>Checkout';
            }
        }
        @endif
    }
    
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            cartCheckboxes.forEach(checkbox => {
                if (checkbox.dataset.available === 'true') {
                    checkbox.checked = this.checked;
                }
            });
            updateSelectedTotal();
        });
    }
    
    cartCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const availableCheckboxes = Array.from(cartCheckboxes).filter(cb => cb.dataset.available === 'true');
            const allChecked = availableCheckboxes.every(cb => cb.checked);
            const someChecked = availableCheckboxes.some(cb => cb.checked);
            
            if (selectAllCheckbox) {
                selectAllCheckbox.checked = allChecked;
                selectAllCheckbox.indeterminate = someChecked && !allChecked;
            }
            
            updateSelectedTotal();
        });
    });
    
    updateSelectedTotal();
});
</script>
@endsection
