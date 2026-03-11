<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\SellerProfile;
use Illuminate\Http\Request;

class SellerController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search', '');
        
        $query = User::where('role', 'admin')->with('sellerProfile');
        
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhereHas('sellerProfile', function ($q) use ($search) {
                      $q->where('store_name', 'like', "%{$search}%");
                  });
            });
        }
        
        $sellers = $query->orderBy('created_at', 'desc')->paginate(20);
        
        // Get stats for each seller
        $sellers->getCollection()->transform(function ($seller) {
            $sellerMenuIds = Menu::where('user_id', $seller->id)->pluck('id');
            $sellerOrderIds = OrderItem::whereIn('menu_id', $sellerMenuIds)->pluck('order_id')->unique();
            
            $seller->total_menus = Menu::where('user_id', $seller->id)->count();
            $seller->total_orders = Order::whereIn('id', $sellerOrderIds)->count();
            $seller->total_revenue = Order::whereIn('id', $sellerOrderIds)
                ->where(function ($query) {
                    $query->where(function ($q) {
                        $q->where('is_completed', true)
                          ->orWhere('is_auto_confirmed', true);
                    })->where('is_confirmed_by_seller', true)
                       ->where('is_confirmed_by_user', true);
                })
                ->sum('total_amount');
                
            return $seller;
        });
        
        return view('superadmin.sellers.index', compact('sellers', 'search'));
    }

    public function show(User $seller)
    {
        if ($seller->role !== 'admin') {
            return redirect()->route('superadmin.sellers.index')->with('error', 'User bukan seller.');
        }
        
        $seller->load('sellerProfile');
        
        $sellerMenuIds = Menu::where('user_id', $seller->id)->pluck('id');
        $sellerOrderIds = OrderItem::whereIn('menu_id', $sellerMenuIds)->pluck('order_id')->unique();
        
        $stats = [
            'total_menus' => Menu::where('user_id', $seller->id)->count(),
            'total_orders' => Order::whereIn('id', $sellerOrderIds)->count(),
            'total_revenue' => Order::whereIn('id', $sellerOrderIds)
                ->where(function ($query) {
                    $query->where(function ($q) {
                        $q->where('is_completed', true)
                          ->orWhere('is_auto_confirmed', true);
                    })->where('is_confirmed_by_seller', true)
                       ->where('is_confirmed_by_user', true);
                })
                ->sum('total_amount'),
            'today_orders' => Order::whereIn('id', $sellerOrderIds)
                ->whereDate('created_at', today())
                ->count(),
            'today_revenue' => Order::whereIn('id', $sellerOrderIds)
                ->whereDate('created_at', today())
                ->where(function ($query) {
                    $query->where(function ($q) {
                        $q->where('is_completed', true)
                          ->orWhere('is_auto_confirmed', true);
                    })->where('is_confirmed_by_seller', true)
                       ->where('is_confirmed_by_user', true);
                })
                ->sum('total_amount'),
        ];
        
        // Recent orders
        $recentOrders = Order::with(['user', 'orderItems.menu'])
            ->whereIn('id', $sellerOrderIds)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
            
        return view('superadmin.sellers.show', compact('seller', 'stats', 'recentOrders'));
    }
}

