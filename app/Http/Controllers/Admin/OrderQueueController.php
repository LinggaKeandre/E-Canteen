<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Menu;
use App\Models\OrderItem;
use App\Models\User;
use App\Models\BalanceTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderQueueController extends Controller
{
    public function index(Request $request)
    {
        $highlightId = $request->highlight;
        
        // Get the logged-in seller's ID
        $sellerId = auth()->id();
        
        // Get menu IDs that belong to this seller
        $sellerMenuIds = Menu::where('user_id', $sellerId)->pluck('id');
        
        // Get order IDs that contain items from this seller's menus
        $orderIds = OrderItem::whereIn('menu_id', $sellerMenuIds)->pluck('order_id')->unique();
        
        // Get pagination per slot first - only orders containing this seller's items
        $istirahat1Orders = Order::with(['user', 'orderItems.menu'])
            ->whereIn('id', $orderIds)
            ->where('pickup_slot', 'istirahat_1')
            ->orderBy('created_at', 'desc')
            ->paginate(5, ['*'], 'page_istirahat1');
            
        $istirahat2Orders = Order::with(['user', 'orderItems.menu'])
            ->whereIn('id', $orderIds)
            ->where('pickup_slot', 'istirahat_2')
            ->orderBy('created_at', 'desc')
            ->paginate(5, ['*'], 'page_istirahat2');
        
        // If there's a highlight parameter, check if order exists in current pages
        // If not found in current page, redirect to correct page
        if ($highlightId) {
            // Check if order exists in istirahat1 current page
            $foundInIstirahat1 = $istirahat1Orders->pluck('id')->contains($highlightId);
            // Check if order exists in istirahat2 current page
            $foundInIstirahat2 = $istirahat2Orders->pluck('id')->contains($highlightId);
            
            if (!$foundInIstirahat1 && !$foundInIstirahat2) {
                // Order not in current pages, find which page it belongs to
                // Only look for orders that belong to this seller
                $highlightOrder = Order::whereIn('id', $orderIds)->find($highlightId);
                
                if ($highlightOrder) {
                    $highlightSlot = $highlightOrder->pickup_slot;
                    
                    if ($highlightSlot === 'istirahat_1') {
                        // Find position in istirahat_1 orders (filtered by seller)
                        $allIstirahat1Ids = Order::whereIn('id', $orderIds)
                            ->where('pickup_slot', 'istirahat_1')
                            ->orderBy('created_at', 'desc')
                            ->pluck('id');
                        $position = $allIstirahat1Ids->search($highlightId);
                        
                        if ($position !== false) {
                            $page = floor($position / 5) + 1;
                            // Redirect to correct page
                            return redirect()->route('admin.orders.index', [
                                'page_istirahat1' => $page,
                                'highlight' => $highlightId
                            ]);
                        }
                    } elseif ($highlightSlot === 'istirahat_2') {
                        // Find position in istirahat_2 orders (filtered by seller)
                        $allIstirahat2Ids = Order::whereIn('id', $orderIds)
                            ->where('pickup_slot', 'istirahat_2')
                            ->orderBy('created_at', 'desc')
                            ->pluck('id');
                        $position = $allIstirahat2Ids->search($highlightId);
                        
                        if ($position !== false) {
                            $page = floor($position / 5) + 1;
                            // Redirect to correct page
                            return redirect()->route('admin.orders.index', [
                                'page_istirahat2' => $page,
                                'highlight' => $highlightId
                            ]);
                        }
                    }
                }
            }
            // If found in current pages, just display normally (highlight will be handled by JS)
        }
        
        return view('admin.orders.index', compact('istirahat1Orders', 'istirahat2Orders'));
    }

    // Accept (confirm) an order - change from pending to preparing
    public function acceptOrder(Order $order)
    {
        // Can only accept orders with status "pending"
        if ($order->status !== 'pending') {
            return back()->with('error', 'Hanya pesanan dengan status Pending yang dapat diterima.');
        }
        
        $order->update(['status' => 'preparing']);
        
        return back()->with('success', 'Pesanan berhasil diterima. Status: Sedang Disiapkan.');
    }

    public function updateStatus(Order $order, Request $request)
    {
        $request->validate([
            'status' => 'required|in:pending,preparing,ready',
        ]);

        // Cannot change status if there's a pending cancel request (for preparing -> ready)
        if ($order->cancel_request === 'pending' && $request->status !== 'pending') {
            return back()->with('error', 'Tidak dapat mengubah status. Ada permintaan pembatalan yang pending.');
        }

        // If changing TO "pending" (rejecting cancel request), reset the status
        if ($request->status === 'pending' && $order->cancel_request === 'pending') {
            $order->update([
                'status' => 'pending',
                'cancel_request' => 'none',
                'cancel_responded_at' => null,
            ]);
            return back()->with('success', 'Status pesanan dikembalikan ke Pending.');
        }

        $updateData = ['status' => $request->status];
        
        // When status changes to ready, record the time
        if ($request->status === 'ready' && $order->status !== 'ready') {
            $updateData['ready_at'] = now();
        }
        
        $order->update($updateData);

        return back()->with('success', 'Status pesanan berhasil diperbarui.');
    }

    // Seller confirms they've handed over the order to the user
    // This is the FIRST step - seller initiates handover
    public function confirmHandover(Order $order)
    {
        // Cannot confirm if there's a pending cancel request
        if ($order->cancel_request === 'pending') {
            return back()->with('error', 'Tidak dapat mengkonfirmasi. Ada permintaan pembatalan yang pending.');
        }
        
        // Can only confirm if order is ready
        if ($order->status !== 'ready') {
            return back()->with('error', 'Pesanan harus berstatus Siap Diambil.');
        }
        
        if ($order->is_confirmed_by_seller) {
            return back()->with('error', 'Konfirmasi serah terima sudah dilakukan.');
        }
        
        $order->update(['is_confirmed_by_seller' => true]);
        
        return back()->with('success', 'Konfirmasi serah terima berhasil. Menunggu konfirmasi dari pembeli.');
    }

    // Seller responds to cancel request
    public function respondCancel(Request $request, Order $order)
    {
        $request->validate([
            'action' => 'required|in:accept,reject',
        ]);

        // Can only respond to pending cancel requests
        if (!$order->canSellerRespondToCancel()) {
            return back()->with('error', 'Tidak ada permintaan pembatalan yang pending.');
        }

        // Auto-accept if status is "pending" (shouldn't happen with new logic but just in case)
        if ($order->shouldAutoAcceptCancel()) {
            DB::beginTransaction();
            try {
                $user = $order->user;
                $balanceBefore = $user->balance;
                $user->balance += $order->total_amount;
                $user->save();

                BalanceTransaction::create([
                    'user_id' => $user->id,
                    'order_id' => $order->id,
                    'type' => 'credit',
                    'amount' => $order->total_amount,
                    'balance_before' => $balanceBefore,
                    'balance_after' => $user->balance,
                    'notes' => 'Pembatalan pesanan #' . $order->id . ' (Auto-accept - Status Pending)',
                ]);

                $order->update([
                    'cancel_request' => 'accepted',
                    'cancel_responded_at' => now(),
                    'is_completed' => true,
                    'completed_at' => now(),
                ]);

                DB::commit();
                return back()->with('success', 'Permintaan pembatalan diterima secara otomatis (Status Pending). Dana telah dikembalikan.');
            } catch (\Exception $e) {
                DB::rollBack();
                return back()->with('error', 'Terjadi kesalahan. Silakan coba lagi.');
            }
        }

        DB::beginTransaction();
        try {
            if ($request->action === 'accept') {
                // Accept cancel - refund user (only for "preparing" status)
                $user = $order->user;
                $balanceBefore = $user->balance;
                $user->balance += $order->total_amount;
                $user->save();

                // Record transaction
                BalanceTransaction::create([
                    'user_id' => $user->id,
                    'order_id' => $order->id,
                    'type' => 'credit',
                    'amount' => $order->total_amount,
                    'balance_before' => $balanceBefore,
                    'balance_after' => $user->balance,
                    'notes' => 'Pembatalan pesanan #' . $order->id . ' (Disetujui Penjual)',
                ]);

                $order->update([
                    'cancel_request' => 'accepted',
                    'cancel_responded_at' => now(),
                    'is_completed' => true,
                    'completed_at' => now(),
                ]);

                DB::commit();
                return back()->with('success', 'Permintaan pembatalan diterima. Dana telah dikembalikan ke pembeli.');
            } else {
                // Reject cancel - continue with order
                $order->update([
                    'cancel_request' => 'rejected',
                    'cancel_responded_at' => now(),
                ]);

                DB::commit();
                return back()->with('success', 'Permintaan pembatalan ditolak. Pesanan akan terus diproses.');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan. Silakan coba lagi.');
        }
    }
}
