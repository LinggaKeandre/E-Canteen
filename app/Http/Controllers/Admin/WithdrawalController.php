<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\WithdrawalRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WithdrawalController extends Controller
{
    public function index()
    {
        $sellerId = auth()->id();
        
        // Get seller's menu IDs and order IDs
        $sellerMenuIds = Menu::where('user_id', $sellerId)->pluck('id');
        $orderIds = OrderItem::whereIn('menu_id', $sellerMenuIds)->pluck('order_id')->unique();
        
        // Calculate total completed earnings
        $totalEarnings = Order::whereIn('id', $orderIds)
            ->where(function ($query) {
                $query->where(function ($q) {
                    $q->where('is_completed', true)
                      ->where('is_confirmed_by_seller', true)
                      ->where('is_confirmed_by_user', true);
                })->orWhere(function ($q) {
                    $q->where('is_auto_confirmed', true)
                      ->where('is_confirmed_by_seller', true)
                      ->where('is_confirmed_by_user', true);
                });
            })
            ->sum('total_amount');
        
        // Pending (in escrow) - paid but not yet confirmed by buyer
        $pendingBalance = Order::whereIn('id', $orderIds)
            ->where('is_paid', true)
            ->where('is_confirmed_by_seller', true)
            ->where('is_confirmed_by_user', false)
            ->where('cancel_request', '!=', 'accepted')
            ->sum('total_amount');
        
        // Available to withdraw (completed orders minus already withdrawn)
        $alreadyWithdrawn = WithdrawalRequest::where('user_id', $sellerId)
            ->where('status', 'approved')
            ->sum('amount');
        
        $availableBalance = max(0, $totalEarnings - $alreadyWithdrawn);
        
        // Get withdrawal history
        $withdrawalRequests = WithdrawalRequest::where('user_id', $sellerId)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('admin.withdrawal.index', compact(
            'totalEarnings',
            'pendingBalance',
            'availableBalance',
            'alreadyWithdrawn',
            'withdrawalRequests'
        ));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'amount' => 'required|integer|min:10000',
            'appointment_date' => 'required|date|after:today',
            'appointment_time' => 'required',
        ], [
            'amount.min' => 'Minimal penarikan adalah Rp 10.000',
            'appointment_date.after' => 'Tanggal janji temu minimal H+1 dari hari ini',
        ]);
        
        $sellerId = auth()->id();
        
        // Get available balance
        $sellerMenuIds = Menu::where('user_id', $sellerId)->pluck('id');
        $orderIds = OrderItem::whereIn('menu_id', $sellerMenuIds)->pluck('order_id')->unique();
        
        $totalEarnings = Order::whereIn('id', $orderIds)
            ->where(function ($query) {
                $query->where(function ($q) {
                    $q->where('is_completed', true)
                      ->where('is_confirmed_by_seller', true)
                      ->where('is_confirmed_by_user', true);
                })->orWhere(function ($q) {
                    $q->where('is_auto_confirmed', true)
                      ->where('is_confirmed_by_seller', true)
                      ->where('is_confirmed_by_user', true);
                });
            })
            ->sum('total_amount');
        
        $alreadyWithdrawn = WithdrawalRequest::where('user_id', $sellerId)
            ->where('status', 'approved')
            ->sum('amount');
        
        $availableBalance = max(0, $totalEarnings - $alreadyWithdrawn);
        
        if ($request->amount > $availableBalance) {
            return back()->with('error', 'Saldo tidak mencukupi. Saldo tersedia: Rp ' . number_format($availableBalance, 0, ',', '.'));
        }
        
        // Get bank info from user table
        $user = auth()->user();
        
        // Validate time is within allowed range (07:00 - 15:00)
        $time = $request->appointment_time;
        $hour = (int)explode(':', $time)[0];
        if ($hour < 7 || $hour >= 15) {
            return back()->with('error', 'Waktu janji temu hanya bisa dipilih antara jam 07:00 - 15:00');
        }
        
        // Create withdrawal request with appointment details
        WithdrawalRequest::create([
            'user_id' => $sellerId,
            'amount' => $request->amount,
            'bank_name' => $user->bank_name,
            'bank_account' => $user->account_number,
            'account_holder' => $user->name,
            'status' => 'pending',
            'notes' => 'Janji temu: ' . $request->appointment_date . ' jam ' . $request->appointment_time .
                       ($request->notes ? '. Catatan: ' . $request->notes : ''),
        ]);
        
        return back()->with('success', 'Permintaan penarikan berhasil dikirim. Menunggu approval superadmin.');
    }
}
