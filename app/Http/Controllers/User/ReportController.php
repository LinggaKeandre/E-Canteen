<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\Order;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function create(Order $order)
    {
        // Only the order owner can report
        if ($order->user_id !== auth()->id()) {
            return back()->with('error', 'Anda tidak memiliki akses ke pesanan ini.');
        }
        
        // Check if order is completed
        if (!$order->is_completed && !$order->is_auto_confirmed) {
            return back()->with('error', 'Hanya pesanan yang sudah selesai dapat seringkai.');
        }
        
        // Check if already reported
        $existingReport = Report::where('order_id', $order->id)->where('user_id', auth()->id())->first();
        if ($existingReport) {
            return back()->with('error', 'Pesanan ini sudah pernah seringkai.');
        }
        
        return view('user.reports.create', compact('order'));
    }

    public function store(Request $request, Order $order)
    {
        // Only the order owner can report
        if ($order->user_id !== auth()->id()) {
            return back()->with('error', 'Anda tidak memiliki akses ke pesanan ini.');
        }
        
        // Check if already reported
        $existingReport = Report::where('order_id', $order->id)->where('user_id', auth()->id())->first();
        if ($existingReport) {
            return back()->with('error', 'Pesanan ini sudah pernah seringkai.');
        }
        
        $request->validate([
            'reason' => 'required|in:food_quality,missing_item,wrong_order,late_delivery,seller_behavior,other',
            'description' => 'nullable|string|max:1000',
        ]);
        
        Report::create([
            'user_id' => auth()->id(),
            'order_id' => $order->id,
            'reason' => $request->reason,
            'description' => $request->description,
            'status' => 'pending',
        ]);
        
        return redirect()->route('user.orders.index')->with('success', 'Laporan berhasil dikirim. Tim kami akan meninjaunya.');
    }
}

