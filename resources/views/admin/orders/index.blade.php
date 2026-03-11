@extends('layouts.master')

@section('title', 'Antrean Pesanan - E-Canteen')

@section('styles')
<style>
.table-highlight {
    background-color: #ffc107 !important;
    box-shadow: 0 0 10px rgba(255, 193, 7, 0.8);
    transition: all 0.3s ease;
}
</style>
@endsection

@section('content')
<div class="container py-4">
    <h2><i class="bi bi-list-check"></i> Antrean Pesanan</h2>
    <div class="mb-2">
        <span class="text-muted"><i class="bi bi-arrow-repeat"></i> Halaman akan refresh otomatis setiap 3 detik melalui polling HTTP untuk melihat pesanan terbaru.</span>
        <button class="btn btn-sm btn-outline-secondary ms-2" onclick="location.reload()">
            <i class="bi bi-arrow-clockwise"></i> Refresh
        </button>
    </div>
    
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

    <!-- Istirahat 1 Table -->
    <div class="card mb-4">
        <div class="card-header" style="background-color: #fd7e14; color: white;">
            <h5 class="mb-0">
                <i class="bi bi-clock"></i> Istirahat 1
                <span class="badge bg-white text-dark ms-2">{{ $istirahat1Orders->total() }}</span>
            </h5>
        </div>
        <div class="card-body">
            @if($istirahat1Orders->isEmpty())
                <div class="text-center py-4">
                    <i class="bi bi-emoji-frown display-6 text-muted"></i>
                    <p class="mt-2">Belum ada pesanan.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Pemesan</th>
                                <th>Kelas</th>
                                <th>Items 
                                    @php
                                        $hasNotesInPage1 = false;
                                        foreach($istirahat1Orders as $o) {
                                            foreach($o->orderItems as $item) {
                                                if(!empty($item->notes)) { $hasNotesInPage1 = true; break; }
                                            }
                                        }
                                    @endphp
                                    @if($hasNotesInPage1)
                                        <span class="badge bg-info ms-1"><i class="bi bi-sticky"></i></span>
                                    @endif
                                </th>
                                <th>Total</th>
                                <th>Pembayaran</th>
                                <th>Konfirmasi</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="istirahat1-body">
                            @foreach($istirahat1Orders as $order)
                                <tr class="{{ $order->cancel_request === 'pending' ? 'table-warning' : '' }}" 
                                    data-order-id="{{ $order->id }}"
                                    data-status="{{ $order->status }}"
                                    data-cancel-request="{{ $order->cancel_request ?? 'none' }}"
                                    data-is-completed="{{ $order->is_completed ? '1' : '0' }}"
                                    data-is-auto-confirmed="{{ $order->is_auto_confirmed ? '1' : '0' }}"
                                    data-is-confirmed-by-seller="{{ $order->is_confirmed_by_seller ? '1' : '0' }}"
                                    data-is-confirmed-by-user="{{ $order->is_confirmed_by_user ? '1' : '0' }}">
                                    <td>#{{ $order->id }}</td>
                                    <td>{{ $order->user->name }}</td>
                                    <td>{{ $order->classroom }}</td>
                                    <td>
                                        @foreach($order->orderItems as $item)
                                            <div>
                                                {{ $item->menu->name }}
                                                @if($item->variant_name)
                                                    <span class="badge bg-warning text-dark">{{ $item->variant_name }}</span>
                                                @endif
                                                @if($item->menu->category)
                                                    <span class="badge bg-info">{{ $item->menu->category }}</span>
                                                @endif
                                                @if(!empty($item->addons))
                                                    <div class="small text-muted">
                                                        @foreach($item->addons as $addon)
                                                            <span class="badge bg-secondary">+{{ $addon['name'] }} (Rp {{ number_format($addon['price'], 0, ',', '.') }})</span>
                                                        @endforeach
                                                    </div>
                                                @endif
                                                @if(!empty($item->notes))
                                                    <div class="small text-info mt-1">
                                                        <i class="bi bi-sticky"></i> Catatan: {{ $item->notes }}
                                                    </div>
                                                @endif
                                                x{{ $item->qty }}
                                            </div>
                                        @endforeach
                                    </td>
                                    <td>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                                    <td>
                                        @if($order->cancel_request === 'accepted')
                                            <span class="badge bg-danger">Dibatalkan</span>
                                        @elseif($order->is_completed || $order->is_auto_confirmed)
                                            <span class="badge bg-success">Selesai</span>
                                        @elseif($order->is_confirmed_by_seller && !$order->is_confirmed_by_user)
                                            <span class="badge bg-warning text-dark">Menunggu Pembeli</span>
                                        @elseif($order->is_paid)
                                            <span class="badge bg-info">Lunas (Escrow)</span>
                                        @else
                                            <span class="badge bg-secondary">Belum Bayar</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="mb-1">
                                            <span class="{{ $order->is_confirmed_by_seller ? 'text-success' : 'text-muted' }}">
                                                <i class="bi {{ $order->is_confirmed_by_seller ? 'bi-check-circle-fill' : 'bi-circle' }}"></i>
                                                Saya (Seller)
                                            </span>
                                        </div>
                                        <div>
                                            <span class="{{ $order->is_confirmed_by_user ? 'text-success' : 'text-muted' }}">
                                                <i class="bi {{ $order->is_confirmed_by_user ? 'bi-check-circle-fill' : 'bi-circle' }}"></i>
                                                Pembeli
                                            </span>
                                        </div>
                                    </td>
                                    <td>
                                        @if($order->cancel_request === 'accepted')
                                            <span class="badge bg-danger">Dibatalkan</span>
                                        @elseif($order->is_completed || $order->is_auto_confirmed)
                                            <span class="badge bg-success">Selesai</span>
                                        @elseif($order->status === 'pending')
                                            <span class="badge bg-secondary">Pending</span>
                                        @elseif($order->status === 'preparing')
                                            <span class="badge" style="background-color: #fd7e14;">Sedang Disiapkan</span>
                                        @else
                                            <span class="badge bg-success">Siap Diambil</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($order->cancel_request === 'pending')
                                            <div class="alert alert-warning p-2 mb-2">
                                                <i class="bi bi-exclamation-triangle"></i> Permintaan pembatalan!
                                            </div>
                                            @if($order->status === 'preparing')
                                            <form action="{{ route('admin.orders.respondCancel', $order) }}" method="POST" class="d-inline">
                                                @csrf
                                                <input type="hidden" name="action" value="accept">
                                                <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Terima pembatalan dan refund?')">
                                                    <i class="bi bi-check-circle"></i> Terima
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.orders.respondCancel', $order) }}" method="POST" class="d-inline">
                                                @csrf
                                                <input type="hidden" name="action" value="reject">
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Tolak pembatalan dan teruskan pesanan?')">
                                                    <i class="bi bi-x-circle"></i> Tolak
                                                </button>
                                            </form>
                                            @endif
                                        @elseif($order->cancel_request === 'accepted')
                                            <span class="text-danger"><i class="bi bi-x-circle"></i> Dibatalkan</span>
                                        @elseif($order->is_completed || $order->is_auto_confirmed)
                                            <span class="text-success"><i class="bi bi-check-circle-fill"></i> Selesai</span>
                                        @else
                                            @if($order->status === 'pending')
                                                <form action="{{ route('admin.orders.accept', $order) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-primary">
                                                        <i class="bi bi-check-circle"></i> Terima Pesanan
                                                    </button>
                                                </form>
                                            @elseif($order->status === 'preparing')
                                                <form action="{{ route('admin.orders.updateStatus', $order) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="status" value="ready">
                                                    <button type="submit" class="btn btn-sm btn-success">
                                                        <i class="bi bi-check-circle"></i> Tandai Siap
                                                    </button>
                                                </form>
                                            @else
                                                @if(!$order->is_confirmed_by_seller)
                                                    <form action="{{ route('admin.orders.confirmHandover', $order) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-primary">
                                                            <i class="bi bi-hand-thumbs-up"></i> Konfirmasi Serah
                                                        </button>
                                                    </form>
                                                @elseif($order->is_confirmed_by_seller && !$order->is_confirmed_by_user)
                                                    <span class="text-muted small">
                                                        <i class="bi bi-hourglass-split"></i> Menunggu konfirmasi pembeli...
                                                    </span>
                                                @elseif($order->is_confirmed_by_user && $order->is_confirmed_by_seller)
                                                    <span class="text-success">
                                                        <i class="bi bi-check-circle-fill"></i> Dana sudah ditransfer
                                                    </span>
                                                @endif
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <!-- Pagination for Istirahat 1 -->
                <div class="d-flex justify-content-center mt-3">
                    @if($istirahat1Orders->hasPages())
                        <nav>
                            <ul class="pagination mb-0">
                                @if($istirahat1Orders->onFirstPage())
                                    <li class="page-item disabled"><span class="page-link">Previous</span></li>
                                @else
                                    <li class="page-item"><a class="page-link" href="{{ $istirahat1Orders->previousPageUrl() }}">Previous</a></li>
                                @endif

                                @foreach($istirahat1Orders->getUrlRange(1, $istirahat1Orders->lastPage()) as $page => $url)
                                    @if($page == $istirahat1Orders->currentPage())
                                        <li class="page-item active"><span class="page-link">{{ $page }}</span></li>
                                    @else
                                        <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                                    @endif
                                @endforeach

                                @if($istirahat1Orders->hasMorePages())
                                    <li class="page-item"><a class="page-link" href="{{ $istirahat1Orders->nextPageUrl() }}">Next</a></li>
                                @else
                                    <li class="page-item disabled"><span class="page-link">Next</span></li>
                                @endif
                            </ul>
                        </nav>
                    @endif
                </div>
            @endif
        </div>
    </div>

    <!-- Istirahat 2 Table -->
    <div class="card mb-4">
        <div class="card-header" style="background-color: #fd7e14; color: white;">
            <h5 class="mb-0">
                <i class="bi bi-clock"></i> Istirahat 2
                <span class="badge bg-white text-dark ms-2">{{ $istirahat2Orders->total() }}</span>
            </h5>
        </div>
        <div class="card-body">
            @if($istirahat2Orders->isEmpty())
                <div class="text-center py-4">
                    <i class="bi bi-emoji-frown display-6 text-muted"></i>
                    <p class="mt-2">Belum ada pesanan.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Pemesan</th>
                                <th>Kelas</th>
                                <th>Items
                                    @php
                                        $hasNotesInPage2 = false;
                                        foreach($istirahat2Orders as $o) {
                                            foreach($o->orderItems as $item) {
                                                if(!empty($item->notes)) { $hasNotesInPage2 = true; break; }
                                            }
                                        }
                                    @endphp
                                    @if($hasNotesInPage2)
                                        <span class="badge bg-info ms-1"><i class="bi bi-sticky"></i></span>
                                    @endif
                                </th>
                                <th>Total</th>
                                <th>Pembayaran</th>
                                <th>Konfirmasi</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="istirahat2-body">
                            @foreach($istirahat2Orders as $order)
                                <tr class="{{ $order->cancel_request === 'pending' ? 'table-warning' : '' }}" 
                                    data-order-id="{{ $order->id }}"
                                    data-status="{{ $order->status }}"
                                    data-cancel-request="{{ $order->cancel_request ?? 'none' }}"
                                    data-is-completed="{{ $order->is_completed ? '1' : '0' }}"
                                    data-is-auto-confirmed="{{ $order->is_auto_confirmed ? '1' : '0' }}"
                                    data-is-confirmed-by-seller="{{ $order->is_confirmed_by_seller ? '1' : '0' }}"
                                    data-is-confirmed-by-user="{{ $order->is_confirmed_by_user ? '1' : '0' }}">
                                    <td>#{{ $order->id }}</td>
                                    <td>{{ $order->user->name }}</td>
                                    <td>{{ $order->classroom }}</td>
                                    <td>
                                        @foreach($order->orderItems as $item)
                                            <div>
                                                {{ $item->menu->name }}
                                                @if($item->variant_name)
                                                    <span class="badge bg-warning text-dark">{{ $item->variant_name }}</span>
                                                @endif
                                                @if(!empty($item->addons))
                                                    <div class="small text-muted">
                                                        @foreach($item->addons as $addon)
                                                            <span class="badge bg-secondary">+{{ $addon['name'] }} (Rp {{ number_format($addon['price'], 0, ',', '.') }})</span>
                                                        @endforeach
                                                    </div>
                                                @endif
                                                @if(!empty($item->notes))
                                                    <div class="small text-info mt-1">
                                                        <i class="bi bi-sticky"></i> Catatan: {{ $item->notes }}
                                                    </div>
                                                @endif
                                                x{{ $item->qty }}
                                            </div>
                                        @endforeach
                                    </td>
                                    <td>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                                    <td>
                                        @if($order->cancel_request === 'accepted')
                                        @elseif($order->is_completed || $order->is_auto_confirmed)
                                            <span class="badge bg-success">Selesai</span>
                                        @elseif($order->is_confirmed_by_seller && !$order->is_confirmed_by_user)
                                            <span class="badge bg-warning text-dark">Menunggu Pembeli</span>
                                        @elseif($order->is_paid)
                                            <span class="badge bg-info">Lunas (Escrow)</span>
                                        @else
                                            <span class="badge bg-secondary">Belum Bayar</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="mb-1">
                                            <span class="{{ $order->is_confirmed_by_seller ? 'text-success' : 'text-muted' }}">
                                                <i class="bi {{ $order->is_confirmed_by_seller ? 'bi-check-circle-fill' : 'bi-circle' }}"></i>
                                                Saya (Seller)
                                            </span>
                                        </div>
                                        <div>
                                            <span class="{{ $order->is_confirmed_by_user ? 'text-success' : 'text-muted' }}">
                                                <i class="bi {{ $order->is_confirmed_by_user ? 'bi-check-circle-fill' : 'bi-circle' }}"></i>
                                                Pembeli
                                            </span>
                                        </div>
                                    </td>
                                    <td>
                                        @if($order->cancel_request === 'accepted')
                                            <span class="badge bg-danger">Dibatalkan</span>
                                        @elseif($order->is_completed || $order->is_auto_confirmed)
                                            <span class="badge bg-success">Selesai</span>
                                        @elseif($order->status === 'pending')
                                            <span class="badge bg-secondary">Pending</span>
                                        @elseif($order->status === 'preparing')
                                            <span class="badge" style="background-color: #fd7e14;">Sedang Disiapkan</span>
                                        @else
                                            <span class="badge bg-success">Siap Diambil</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($order->cancel_request === 'pending')
                                            <div class="alert alert-warning p-2 mb-2">
                                                <i class="bi bi-exclamation-triangle"></i> Permintaan pembatalan!
                                            </div>
                                            @if($order->status === 'preparing')
                                            <form action="{{ route('admin.orders.respondCancel', $order) }}" method="POST" class="d-inline">
                                                @csrf
                                                <input type="hidden" name="action" value="accept">
                                                <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Terima pembatalan dan refund?')">
                                                    <i class="bi bi-check-circle"></i> Terima
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.orders.respondCancel', $order) }}" method="POST" class="d-inline">
                                                @csrf
                                                <input type="hidden" name="action" value="reject">
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Tolak pembatalan dan teruskan pesanan?')">
                                                    <i class="bi bi-x-circle"></i> Tolak
                                                </button>
                                            </form>
                                            @endif
                                        @elseif($order->cancel_request === 'accepted')
                                            <span class="text-danger"><i class="bi bi-x-circle"></i> Dibatalkan</span>
                                        @elseif($order->is_completed || $order->is_auto_confirmed)
                                            <span class="text-success"><i class="bi bi-check-circle-fill"></i> Selesai</span>
                                        @else
                                            @if($order->status === 'pending')
                                                <form action="{{ route('admin.orders.accept', $order) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-primary">
                                                        <i class="bi bi-check-circle"></i> Terima Pesanan
                                                    </button>
                                                </form>
                                            @elseif($order->status === 'preparing')
                                                <form action="{{ route('admin.orders.updateStatus', $order) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="status" value="ready">
                                                    <button type="submit" class="btn btn-sm btn-success">
                                                        <i class="bi bi-check-circle"></i> Tandai Siap
                                                    </button>
                                                </form>
                                            @else
                                                @if(!$order->is_confirmed_by_seller)
                                                    <form action="{{ route('admin.orders.confirmHandover', $order) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-primary">
                                                            <i class="bi bi-hand-thumbs-up"></i> Konfirmasi Serah
                                                        </button>
                                                    </form>
                                                @elseif($order->is_confirmed_by_seller && !$order->is_confirmed_by_user)
                                                    <span class="text-muted small">
                                                        <i class="bi bi-hourglass-split"></i> Menunggu konfirmasi pembeli...
                                                    </span>
                                                @elseif($order->is_confirmed_by_user && $order->is_confirmed_by_seller)
                                                    <span class="text-success">
                                                        <i class="bi bi-check-circle-fill"></i> Dana sudah ditransfer
                                                    </span>
                                                @endif
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <!-- Pagination for Istirahat 2 -->
                <div class="d-flex justify-content-center mt-3">
                    @if($istirahat2Orders->hasPages())
                        <nav>
                            <ul class="pagination mb-0">
                                @if($istirahat2Orders->onFirstPage())
                                    <li class="page-item disabled"><span class="page-link">Previous</span></li>
                                @else
                                    <li class="page-item"><a class="page-link" href="{{ $istirahat2Orders->previousPageUrl() }}">Previous</a></li>
                                @endif

                                @foreach($istirahat2Orders->getUrlRange(1, $istirahat2Orders->lastPage()) as $page => $url)
                                    @if($page == $istirahat2Orders->currentPage())
                                        <li class="page-item active"><span class="page-link">{{ $page }}</span></li>
                                    @else
                                        <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                                    @endif
                                @endforeach

                                @if($istirahat2Orders->hasMorePages())
                                    <li class="page-item"><a class="page-link" href="{{ $istirahat2Orders->nextPageUrl() }}">Next</a></li>
                                @else
                                    <li class="page-item disabled"><span class="page-link">Next</span></li>
                                @endif
                            </ul>
                        </nav>
                    @endif
                </div>
            @endif
        </div>
    </div>
    </div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Get highlight parameter from URL
    const urlParams = new URLSearchParams(window.location.search);
    const highlightId = urlParams.get('highlight');
    
    if (highlightId) {
        // Find the row with the matching order ID
        const rows = document.querySelectorAll('tbody tr');
        
        rows.forEach(row => {
            // Check if this row contains the highlighted order ID
            const firstCell = row.querySelector('td:first-child');
            if (firstCell && firstCell.textContent.includes('#' + highlightId)) {
                // Add highlight class
                row.classList.add('table-highlight');
                
                // Remove highlight after 5 seconds
                setTimeout(() => {
                    row.classList.remove('table-highlight');
                }, 5000);
                
                // Scroll to the row
                row.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        });
    }
    
    // HTTP polling every 3 seconds to detect changes
    const orderIds = [];
    document.querySelectorAll('tbody tr[data-order-id]').forEach(function(el) {
        const orderId = el.dataset.orderId;
        if (orderId && !orderIds.includes(orderId)) {
            orderIds.push(orderId);
        }
    });

    if (orderIds.length > 0) {
        let previousStatuses = {};
        document.querySelectorAll('tbody tr[data-order-id]').forEach(function(el) {
            const id = el.dataset.orderId;
            const status = el.dataset.status || '';
            const cancel = el.dataset.cancelRequest || 'none';
            const comp = el.dataset.isCompleted || '0';
            const auto = el.dataset.isAutoConfirmed || '0';
            const seller = el.dataset.isConfirmedBySeller || '0';
            const userc = el.dataset.isConfirmedByUser || '0';
            previousStatuses[id] = status + '_' + cancel + '_' + comp + '_' + auto + '_' + seller + '_' + userc;
        });

        function checkAdminStatuses() {
            fetch('/api/orders/status?ids=' + orderIds.join(','))
                .then(response => response.json())
                .then(orders => {
                    let shouldReload = false;
                    orders.forEach(order => {
                        const current = order.status + '_' + (order.cancel_request || 'none') + '_' + (order.is_completed ? '1' : '0') + '_' + (order.is_auto_confirmed ? '1' : '0') + '_' + (order.is_confirmed_by_seller ? '1' : '0') + '_' + (order.is_confirmed_by_user ? '1' : '0');
                        const prev = previousStatuses[order.id];
                        if (prev && prev !== current) {
                            shouldReload = true;
                        }
                        previousStatuses[order.id] = current;
                    });
                    if (shouldReload) location.reload();
                })
                .catch(error => console.log('Error polling order statuses:', error));
        }

        checkAdminStatuses();
        setInterval(checkAdminStatuses, 3000);
    }
});
</script>

<style>
.table-highlight {
    animation: highlight-pulse 1s ease-in-out 5;
    background-color: rgba(253, 126, 20, 0.3) !important;
}

@keyframes highlight-pulse {
    0%, 100% {
        background-color: rgba(253, 126, 20, 0.3);
    }
    50% {
        background-color: rgba(253, 126, 20, 0.5);
    }
}
</style>
@endsection

