<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Http\Request;

class MenuBrowseController extends Controller
{
    public function index(Request $request)
    {
        // Get current day of week
        $today = now()->dayOfWeek;
        $dayMap = [0 => 7, 1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5, 6 => 6];
        $currentDay = $dayMap[$today];
        
        $query = Menu::where('status', 'tersedia')
            ->where(function($q) use ($currentDay) {
                $q->where('is_daily', false)
                  ->orWhereNull('is_daily')
                  ->orWhereJsonContains('available_days', (string) $currentDay);
            });
        
        // Filter by category if selected
        if ($request->has('category') && $request->category) {
            $query->where('category', $request->category);
        }
        
        $menus = $query->latest()->get()
            ->filter(function($menu) {
                return $menu->isAvailableToday();
            })
            ->values();
            
        return view('user.menus.index', compact('menus'));
    }

    public function show(Menu $menu)
    {
        if ($menu->status !== 'tersedia') {
            return redirect()->route('user.menus.index')->with('error', 'Menu tidak tersedia.');
        }
        return view('user.menus.show', compact('menu'));
    }
}
