<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Rating;
use Illuminate\Http\Request;

class MenuRatingController extends Controller
{
    /**
     * Display list of menus with their ratings.
     */
    public function index()
    {
        $menus = Menu::with(['ratings.user'])
            ->where('status', 'tersedia')
            ->orWhere('status', 'habis')
            ->get()
            ->map(function ($menu) {
                $menu->total_ratings = $menu->ratings->count();
                $menu->avg_rating = $menu->ratings->avg('rating') ?? 0;
                return $menu;
            });
        
        return view('admin.ratings.index', compact('menus'));
    }

    /**
     * Show details of a specific menu's ratings.
     */
    public function show(Menu $menu)
    {
        $menu->load(['ratings.user', 'orderItems']);
        
        return view('admin.ratings.show', compact('menu'));
    }
}
