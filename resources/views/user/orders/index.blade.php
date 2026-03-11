@extends('layouts.master')

@section('title', 'Riwayat Pesanan - E-Canteen')

@section('styles')
<style>
    .orders-header {
        background-color: var(--primary-orange);
        padding: 1.5rem 0;
        margin-bottom: 1.5rem;
    }
    
    .order-card {
        background: white;
        border: 1px solid #E5E7EB;
        border-radius: 12px;
        margin-bottom: 1rem;
        overflow: hidden;
    }
    
    .order-header {
        background: #F9FAFB;
        padding: 1rem;
        border-bottom: 1px solid #E5E7EB;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .order-id {
        font-weight: 700;
        font-size: 1rem;
    }
    
    .order-date {
        color: #6B7280;
        font-size: 0.875rem;
    }
    
    .order-body {
        padding: 1rem;
    }
    
    .order-item {
        display: flex;
        align-items: center;
        padding: 0.75rem 0;
        border-bottom: 1px solid #E5E7EB;
    }
    
    .order-item:last-child {
        border-bottom: none;
    }
    
    .item-qty {
        background: var(--light-orange);
        color: var(--primary-orange);
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
        font-weight: 600;
        font-size: 0.875rem;
        min-width: 40px;
        text-align: center;
    }
    
    .item-name {
        font-weight: 500;
    }
    
    .item-price {
        color: var(--primary-orange);
        font-weight: 600;
    }
    
    .order-footer {
        background: #F9FAFB;
        padding: 1rem;
        border-top: 1px solid #E5E7EB;
    }
    
    .status-badge {
        padding: 0.35rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    
    .status-pending { background: #FEE2E2; color: #991B1B; }
    .status-preparing { background: var(--light-orange); color: var(--primary-orange); }
    .status-ready { background: #D1FAE5; color: #065F46; }
    .status-completed { background: #D1FAE5; color: #065F46; }
    .status-cancelled { background: #FEE2E2; color: #991B1B; }
    
    .empty-orders-icon {
        font-size: 5rem;
        color: #E5E7EB;
    }
</style>
@endsection

@section('content')
<div class="orders-header">
    <div class="container">
        <h2 class="mb-0 text-white"><i class="bi bi-receipt me-2"></i>Riwayat Pesanan</h2>
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
    
    @if($orders->isEmpty())
        <div class="text-center py-5">
            <i class="bi bi-receipt empty-orders-icon"></i>
            <p class="mt-3 fs-5 text-muted">Belum ada pesanan.</p>
            <a href="{{ route('home') }}" class="btn btn-primary">
                <i class="bi bi-shop me-2"></i>Pesan Sekarang
            </a>
        </div>
    @else
        @foreach($orders as $order)
            <div class="order-card" id="status-{{ $order->id }}" 
                 data-order-id="{{ $order->id }}"
                 data-status="{{ $order->status }}"
                 data-cancel-request="{{ $order->cancel_request ?? 'none' }}"
                 data-is-completed="{{ $order->is_completed ? '1' : '0' }}"
                 data-is-auto-confirmed="{{ $order->is_auto_confirmed ? '1' : '0' }}">
                <div class="order-header">
                    <div>
                        <span class="order-id">Pesanan #{{ $order->id }}</span>
                        <span class="order-date ms-2">{{ $order->created_at->format('d M Y H:i') }}</span>
                    </div>
                    <div>
                        @if($order->cancel_request === 'accepted')
                            <span class="status-badge status-cancelled">Dibatalkan</span>
                        @elseif($order->is_completed || $order->is_auto_confirmed)
                            <span class="status-badge status-completed">Selesai</span>
                        @elseif($order->status === 'pending')
                            <span class="status-badge status-pending">Pending</span>
                        @elseif($order->status === 'preparing')
                            <span class="status-badge status-preparing">Sedang Disiapkan</span>
                        @else
                            <span class="status-badge status-ready">Siap Diambil</span>
                        @endif
                    </div>
                </div>
                
                <div class="order-body">
                    @if($order->cancel_request === 'accepted')
                        <div class="alert alert-danger mb-3">
                            <i class="bi bi-x-circle me-2"></i>Pesanan ini telah dibatalkan.
                        </div>
                    @endif
                    
                    @php
                        $orderNotes = [];
                        foreach($order->orderItems as $item) {
                            if(!empty($item->notes)) {
                                $orderNotes[] = ['menu' => $item->menu->name, 'notes' => $item->notes];
                            }
                        }
                    @endphp
                    @if(count($orderNotes) > 0)
                        <div class="alert alert-info mb-3">
                            <div class="d-flex align-items-start">
                                <i class="bi bi-sticky-fill me-2 mt-1"></i>
                                <div>
                                    <strong>Catatan Pesanan:</strong>
                                    <ul class="mb-0 mt-2">
                                        @foreach($orderNotes as $note)
                                            <li><strong>{{ $note['menu'] }}:</strong> {{ $note['notes'] }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif
                    
                    @if($order->cancel_request !== 'accepted')
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Waktu Pengambilan:</strong> {{ $order->pickup_slot === 'istirahat_1' ? 'Istirahat 1' : 'Istirahat 2' }}</p>
                                <p class="mb-0"><strong>Kelas:</strong> {{ $order->classroom }}</p>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <!-- Payment Status -->
                        <div class="mb-3">
                            <strong>Status Pembayaran:</strong>
                            @if($order->is_completed || $order->is_auto_confirmed)
                                <span class="badge bg-success ms-2">Selesai (Dana ke Seller)</span>
                            @elseif($order->is_confirmed_by_seller && !$order->is_confirmed_by_user)
                                <span class="badge bg-warning text-dark ms-2">Menunggu Konfirmasi Anda</span>
                            @elseif($order->is_paid)
                                <span class="badge bg-info ms-2">Lunas (Dana di Escrow)</span>
                            @else
                                <span class="badge bg-secondary ms-2">Belum Bayar</span>
                            @endif
                        </div>
                        
                        <!-- Confirmation Status -->
                        <div class="mb-3 p-3 bg-light rounded">
                            <strong>Status Konfirmasi:</strong>
                            <div class="mt-2">
                                <div class="{{ $order->is_confirmed_by_seller ? 'text-success' : 'text-muted' }}">
                                    <i class="bi {{ $order->is_confirmed_by_seller ? 'bi-check-circle-fill' : 'bi-circle' }}"></i>
                                    Penjual sudah serah terimana
                                </div>
                                <div class="{{ $order->is_confirmed_by_user ? 'text-success' : 'text-muted' }}">
                                    <i class="bi {{ $order->is_confirmed_by_user ? 'bi-check-circle-fill' : 'bi-circle' }}"></i>
                                    Saya sudah terima pesanan
                                </div>
                        </div>
                        
                        @if($order->status === 'ready' && $order->is_confirmed_by_seller && !$order->is_confirmed_by_user && !$order->is_completed && !$order->is_auto_confirmed)
                            <div class="alert alert-warning mb-3">
                                <i class="bi bi-clock me-2"></i> 
                                <strong>Konfirmasi dalam:</strong> 
                                <span id="timer-{{ $order->id }}" data-minutes="{{ $order->getAutoConfirmRemainingMinutes() }}"></span>
                            </div>
                            
                            <form action="{{ route('user.orders.confirmReceipt', $order) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-success">
                                    <i class="bi bi-check-circle me-2"></i>Konfirmasi Sudah Terima Pesanan
                                </button>
                            </form>
                        @elseif($order->is_confirmed_by_user)
                            <div class="alert alert-success mb-0">
                                <i class="bi bi-check-circle-fill me-2"></i>Anda telah mengkonfirmasi penerimaan pesanan ini.
                            </div>
                        @elseif($order->status === 'ready' && !$order->is_confirmed_by_seller)
                            <div class="text-muted">
                                <i class="bi bi-hourglass-split me-2"></i>Menunggu konfirmasi dari seller...
                            </div>
                        @elseif($order->status === 'pending')
                            <div class="text-muted">
                                <i class="bi bi-hourglass-split me-2"></i>Menunggu konfirmasi dari seller...
                            </div>
                        @elseif($order->status === 'preparing')
                            <div class="text-muted">
                                <i class="bi bi-hourglass-split me-2"></i>Pesanan sedang disiapkan oleh seller...
                            </div>
                        @elseif($order->is_auto_confirmed)
                            <div class="alert alert-info mb-0">
                                <i class="bi bi-clock-history me-2"></i>Pesanan diselesaikan secara otomatis oleh sistem.
                            </div>
                        @elseif($order->is_completed)
                            <div class="alert alert-info mb-0">
                                <i class="bi bi-check-circle-fill me-2"></i>Pesanan selesai.
                            </div>
                        @endif
                        
                        @if($order->canUserRequestCancel())
                            <form action="{{ route('user.orders.requestCancel', $order) }}" method="POST" class="d-inline ms-2">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Yakin ingin membatalkan pesanan?')">
<i class="bi bi-x-circle me-1"></i>Batalkan</button>
                            </form>
                        @elseif($order->cancel_request === 'pending')
                            <div class="alert alert-warning mb-0 mt-2">
                                <i class="bi bi-hourglass-split me-2"></i>Menunggu konfirmasi pembatalan...
                            </div>
                        @elseif($order->cancel_request === 'rejected')
                            <div class="alert alert-danger mb-0 mt-2">
                                <i class="bi bi-x-circle me-2"></i>Permintaan pembatalan ditolak.
                            </div>
                        @endif
                        
                        <hr>
                        <h6 class="mb-3">Detail Items:</h6>
                        @foreach($order->orderItems as $item)
                            <div class="order-item">
                                <span class="item-qty me-3">x{{ $item->qty }}</span>
                                <div class="flex-grow-1">
                                    <div class="item-name">
                                        {{ $item->menu->name }}
                                        @if($item->variant_name)
                                            <span class="badge bg-light text-dark ms-1">{{ $item->variant_name }}</span>
                                        @endif
                                    </div>
                                    @if(!empty($item->addons))
                                        <div class="small text-muted">
                                            @foreach($item->addons as $addon)
                                                <span class="badge bg-light text-dark me-1">+{{ $addon['name'] }}</span>
                                            @endforeach
                                        </div>
                                    @endif
                                    @if(!empty($item->notes))
                                        <div class="small text-info">
                                            <i class="bi bi-sticky me-1"></i>{{ $item->notes }}
                                        </div>
                                    @endif
                                </div>
                                <span class="item-price">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                            </div>
                        @endforeach
                        
                        <!-- Rating Section -->
                        @if(($order->is_completed || $order->is_auto_confirmed) && $order->cancel_request !== 'accepted')
                            <hr>
                            <h6 class="mt-3"><i class="bi bi-star me-2"></i>Rating & Komentar</h6>
                            
                            @php
                                $hasRated = false;
                                if($order->ratings) {
                                    foreach($order->ratings as $rating) {
                                        if($rating->user_id === auth()->id()) {
                                            $hasRated = true;
                                        }
                                    }
                                }
                                $hasReported = \App\Models\Report::where('order_id', $order->id)->where('user_id', auth()->id())->exists();
                            @endphp
                            
                            @if($hasRated && $order->ratings)
                                <div class="mb-3">
                                    @foreach($order->ratings as $rating)
                                        @if($rating->user_id === auth()->id())
                                            <div class="card mb-2">
                                                <div class="card-body py-2">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div>
                                                            <strong>{{ $rating->menu->name }}</strong>
                                                            <div class="text-warning">
                                                                @for($i = 1; $i <= 5; $i++)
                                                                    <i class="bi {{ $i <= $rating->rating ? 'bi-star-fill' : 'bi-star' }}"></i>
                                                                @endfor
                                                            </div>
                                                            @if($rating->comment)
                                                                <p class="mb-0 mt-1 text-muted">{{ $rating->comment }}</p>
                                                            @endif
                                                        </div>
                                                        <span class="text-success"><i class="bi bi-check-circle-fill"></i> Sudah dinilai</span>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            @else
                                <button class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#ratingModal{{ $order->id }}">
                                    <i class="bi bi-star me-1"></i>Beri Rating
                                </button>
                                
                                <div class="modal fade" id="ratingModal{{ $order->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Beri Rating #{{ $order->id }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form action="{{ route('user.orders.rating', $order) }}" method="POST">
                                                @csrf
                                                <div class="modal-body">
                                                    @foreach($order->orderItems as $item)
                                                        <div class="mb-3 border-bottom pb-3">
                                                            <label class="form-label fw-bold">{{ $item->menu->name }}</label>
                                                            <input type="hidden" name="items[{{ $loop->index }}][menu_id]" value="{{ $item->menu_id }}">
                                                            
                                                            <div class="mb-2">
                                                                @for($i = 1; $i <= 5; $i++)
                                                                    <div class="form-check form-check-inline">
                                                                        <input class="form-check-input" type="radio" name="items[{{ $loop->index }}][rating]" id="rating{{ $order->id }}_{{ $item->menu_id }}_{{ $i }}" value="{{ $i }}" {{ $i == 5 ? 'checked' : '' }}>
                                                                        <label class="form-check-label text-warning" for="rating{{ $order->id }}_{{ $item->menu_id }}_{{ $i }}">
                                                                            @for($s = 1; $s <= $i; $s++)<i class="bi bi-star-fill"></i>@endfor
                                                                        </label>
                                                                    </div>
                                                                @endfor
                                                            </div>
                                                            
                                                            <input type="text" name="items[{{ $loop->index }}][comment]" class="form-control" placeholder="Komentar (opsional)">
                                                        </div>
                                                    @endforeach
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-primary">Kirim Rating</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            
                            @if(!$hasReported)
                                <a href="{{ route('user.reports.create', $order) }}" class="btn btn-sm btn-outline-danger ms-2">
                                    <i class="bi bi-flag me-1"></i>Laporan
                                </a>
                            @else
                                <span class="badge bg-warning text-dark ms-2">
                                    <i class="bi bi-check-circle me-1"></i>Sudah dilaporakan
                                </span>
                            @endif
                            
                            @if($order->report && $order->report->status !== 'pending')
                                <hr>
                                <div class="card mt-3">
                                    <div class="card-header bg-{{ $order->report->status === 'resolved' ? 'success' : ($order->report->status === 'rejected' ? 'danger' : 'info') }} text-white">
                                        <i class="bi {{ $order->report->status === 'resolved' ? 'bi-check-circle-fill' : ($order->report->status === 'rejected' ? 'bi-x-circle-fill' : 'bi-info-circle-fill') }} me-2"></i>
                                        {{ $order->report->status === 'resolved' ? 'Laporan Selesai' : ($order->report->status === 'rejected' ? 'Laporan Ditolak' : 'Laporan Ditinjau') }}
                                    </div>
                                    <div class="card-body">
                                        <p class="mb-0">{{ $order->report->resolution_notes }}</p>
                                    </div>
                                </div>
                            @elseif($order->report && $order->report->status === 'pending')
                                <hr>
                                <div class="alert alert-warning mt-3">
                                    <i class="bi bi-hourglass-split me-2"></i>Laporan Anda masih dalam proses peninjauan.
                                </div>
                            @endif
                        @endif
                    @endif
                </div>
                
                <div class="order-footer">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-bold">Total:</span>
                        <span class="price-tag" style="font-size: 1.25rem;">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        @endforeach
        
        <div class="d-flex justify-content-center mt-4">
            {{ $orders->links('pagination.custom') }}
        </div>
    @endif
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('[id^="timer-"]').forEach(function(timerEl) {
        let minutes = parseInt(timerEl.dataset.minutes);
        
        function updateTimer() {
            if (minutes <= 0) {
                timerEl.textContent = '00:00 - Auto konfirmasi...';
                setTimeout(function() { location.reload(); }, 2000);
                return;
            }
            
            const hours = Math.floor(minutes / 60);
            const mins = minutes % 60;
            timerEl.textContent = (hours > 0 ? hours + ' jam ' : '') + mins + ' menit';
            minutes--;
        }
        
        updateTimer();
        setInterval(updateTimer, 60000);
    });
    
    const orderIds = [];
    document.querySelectorAll('[data-order-id]').forEach(function(el) {
        const orderId = el.dataset.orderId;
        if (orderId && !orderIds.includes(orderId)) {
            orderIds.push(orderId);
        }
    });

    if (orderIds.length > 0) {
        let previousStatuses = {};
        document.querySelectorAll('[data-order-id]').forEach(function(el) {
            const id = el.dataset.orderId;
            const status = el.dataset.status || '';
            const cancel = el.dataset.cancelRequest || '';
            const comp = el.dataset.isCompleted || '0';
            const auto = el.dataset.isAutoConfirmed || '0';
            previousStatuses[id] = status + '|' + cancel + '|' + comp + '|' + auto;
        });

        function checkUserStatuses() {
            fetch('/api/orders/status?ids=' + orderIds.join(','))
                .then(response => response.json())
                .then(orders => {
                    let shouldReload = false;
                    orders.forEach(order => {
                        const currentCancel = order.cancel_request || '';
                        const current = order.status + '|' + currentCancel + '|' + (order.is_completed ? '1' : '0') + '|' + (order.is_auto_confirmed ? '1' : '0');
                        const prev = previousStatuses[order.id];
                        
                        if (prev && prev !== current) {
                            shouldReload = true;
                        }
                        previousStatuses[order.id] = current;
                    });
                    if (shouldReload) { location.reload(); }
                })
                .catch(error => console.log('Error polling:', error));
        }

        checkUserStatuses();
        setInterval(checkUserStatuses, 3000);
    }
});
</script>
@endpush
@endsection
