<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Rating;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RatingController extends Controller
{
    /**
     * Store ratings for an order (can rate multiple items at once).
     */
    public function store(Request $request, Order $order)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.menu_id' => 'required|exists:menus,id',
            'items.*.rating' => 'required|integer|between:1,5',
            'items.*.comment' => 'nullable|string|max:500',
        ]);

        // Verify order belongs to user
        if ($order->user_id !== Auth::id()) {
            return back()->with('error', 'Pesanan tidak ditemukan.');
        }

        // Verify order is completed
        if (!$order->is_completed && !$order->is_auto_confirmed) {
            return back()->with('error', 'Hanya pesanan yang sudah selesai dapat dinilai.');
        }

        // Process each rating
        foreach ($request->items as $item) {
            $menuId = $item['menu_id'];
            $ratingValue = $item['rating'];
            $comment = $item['comment'] ?? null;

            // Verify menu is part of this order
            $orderItem = $order->orderItems()->where('menu_id', $menuId)->first();
            if (!$orderItem) {
                continue;
            }

            // Check if already rated
            $existingRating = Rating::where('order_id', $order->id)
                ->where('menu_id', $menuId)
                ->where('user_id', Auth::id())
                ->first();

            if ($existingRating) {
                continue;
            }

            // Create rating
            Rating::create([
                'order_id' => $order->id,
                'menu_id' => $menuId,
                'user_id' => Auth::id(),
                'rating' => $ratingValue,
                'comment' => $comment,
            ]);

            // Update menu average rating
            $this->updateMenuRating($menuId);
        }

        return back()->with('success', 'Terima kasih atas rating Anda!');
    }

    /**
     * Update menu's average rating.
     */
    private function updateMenuRating($menuId)
    {
        $menu = Menu::find($menuId);
        if (!$menu) return;

        $ratings = Rating::where('menu_id', $menuId)->get();
        
        if ($ratings->isEmpty()) {
            $menu->update([
                'average_rating' => 0,
                'rating_count' => 0,
            ]);
            return;
        }

        $average = $ratings->avg('rating');
        $menu->update([
            'average_rating' => round($average, 2),
            'rating_count' => $ratings->count(),
        ]);
    }
}
