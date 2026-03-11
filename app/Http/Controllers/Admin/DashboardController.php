<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $sellerId = auth()->id();
        
        $sellerMenuIds = Menu::where('user_id', $sellerId)->pluck('id');
        $orderIds = OrderItem::whereIn('menu_id', $sellerMenuIds)->pluck('order_id')->unique();
        
        $totalMenus = Menu::where('user_id', $sellerId)->count();
        
        $todayOrders = Order::whereIn('id', $orderIds)
            ->whereDate('created_at', today())
            ->count();
        
        // Today's completed revenue
        $todayRevenue = Order::whereIn('id', $orderIds)
            ->whereDate('created_at', today())
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
        
        // Wallet stats - total completed earnings
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
        
        // Available to withdraw (completed orders)
        $availableBalance = $totalEarnings;
        
        $timeFilter = $request->get('period', 'day');
        $topProducts = $this->getTopProducts($timeFilter, $sellerMenuIds);
        
        $pendingOrders = Order::with(['user', 'orderItems.menu'])
            ->whereIn('id', $orderIds)
            ->where('status', 'pending')
            ->where('is_paid', true)
            ->where('cancel_request', '!=', 'accepted')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        return view('admin.dashboard', compact(
            'totalMenus', 
            'todayOrders', 
            'todayRevenue',
            'topProducts',
            'pendingOrders',
            'timeFilter',
            'totalEarnings',
            'pendingBalance',
            'availableBalance'
        ));
    }
    
    private function getTopProducts($period, $sellerMenuIds)
    {
        $dateCondition = match($period) {
            'day' => now()->startOfDay(),
            'week' => now()->startOfWeek(),
            'month' => now()->startOfMonth(),
            'lifetime' => null,
            default => now()->startOfDay(),
        };
        
        $query = OrderItem::select(
                'menu_id',
                DB::raw('SUM(order_items.qty) as total_sold'),
                DB::raw('SUM(order_items.subtotal) as total_revenue')
            )
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('menus', 'order_items.menu_id', '=', 'menus.id')
            ->whereIn('menus.id', $sellerMenuIds)
            ->where(function ($q) use ($dateCondition) {
                if ($dateCondition !== null) {
                    $q->where('orders.created_at', '>=', $dateCondition);
                }
                $q->where(function ($query) {
                    $query->where(function ($subq) {
                        $subq->where('orders.is_completed', true)
                             ->where('orders.is_confirmed_by_seller', true)
                             ->where('orders.is_confirmed_by_user', true);
                    })->orWhere(function ($subq) {
                        $subq->where('orders.is_auto_confirmed', true)
                             ->where('orders.is_confirmed_by_seller', true)
                             ->where('orders.is_confirmed_by_user', true);
                    });
                });
            })
            ->groupBy('menu_id')
            ->orderBy('total_sold', 'desc')
            ->limit(5);
        
        $results = $query->get();
        
        return $results->map(function ($item) {
            $menu = Menu::find($item->menu_id);
            return [
                'menu' => $menu,
                'total_sold' => $item->total_sold,
                'total_revenue' => $item->total_revenue,
                'average_rating' => $menu ? $menu->average_rating : 0,
                'rating_count' => $menu ? $menu->rating_count : 0,
            ];
        });
    }
}

