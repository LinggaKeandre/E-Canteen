<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Menu;
use Illuminate\Http\Response;

class SellerMenuController extends Controller
{
    public function getMenus(User $seller)
    {
        // Verify seller has admin role
        if ($seller->role !== 'admin') {
            return response()->json(['error' => 'Not a seller'], 404);
        }

        // Get current day of week
        $today = now()->dayOfWeek;
        $dayMap = [0 => 7, 1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5, 6 => 6];
        $currentDay = $dayMap[$today];
        
        // Filter menus based on daily availability
        $menus = $seller->menus()
            ->where('status', 'tersedia')
            ->where(function($query) use ($currentDay) {
                $query->where('is_daily', false)
                      ->orWhereNull('is_daily')
                      ->orWhereJsonContains('available_days', (string) $currentDay);
            })
            ->select('id', 'name', 'price', 'category', 'photo_path', 'average_rating', 'rating_count', 'status', 'has_variants', 'has_addons')
            ->get()
            ->filter(function($menu) use ($currentDay) {
                return $menu->isAvailableToday();
            })
            ->values();

        return response()->json([
            'success' => true,
            'menus' => $menus
        ]);
    }
    
    public function getMenuDetails(Menu $menu)
    {
        // Load variants and addons
        $menu->load('variants', 'addons');
        
        return response()->json([
            'success' => true,
            'menu' => [
                'id' => $menu->id,
                'name' => $menu->name,
                'price' => $menu->price,
                'has_variants' => $menu->has_variants,
                'has_addons' => $menu->has_addons,
                'variants' => $menu->variants,
                'addons' => $menu->addons,
            ]
        ]);
    }
}
