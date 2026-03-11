<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\BalanceTransaction;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderHistoryController extends Controller
{
    public function index()
    {
        $orders = Order::with(['orderItems.menu', 'ratings', 'report'])
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(10);
        
        return view('user.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        // Check if order belongs to authenticated user
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }
        
        $order->load(['orderItems.menu', 'user', 'ratings']);
        
        return view('user.orders.show', compact('order'));
    }

    /**
     * Request to cancel an order.
     * If seller hasn't processed yet -> instant cancel
     * If seller already confirmed -> pending request
     */
    public function requestCancel(Request $request, Order $order)
    {
        // Verify order belongs to user
        if ($order->user_id !== Auth::id()) {
            return back()->with('error', 'Pesanan tidak ditemukan.');
        }

        // Verify only pending orders can be cancelled
        if (!$order->canUserRequestCancel() || $order->status !== 'pending') {
            return back()->with('error', 'Pesanan tidak dapat dibatalkan.');
        }

        DB::beginTransaction();
        try {
            // Perform instant cancellation and refund
            $order->update([
                'cancel_request' => 'accepted',
                'cancel_requested_at' => now(),
                'cancel_responded_at' => now(),
                'is_completed' => true,
                'completed_at' => now(),
            ]);

            // Refund balance to user
            $user = Auth::user();
            $balanceBefore = $user->balance;
            $user->balance = $user->balance + $order->total_amount;
            $user->save();

            // Record transaction
            BalanceTransaction::create([
                'user_id' => $user->id,
                'order_id' => $order->id,
                'type' => 'credit',
                'amount' => $order->total_amount,
                'balance_before' => $balanceBefore,
                'balance_after' => $user->balance,
                'notes' => 'Pembatalan pesanan #' . $order->id,
            ]);

            DB::commit();
            return back()->with('success', 'Pesanan berhasil dibatalkan. Dana telah dikembalikan ke saldo Anda.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan. Silakan coba lagi.');
        }
    }
}
