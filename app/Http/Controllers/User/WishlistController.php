<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    /**
     * Display the user's wishlist.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get all wishlist items with menu data
        $wishlists = Wishlist::where('user_id', $user->id)
            ->with(['menu' => function($query) {
                $query->with('user.sellerProfile');
            }])
            ->latest()
            ->get();
        
        // Filter out menus that no longer exist or are unavailable
        $wishlistMenus = $wishlists->filter(function($item) {
            return $item->menu !== null;
        })->map(function($item) {
            return $item->menu;
        });

        return view('user.wishlist.index', compact('wishlistMenus'));
    }

    /**
     * Add a menu to the user's wishlist.
     */
    public function store(Request $request, Menu $menu)
    {
        $user = Auth::user();

        // Check if already in wishlist
        $existingWishlist = Wishlist::where('user_id', $user->id)
            ->where('menu_id', $menu->id)
            ->first();

        if ($existingWishlist) {
            return response()->json([
                'success' => false,
                'message' => 'Menu sudah ada di wishlist'
            ]);
        }

        // Add to wishlist
        Wishlist::create([
            'user_id' => $user->id,
            'menu_id' => $menu->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Menu ditambahkan ke wishlist'
        ]);
    }

    /**
     * Remove a menu from the user's wishlist.
     */
    public function destroy(Menu $menu)
    {
        $user = Auth::user();

        Wishlist::where('user_id', $user->id)
            ->where('menu_id', $menu->id)
            ->delete();

        return response()->json([
            'success' => true,
            'message' => 'Menu dihapus dari wishlist'
        ]);
    }

    /**
     * Toggle wishlist status (add if not exists, remove if exists).
     */
    public function toggle(Request $request, Menu $menu)
    {
        $user = Auth::user();

        $existingWishlist = Wishlist::where('user_id', $user->id)
            ->where('menu_id', $menu->id)
            ->first();

        if ($existingWishlist) {
            $existingWishlist->delete();
            return response()->json([
                'success' => true,
                'inWishlist' => false,
                'message' => 'Menu dihapus dari wishlist'
            ]);
        } else {
            Wishlist::create([
                'user_id' => $user->id,
                'menu_id' => $menu->id,
            ]);
            return response()->json([
                'success' => true,
                'inWishlist' => true,
                'message' => 'Menu ditambahkan ke wishlist'
            ]);
        }
    }

    /**
     * Check if a menu is in user's wishlist (API).
     */
    public function check(Request $request, Menu $menu)
    {
        $user = Auth::user();
        $inWishlist = $user->hasInWishlist($menu->id);

        return response()->json([
            'success' => true,
            'inWishlist' => $inWishlist
        ]);
    }
}

