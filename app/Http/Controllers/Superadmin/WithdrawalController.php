<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\WithdrawalRequest;
use App\Models\BalanceTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WithdrawalController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status', 'all');
        
        $query = WithdrawalRequest::with(['user', 'processor']);
        
        if ($status !== 'all') {
            $query->where('status', $status);
        }
        
        $requests = $query->orderBy('created_at', 'desc')->paginate(20);
        
        $stats = [
            'pending' => WithdrawalRequest::where('status', 'pending')->count(),
            'approved' => WithdrawalRequest::where('status', 'approved')->count(),
            'rejected' => WithdrawalRequest::where('status', 'rejected')->count(),
            'total_pending' => WithdrawalRequest::where('status', 'pending')->sum('amount'),
        ];
        
        return view('superadmin.withdrawal.index', compact('requests', 'stats', 'status'));
    }

    public function approve(WithdrawalRequest $withdrawalRequest)
    {
        if ($withdrawalRequest->status !== 'pending') {
            return back()->with('error', 'Request sudah diproses.');
        }

        // Check if seller has enough balance
        $user = $withdrawalRequest->user;
        if ($user->balance < $withdrawalRequest->amount) {
            return back()->with('error', 'Saldo user tidak mencukupi.');
        }

        DB::beginTransaction();
        try {
            // Deduct balance from user
            $balanceBefore = $user->balance;
            $user->balance -= $withdrawalRequest->amount;
            $user->save();

            // Record transaction
            BalanceTransaction::create([
                'user_id' => $user->id,
                'type' => 'debit',
                'amount' => $withdrawalRequest->amount,
                'balance_before' => $balanceBefore,
                'balance_after' => $user->balance,
                'notes' => 'Penarikan saldo ke ' . $withdrawalRequest->bank_name . ' (' . $withdrawalRequest->account_number . ')',
            ]);

            // Update request status
            $withdrawalRequest->update([
                'status' => 'approved',
                'processed_by' => auth()->id(),
                'processed_at' => now(),
            ]);

            DB::commit();
            return back()->with('success', 'Penarikan disetujui. Saldo seller berhasil dikurangkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function reject(Request $request, WithdrawalRequest $withdrawalRequest)
    {
        if ($withdrawalRequest->status !== 'pending') {
            return back()->with('error', 'Request sudah diproses.');
        }

        $request->validate([
            'notes' => 'nullable|string|max:500',
        ]);

        $withdrawalRequest->update([
            'status' => 'rejected',
            'processed_by' => auth()->id(),
            'processed_at' => now(),
            'notes' => $request->notes,
        ]);

        return back()->with('success', 'Penarikan ditolak.');
    }
}

