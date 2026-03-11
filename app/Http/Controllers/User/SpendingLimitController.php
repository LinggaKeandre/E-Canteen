<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SpendingLimitController extends Controller
{
    /**
     * Show spending limit settings page
     */
    public function index()
    {
        $user = Auth::user();
        $dailyLimitEnabled = $user->isDailySpendingLimitEnabled();
        $dailyLimit = $user->daily_spending_limit;
        $todaySpending = $dailyLimitEnabled ? $user->getTodaySpending() : 0;
        $remainingDailyLimit = $dailyLimitEnabled ? $user->getRemainingDailyLimit() : null;
        
        return view('user.settings.spending-limit', compact(
            'dailyLimitEnabled', 
            'dailyLimit', 
            'todaySpending', 
            'remainingDailyLimit'
        ));
    }

    /**
     * Enable daily spending limit
     */
    public function enable(Request $request)
    {
        $request->validate([
            'limit_amount' => 'required|integer|min:1000|max:10000000',
        ], [
            'limit_amount.required' => 'Masukkan batas pengeluaran harian.',
            'limit_amount.integer' => 'Batas harus berupa angka.',
            'limit_amount.min' => 'Batas minimal Rp 1.000.',
            'limit_amount.max' => 'Batas maksimal Rp 10.000.000.',
        ]);

        $user = Auth::user();
        $user->enableDailySpendingLimit($request->limit_amount);

        return redirect()->route('user.settings.spendingLimit')
            ->with('success', 'Batas pengeluaran harian berhasil diatur! Anda tidak dapat checkout jika melebihi batas.');
    }

    /**
     * Disable daily spending limit
     */
    public function disable(Request $request)
    {
        $user = Auth::user();
        $user->disableDailySpendingLimit();

        return redirect()->route('user.settings.spendingLimit')
            ->with('success', 'Batas pengeluaran harian telah dinonaktifkan.');
    }
}

