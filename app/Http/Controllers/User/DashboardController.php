<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Order;
use App\Models\User;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $recentOrders = Order::where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get();
        
        // Get current day of week (1=Monday, 7=Sunday in Laravel)
        $today = now()->dayOfWeek;
        // Convert Laravel day to our format (1=Monday, ..., 5=Friday)
        $dayMap = [0 => 7, 1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5, 6 => 6];
        $currentDay = $dayMap[$today];
        
        // Get sellers with available menus (including daily menus based on current day)
        $sellers = User::whereHas('menus', function($query) use ($currentDay) {
            $query->where('status', 'tersedia')
                  ->where(function($q) use ($currentDay) {
                      $q->where('is_daily', false)
                        ->orWhereJsonContains('available_days', (string) $currentDay);
                  });
        })
        ->where('role', 'admin')
        ->with(['menus' => function($query) use ($currentDay) {
            $query->where('status', 'tersedia')
                  ->where(function($q) use ($currentDay) {
                      $q->where('is_daily', false)
                        ->orWhereJsonContains('available_days', (string) $currentDay);
                  });
        }, 'sellerProfile'])
        ->latest()
        ->get()
        ->map(function($seller) use ($currentDay) {
            // Filter menus to only show available daily menus for today
            $seller->menus = $seller->menus->filter(function($menu) use ($currentDay) {
                return $menu->isAvailableToday();
            });
            
            // Count total quantity of items ordered from this seller today
            $todayItemCount = OrderItem::whereHas('order', function($query) use ($seller) {
                $query->where('seller_id', $seller->id)
                      ->whereDate('created_at', today());
            })
            ->sum('qty');
            
            // Add order_count to seller
            $seller->today_order_count = $todayItemCount;
            return $seller;
        });

        // Get daily menus available today
        $dailyMenus = Menu::where('status', 'tersedia')
            ->where('is_daily', true)
            ->whereJsonContains('available_days', (string) $currentDay)
            ->with('user.sellerProfile')
            ->latest()
            ->get()
            ->filter(function($menu) {
                return $menu->isAvailableToday();
            });
        
        // Get top products today (from completed/paid orders created today)
        // Only include menus that are available today (non-daily OR daily menus available today)
        $topProductsToday = OrderItem::select(
            'menu_id',
            DB::raw('SUM(qty) as total_sold'),
            DB::raw('MAX(menu_id) as menu_id_max')
        )
        ->whereHas('order', function($query) {
            $query->where('is_paid', true)
                  ->where(function($q) {
                      $q->where('is_completed', true)
                        ->orWhere('is_auto_confirmed', true);
                  })
                  ->whereDate('created_at', today());
        })
        ->groupBy('menu_id')
        ->orderByDesc('total_sold')
        ->take(20) // Get more initially for filtering
        ->get()
        ->map(function($item) {
            return Menu::with('user.sellerProfile')
                ->where('id', $item->menu_id)
                ->first();
        })
        ->filter(function($menu) {
            // Only include menus that are available today
            return $menu && $menu->isAvailableToday();
        })
        ->take(6)
        ->values();
        
        // Get all available menus from all sellers (for "All Products" section) - exclude daily menus
        $allMenus = Menu::where('status', 'tersedia')
            ->where(function($query) {
                $query->where('is_daily', false)
                      ->orWhereNull('is_daily');
            })
            ->with('user.sellerProfile')
            ->latest()
            ->take(20)
            ->get();
        
        // Get day name for display
        $dayNames = [1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu', 7 => 'Minggu'];
        $todayName = $dayNames[$currentDay] ?? 'Hari ini';
        
        // Get user's wishlist menu IDs
        $wishlistMenuIds = $user->getWishlistMenuIds();
        
        return view('user.dashboard', compact('recentOrders', 'sellers', 'topProductsToday', 'allMenus', 'dailyMenus', 'todayName', 'wishlistMenuIds'));
    }
}
