<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status', 'all');
        
        $query = Report::with(['user', 'order', 'reviewer']);
        
        if ($status !== 'all') {
            $query->where('status', $status);
        }
        
        $reports = $query->orderBy('created_at', 'desc')->paginate(20);
        
        $stats = [
            'pending' => Report::where('status', 'pending')->count(),
            'reviewed' => Report::where('status', 'reviewed')->count(),
            'resolved' => Report::where('status', 'resolved')->count(),
            'rejected' => Report::where('status', 'rejected')->count(),
        ];
        
        return view('superadmin.reports.index', compact('reports', 'stats', 'status'));
    }

    public function show(Report $report)
    {
        $report->load(['user', 'order', 'order.orderItems.menu', 'reviewer']);
        return view('superadmin.reports.show', compact('report'));
    }

    public function update(Request $request, Report $report)
    {
        $request->validate([
            'status' => 'required|in:reviewed,resolved,rejected',
            'resolution_notes' => 'nullable|string|max:1000',
        ]);

        $report->update([
            'status' => $request->status,
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
            'resolution_notes' => $request->resolution_notes,
        ]);

        return redirect()->route('superadmin.reports.index')->with('success', 'Laporan berhasil diperbarui.');
    }
}

