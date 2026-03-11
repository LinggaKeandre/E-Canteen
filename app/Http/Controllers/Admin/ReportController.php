<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Exports\SalesReportExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->get('period', 'today');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        
// Determine date range based on period or custom dates
        switch ($period) {
            case 'today':
                $startDate = Carbon::today()->toDateString();
                $endDate = Carbon::today()->toDateString();
                $dateRange = 'Hari Ini (' . Carbon::today()->format('d M Y') . ')';
                break;
            case 'week':
                // Last 7 days including today
                $endDate = Carbon::today()->toDateString();
                $startDate = Carbon::now()->subDays(6)->toDateString();
                $dateRange = '7 Hari Terakhir (' . Carbon::now()->subDays(6)->format('d M') . ' - ' . Carbon::today()->format('d M Y') . ')';
                break;
            case 'month':
                // Last 30 days including today
                $endDate = Carbon::today()->toDateString();
                $startDate = Carbon::now()->subDays(29)->toDateString();
                $dateRange = '30 Hari Terakhir (' . Carbon::now()->subDays(29)->format('d M') . ' - ' . Carbon::today()->format('d M Y') . ')';
                break;
            case 'year':
                // Last 365 days including today
                $endDate = Carbon::today()->toDateString();
                $startDate = Carbon::now()->subDays(364)->toDateString();
                $dateRange = '1 Tahun Terakhir (' . Carbon::now()->subDays(364)->format('d M Y') . ' - ' . Carbon::today()->format('d M Y') . ')';
                break;
            case 'lifetime':
                $startDate = Order::min('created_at') ? Carbon::parse(Order::min('created_at'))->toDateString() : Carbon::now()->startOfYear()->toDateString();
                $endDate = Carbon::today()->toDateString();
                $dateRange = 'Semua Waktu';
                break;
            case 'custom':
                if ($startDate && $endDate) {
                    $dateRange = 'Custom (' . Carbon::parse($startDate)->format('d M Y') . ' - ' . Carbon::parse($endDate)->format('d M Y') . ')';
                } else {
                    $startDate = Carbon::today()->toDateString();
                    $endDate = Carbon::today()->toDateString();
                    $dateRange = 'Hari Ini (' . Carbon::today()->format('d M Y') . ')';
                }
                break;
            default:
                $startDate = Carbon::today()->toDateString();
                $endDate = Carbon::today()->toDateString();
                $dateRange = 'Hari Ini (' . Carbon::today()->format('d M Y') . ')';
        }
        
        // Get the logged-in seller's menu IDs
        $sellerId = auth()->id();
        $sellerMenuIds = Menu::where('user_id', $sellerId)->pluck('id');
        
        // Get order IDs that contain this seller's menu items
        $orderIds = OrderItem::whereIn('menu_id', $sellerMenuIds)->pluck('order_id')->unique();
        
        $orders = Order::with(['orderItems.menu', 'user'])
            ->whereIn('id', $orderIds)
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->get();
        
        $totalRevenue = $orders->sum('total_amount');
        $totalOrders = $orders->count();
        
        // Total sold (qty)
        $totalSold = OrderItem::whereHas('order', function ($query) use ($startDate, $endDate, $orderIds) {
            $query->whereIn('id', $orderIds)->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
        })
        ->whereIn('menu_id', $sellerMenuIds)
        ->sum('qty');
        
        // Group by menu - only for this seller's menus
        $salesByMenu = OrderItem::whereHas('order', function ($query) use ($startDate, $endDate, $orderIds) {
            $query->whereIn('id', $orderIds)->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
        })
        ->whereIn('menu_id', $sellerMenuIds)
        ->with('menu')
        ->get()
        ->groupBy('menu_id')
        ->map(function ($items, $menuId) {
            $menu = $items->first()->menu;
            $totalQty = $items->sum('qty');
            $totalSubtotal = $items->sum('subtotal');
            
            return (object) [
                'menu_name' => $menu->name,
                'total_qty' => $totalQty,
                'total_subtotal' => $totalSubtotal,
            ];
        })
        ->values();
        
        // Get daily sales data for chart - fill all dates including empty ones
        $allDates = [];
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);
        
        while ($start->lte($end)) {
            $allDates[$start->toDateString()] = ['revenue' => 0, 'orders' => 0];
            $start->addDay();
        }
        
        $dailySalesData = Order::whereIn('id', $orderIds)
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->selectRaw('DATE(created_at) as date, SUM(total_amount) as revenue, COUNT(*) as orders')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        foreach ($dailySalesData as $data) {
            if (isset($allDates[$data->date])) {
                $allDates[$data->date] = ['revenue' => $data->revenue, 'orders' => $data->orders];
            }
        }
        
        $dailySales = collect($allDates)->map(function ($item, $date) {
            return (object) ['date' => $date, 'revenue' => $item['revenue'], 'orders' => $item['orders']];
        })->values();
        
        return view('admin.reports.index', compact(
            'orders', 
            'totalRevenue', 
            'totalOrders', 
            'totalSold', 
            'salesByMenu', 
            'dailySales',
            'dateRange',
            'period',
            'startDate',
            'endDate'
        ));
    }
    
    public function export(Request $request)
    {
        $period = $request->get('period', 'today');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        
// Determine date range based on period or custom dates
        switch ($period) {
            case 'today':
                $startDate = Carbon::today()->toDateString();
                $endDate = Carbon::today()->toDateString();
                $dateRange = 'Hari Ini';
                break;
            case 'week':
                // Last 7 days including today
                $endDate = Carbon::today()->toDateString();
                $startDate = Carbon::now()->subDays(6)->toDateString();
                $dateRange = '7_Hari_Terakhir';
                break;
            case 'month':
                // Last 30 days including today
                $endDate = Carbon::today()->toDateString();
                $startDate = Carbon::now()->subDays(29)->toDateString();
                $dateRange = '30_Hari_Terakhir';
                break;
            case 'year':
                // Last 365 days including today
                $endDate = Carbon::today()->toDateString();
                $startDate = Carbon::now()->subDays(364)->toDateString();
                $dateRange = '1_Tahun_Terakhir';
                break;
            case 'lifetime':
                $startDate = Order::min('created_at') ? Carbon::parse(Order::min('created_at'))->toDateString() : Carbon::now()->startOfYear()->toDateString();
                $endDate = Carbon::today()->toDateString();
                $dateRange = 'Semua_Waktu';
                break;
            default:
                $startDate = Carbon::today()->toDateString();
                $endDate = Carbon::today()->toDateString();
                $dateRange = 'Hari_Init';
        }
        
        // Get the logged-in seller's menu IDs
        $sellerId = auth()->id();
        $sellerMenuIds = Menu::where('user_id', $sellerId)->pluck('id');
        
        // Get order IDs that contain this seller's menu items
        $orderIds = OrderItem::whereIn('menu_id', $sellerMenuIds)->pluck('order_id')->unique();
        
        // Get sales data for export - include individual items with variant/addons
        $orderItems = OrderItem::whereHas('order', function ($query) use ($startDate, $endDate, $orderIds) {
            $query->whereIn('id', $orderIds)->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
        })
        ->whereIn('menu_id', $sellerMenuIds)
        ->with('menu')
        ->get();
        
        // Build sales data with variant/addons details
        $salesData = [];
        $totalRevenue = 0;
        $totalSold = 0;
        
        foreach ($orderItems as $item) {
            $menuName = $item->menu->name;
            $variantAddon = '';
            
            // Add variant
            if ($item->variant_name) {
                $variantAddon = $item->variant_name;
            }
            
            // Add addons
            if (!empty($item->addons)) {
                $addonNames = [];
                foreach ($item->addons as $addon) {
                    $addonNames[] = $addon['name'] . ' (Rp ' . number_format($addon['price'], 0, ',', '.') . ')';
                }
                $variantAddon .= ($variantAddon ? ' + ' : '') . implode(', ', $addonNames);
            }
            
            $salesData[] = [
                'menu_name' => $menuName,
                'variant_addons' => $variantAddon ?: '-',
                'price' => $item->price,
                'total_qty' => $item->qty,
                'total_subtotal' => $item->subtotal,
            ];
            
            $totalRevenue += $item->subtotal;
            $totalSold += $item->qty;
        }
        
        return Excel::download(new SalesReportExport($salesData, $dateRange, $totalRevenue, $totalSold), 'laporan_penjualan_' . $dateRange . '.xlsx');
    }
    
    public function exportPdf(Request $request)
    {
        $period = $request->get('period', 'today');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        
        // Determine date range based on period or custom dates
        switch ($period) {
            case 'today':
                $startDate = Carbon::today()->toDateString();
                $endDate = Carbon::today()->toDateString();
                $dateRange = 'Hari Ini';
                $dateRangeDisplay = Carbon::today()->format('d M Y');
                break;
            case 'week':
                $endDate = Carbon::today()->toDateString();
                $startDate = Carbon::now()->subDays(6)->toDateString();
                $dateRange = '7_Hari_Terakhir';
                $dateRangeDisplay = Carbon::now()->subDays(6)->format('d M') . ' - ' . Carbon::today()->format('d M Y');
                break;
            case 'month':
                $endDate = Carbon::today()->toDateString();
                $startDate = Carbon::now()->subDays(29)->toDateString();
                $dateRange = '30_Hari_Terakhir';
                $dateRangeDisplay = Carbon::now()->subDays(29)->format('d M') . ' - ' . Carbon::today()->format('d M Y');
                break;
            case 'year':
                $endDate = Carbon::today()->toDateString();
                $startDate = Carbon::now()->subDays(364)->toDateString();
                $dateRange = '1_Tahun_Terakhir';
                $dateRangeDisplay = Carbon::now()->subDays(364)->format('d M Y') . ' - ' . Carbon::today()->format('d M Y');
                break;
            case 'lifetime':
                $startDate = Order::min('created_at') ? Carbon::parse(Order::min('created_at'))->toDateString() : Carbon::now()->startOfYear()->toDateString();
                $endDate = Carbon::today()->toDateString();
                $dateRange = 'Semua_Waktu';
                $dateRangeDisplay = 'Semua Waktu';
                break;
            default:
                $startDate = Carbon::today()->toDateString();
                $endDate = Carbon::today()->toDateString();
                $dateRange = 'Hari_Init';
                $dateRangeDisplay = Carbon::today()->format('d M Y');
        }
        
        // Get the logged-in seller's menu IDs
        $sellerId = auth()->id();
        $sellerMenuIds = Menu::where('user_id', $sellerId)->pluck('id');
        
        // Get order IDs that contain this seller's menu items
        $orderIds = OrderItem::whereIn('menu_id', $sellerMenuIds)->pluck('order_id')->unique();
        
        // Get sales data for PDF - include individual items with variant/addons
        $orderItems = OrderItem::whereHas('order', function ($query) use ($startDate, $endDate, $orderIds) {
            $query->whereIn('id', $orderIds)->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
        })
        ->whereIn('menu_id', $sellerMenuIds)
        ->with('menu')
        ->get();
        
        // Build sales data with variant/addons details
        $salesData = [];
        $totalRevenue = 0;
        $totalSold = 0;
        
        foreach ($orderItems as $item) {
            $menuName = $item->menu->name;
            $variantAddon = '';
            
            // Add variant
            if ($item->variant_name) {
                $variantAddon = $item->variant_name;
            }
            
            // Add addons
            if (!empty($item->addons)) {
                $addonNames = [];
                foreach ($item->addons as $addon) {
                    $addonNames[] = $addon['name'] . ' (Rp ' . number_format($addon['price'], 0, ',', '.') . ')';
                }
                $variantAddon .= ($variantAddon ? ' + ' : '') . implode(', ', $addonNames);
            }
            
            $salesData[] = [
                'menu_name' => $menuName,
                'variant_addons' => $variantAddon ?: '-',
                'price' => $item->price,
                'total_qty' => $item->qty,
                'total_subtotal' => $item->subtotal,
            ];
            
            $totalRevenue += $item->subtotal;
            $totalSold += $item->qty;
        }
        
        // Get daily sales data for PDF table
        $allDates = [];
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);
        
        while ($start->lte($end)) {
            $allDates[$start->toDateString()] = ['revenue' => 0, 'orders' => 0];
            $start->addDay();
        }
        
        $dailySalesData = Order::whereIn('id', $orderIds)
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->selectRaw('DATE(created_at) as date, SUM(total_amount) as revenue, COUNT(*) as orders')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        foreach ($dailySalesData as $data) {
            if (isset($allDates[$data->date])) {
                $allDates[$data->date] = ['revenue' => $data->revenue, 'orders' => $data->orders];
            }
        }
        
        $dailySales = collect($allDates)->map(function ($item, $date) {
            return (object) ['date' => $date, 'revenue' => $item['revenue'], 'orders' => $item['orders']];
        })->values();
        
        $pdf = Pdf::loadView('admin.reports.pdf', [
            'salesData' => $salesData,
            'dateRange' => $dateRange,
            'dateRangeDisplay' => $dateRangeDisplay,
            'totalRevenue' => $totalRevenue,
            'totalSold' => $totalSold,
            'period' => $period,
            'dailySales' => $dailySales,
        ])->setPaper('a4', 'landscape');
        
        return $pdf->download('laporan_penjualan_' . $dateRange . '.pdf');
    }
}
