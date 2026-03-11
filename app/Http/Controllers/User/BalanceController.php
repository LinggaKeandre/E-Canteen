<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\BalanceTransaction;
use App\Models\TopUpRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BalanceController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        $transactions = BalanceTransaction::where('user_id', $userId)
            ->latest()
            ->paginate(10);

        // also retrieve top-up requests so users can see rejection notes
        $topups = TopUpRequest::where('user_id', $userId)
            ->latest()
            ->paginate(10);
        
        // Get global QR code for top-up
        $globalQrCode = User::whereNotNull('topup_qr_code')->first();
        
        return view('user.balance.index', compact('transactions', 'topups', 'globalQrCode'));
    }

    public function topup(Request $request)
    {
        $request->validate([
            'amount' => 'required|integer|min:1000',
            'payment_method' => 'required|string',
            'payment_proof' => 'required|image|max:2048',
        ]);

        $amount = $request->amount;
        $user = Auth::user();

        // Handle payment proof upload
        $paymentProofPath = null;
        if ($request->hasFile('payment_proof')) {
            $paymentProofPath = $request->file('payment_proof')->store('payment_proofs', 'public');
        }

        // Create top-up request (pending approval from superadmin)
        TopUpRequest::create([
            'user_id' => $user->id,
            'amount' => $amount,
            'payment_method' => $request->payment_method,
            'payment_proof' => $paymentProofPath,
            'status' => 'pending',
        ]);

        return back()->with('success', 'Permintaan top-up telah dikirim! Menunggu persetujuan superadmin.');
    }

    // Quick balance add for demo/testing (bypass superadmin approval)
    public function quickTopup(Request $request)
    {
        $request->validate([
            'amount' => 'required|integer|min:1000',
        ]);

        $amount = $request->amount;
        $user = Auth::user();
        $balanceBefore = $user->balance;
        
        // Update balance using DB query builder
        $newBalance = $balanceBefore + $amount;
        DB::table('users')
            ->where('id', $user->id)
            ->update(['balance' => $newBalance]);
        
        BalanceTransaction::create([
            'user_id' => $user->id,
            'order_id' => null,
            'type' => 'credit',
            'amount' => $amount,
            'balance_before' => $balanceBefore,
            'balance_after' => $newBalance,
            'notes' => 'Top up saldo',
        ]);

        return back()->with('success', 'Saldo berhasil ditambahkan!');
    }
}

