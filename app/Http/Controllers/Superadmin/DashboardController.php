<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\TopUpRequest;
use App\Models\WithdrawalRequest;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Get date filters
        $period = $request->get('period', 'day');
        
        $dateCondition = match($period) {
            'day' => now()->startOfDay(),
            'week' => now()->startOfWeek(),
            'month' => now()->startOfMonth(),
            'year' => now()->startOfYear(),
            default => now()->startOfDay(),
        };

        // Overview Statistics
        $totalUsers = User::where('role', 'user')->count();
        $totalSellers = User::where('role', 'admin')->count();
        $totalMenus = Menu::count();
        
        // Orders in period (only completed orders)
        $ordersInPeriod = Order::where('created_at', '>=', $dateCondition)
            ->where(function ($query) {
                $query->where(function ($q) {
                    $q->where('is_completed', true)
                      ->orWhere('is_auto_confirmed', true);
                })->where('is_confirmed_by_seller', true)
                   ->where('is_confirmed_by_user', true);
            })
            ->count();
        
        // Revenue in period (sum of completed orders)
        $revenueInPeriod = Order::where('created_at', '>=', $dateCondition)
            ->where(function ($query) {
                $query->where(function ($q) {
                    $q->where('is_completed', true)
                      ->orWhere('is_auto_confirmed', true);
                })->where('is_confirmed_by_seller', true)
                   ->where('is_confirmed_by_user', true);
            })
            ->sum('total_amount');

        // Today's stats
        $todayOrders = Order::whereDate('created_at', today())
            ->where(function ($query) {
                $query->where(function ($q) {
                    $q->where('is_completed', true)
                      ->orWhere('is_auto_confirmed', true);
                })->where('is_confirmed_by_seller', true)
                   ->where('is_confirmed_by_user', true);
            })
            ->count();
            
        $todayRevenue = Order::whereDate('created_at', today())
            ->where(function ($query) {
                $query->where(function ($q) {
                    $q->where('is_completed', true)
                      ->orWhere('is_auto_confirmed', true);
                })->where('is_confirmed_by_seller', true)
                   ->where('is_confirmed_by_user', true);
            })
            ->sum('total_amount');

        // Pending Requests
        $pendingTopUps = TopUpRequest::where('status', 'pending')->count();
        $pendingWithdrawals = WithdrawalRequest::where('status', 'pending')->count();
        $pendingReports = Report::where('status', 'pending')->count();

        // Top Stores by Revenue (last 30 days)
        $topStores = $this->getTopStores(30);
        
        // Top Products (last 30 days)
        $topProducts = $this->getTopProducts(30);
        
        // Recent Orders
        $recentOrders = Order::with(['user', 'orderItems.menu'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Orders by Status (today)
        $ordersByStatus = [
            'pending' => Order::whereDate('created_at', today())->where('status', 'pending')->count(),
            'preparing' => Order::whereDate('created_at', today())->where('status', 'preparing')->count(),
            'ready' => Order::whereDate('created_at', today())->where('status', 'ready')->count(),
            'completed' => Order::whereDate('created_at', today())
                ->where(function ($query) {
                    $query->where('is_completed', true)
                          ->orWhere('is_auto_confirmed', true);
                })->count(),
        ];

        // Monthly Revenue (last 6 months)
        $monthlyRevenue = $this->getMonthlyRevenue(6);

        return view('superadmin.dashboard', compact(
            'totalUsers',
            'totalSellers',
            'totalMenus',
            'ordersInPeriod',
            'revenueInPeriod',
            'todayOrders',
            'todayRevenue',
            'pendingTopUps',
            'pendingWithdrawals',
            'pendingReports',
            'topStores',
            'topProducts',
            'recentOrders',
            'ordersByStatus',
            'monthlyRevenue',
            'period'
        ));
    }

    private function getTopStores($days)
    {
        $startDate = now()->subDays($days);
        
        // Get sellers with their revenue
        $stores = User::where('role', 'admin')
            ->with('sellerProfile')
            ->get()
            ->map(function ($seller) use ($startDate) {
                $sellerMenuIds = Menu::where('user_id', $seller->id)->pluck('id');
                $orderIds = OrderItem::whereIn('menu_id', $sellerMenuIds)->pluck('order_id')->unique();
                
                $revenue = Order::whereIn('id', $orderIds)
                    ->where('created_at', '>=', $startDate)
                    ->where(function ($query) {
                        $query->where(function ($q) {
                            $q->where('is_completed', true)
                              ->orWhere('is_auto_confirmed', true);
                        })->where('is_confirmed_by_seller', true)
                           ->where('is_confirmed_by_user', true);
                    })
                    ->sum('total_amount');
                    
                $orderCount = Order::whereIn('id', $orderIds)
                    ->where('created_at', '>=', $startDate)
                    ->where(function ($query) {
                        $query->where(function ($q) {
                            $q->where('is_completed', true)
                              ->orWhere('is_auto_confirmed', true);
                        })->where('is_confirmed_by_seller', true)
                           ->where('is_confirmed_by_user', true);
                    })
                    ->count();
                    
                return [
                    'seller' => $seller,
                    'store_name' => $seller->sellerProfile?->store_name ?? $seller->name,
                    'revenue' => $revenue,
                    'order_count' => $orderCount,
                ];
            })
            ->sortByDesc('revenue')
            ->take(10)
            ->values();
            
        return $stores;
    }

    private function getTopProducts($days)
    {
        $startDate = now()->subDays($days);
        
        $products = OrderItem::select(
                'menu_id',
                DB::raw('SUM(order_items.qty) as total_sold'),
                DB::raw('SUM(order_items.subtotal) as total_revenue')
            )
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('menus', 'order_items.menu_id', '=', 'menus.id')
            ->where('orders.created_at', '>=', $startDate)
            ->where(function ($query) {
                $query->where(function ($q) {
                    $q->where('orders.is_completed', true)
                      ->orWhere('orders.is_auto_confirmed', true);
                })->where('orders.is_confirmed_by_seller', true)
                   ->where('orders.is_confirmed_by_user', true);
            })
            ->groupBy('menu_id', 'menus.id', 'menus.name', 'menus.photo_path')
            ->orderBy('total_sold', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($item) {
                $menu = Menu::find($item->menu_id);
                return [
                    'menu' => $menu,
                    'total_sold' => $item->total_sold,
                    'total_revenue' => $item->total_revenue,
                ];
            });
            
        return $products;
    }

    private function getMonthlyRevenue($months)
    {
        $result = [];
        for ($i = $months - 1; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $startOfMonth = $month->copy()->startOfMonth();
            $endOfMonth = $month->copy()->endOfMonth();
            
            $revenue = Order::whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->where(function ($query) {
                    $query->where(function ($q) {
                        $q->where('is_completed', true)
                          ->orWhere('is_auto_confirmed', true);
                    })->where('is_confirmed_by_seller', true)
                       ->where('is_confirmed_by_user', true);
                })
                ->sum('total_amount');
                
            $result[] = [
                'month' => $month->format('M Y'),
                'revenue' => $revenue,
            ];
        }
        return $result;
    }
}

