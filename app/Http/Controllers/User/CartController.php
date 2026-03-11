<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\BalanceTransaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    public function addGuest(Request $request, Menu $menu)
    {
        // Redirect to login if not authenticated
        return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu untuk memesan.');
    }
    
    public function index()
    {
        $cart = session()->get('cart', []);
        
        // Refresh cart with latest prices from database
        $cart = $this->refreshCartPrices($cart);
        
        // Get daily spending limit info
        $user = Auth::user();
        $dailyLimitEnabled = $user->isDailySpendingLimitEnabled();
        $todaySpending = $dailyLimitEnabled ? $user->getTodaySpending() : 0;
        $dailyLimit = $dailyLimitEnabled ? $user->daily_spending_limit : 0;
        $remainingDailyLimit = $dailyLimitEnabled ? $user->getRemainingDailyLimit() : null;
        
        // Calculate cart total
        $cartTotal = 0;
        foreach ($cart as $item) {
            $cartTotal += $item['price'] * $item['qty'];
        }
        
        // Check if would exceed limit
        $wouldExceedLimit = $dailyLimitEnabled && $user->wouldExceedDailyLimit($cartTotal);
        
        return view('user.cart.index', compact('cart', 'dailyLimitEnabled', 'todaySpending', 'dailyLimit', 'remainingDailyLimit', 'wouldExceedLimit', 'cartTotal'));
    }

    /**
     * Refresh cart prices from database (in case menu prices were updated)
     */
    private function refreshCartPrices($cart)
    {
        if (empty($cart)) {
            return $cart;
        }
        
        $menuIds = array_keys(array_column($cart, 'menu_id', 'menu_id') ?: array_flip(array_keys($cart)));
        $menuIds = array_unique(array_merge($menuIds, array_map(function($item) {
            return is_array($item) && isset($item['menu_id']) ? $item['menu_id'] : null;
        }, $cart)));
        $menuIds = array_filter($menuIds);
        
        if (empty($menuIds)) {
            return $cart;
        }
        
        $menus = Menu::whereIn('id', $menuIds)->get()->keyBy('id');
        
        foreach ($cart as $cartKey => $item) {
            $menuId = is_array($item) && isset($item['menu_id']) ? $item['menu_id'] : $cartKey;
            
            if (isset($menus[$menuId])) {
                $menu = $menus[$menuId];
                // Update cart item with latest data from database
                $cart[$cartKey]['price'] = $menu->price;
                $cart[$cartKey]['name'] = $menu->name;
                $cart[$cartKey]['category'] = $menu->category;
                $cart[$cartKey]['status'] = $menu->status;
                $cart[$cartKey]['photo_path'] = $menu->photo_path;
                // Check if menu is available today (for daily menus)
                $cart[$cartKey]['is_available_today'] = $menu->isAvailableToday();
            }
        }
        
        // Save updated cart to session
        session()->put('cart', $cart);
        
        return $cart;
    }

    public function add(Request $request, Menu $menu)
    {
        if ($menu->status !== 'tersedia') {
            return back()->with('error', 'Menu tidak tersedia.');
        }

        // Check if menu is available today (for daily menus)
        if (!$menu->isAvailableToday()) {
            return back()->with('error', 'Menu ini hanya tersedia pada hari tertentu.');
        }

        // Validate variant is required if menu has variants
        if ($menu->has_variants && !$request->has('variant_id')) {
            return back()->with('error', 'Silakan pilih varian terlebih dahulu.');
        }

        $qty = $request->get('qty', 1);
        
        // Get variant price adjustment
        $variantPriceAdjustment = 0;
        $variantName = null;
        if ($menu->has_variants && $request->variant_id) {
            $variant = $menu->variants->find($request->variant_id);
            if ($variant) {
                $variantPriceAdjustment = $variant->price_adjustment;
                $variantName = $variant->name;
            }
        }
        
        // Get selected addons
        $selectedAddons = [];
        $addonsPrice = 0;
        if ($menu->has_addons && $request->addons) {
            $addonIds = is_array($request->addons) ? $request->addons : [];
            $menuAddons = $menu->addons->whereIn('id', $addonIds);
            foreach ($menuAddons as $addon) {
                $selectedAddons[] = [
                    'id' => $addon->id,
                    'name' => $addon->name,
                    'price' => $addon->price,
                ];
                $addonsPrice += $addon->price;
            }
        }
        
        $cart = session()->get('cart', []);
        
        // Create unique cart key based on menu, variant, and addons
        $cartKey = $menu->id;
        if ($variantName) {
            $cartKey .= '_variant_' . $variantName;
        }
        if (!empty($selectedAddons)) {
            $addonIds = array_column($selectedAddons, 'id');
            sort($addonIds);
            $cartKey .= '_addons_' . implode('_', $addonIds);
        }
        
        $itemPrice = $menu->price + $variantPriceAdjustment + $addonsPrice;
        
        if (isset($cart[$cartKey])) {
            $cart[$cartKey]['qty'] += $qty;
        } else {
            $cart[$cartKey] = [
                'menu_id' => $menu->id,
                'name' => $menu->name,
                'category' => $menu->category,
                'price' => $itemPrice,
                'base_price' => $menu->price,
                'qty' => $qty,
                'status' => $menu->status,
                'photo_path' => $menu->photo_path,
                'is_available_today' => $menu->isAvailableToday(),
                'variant_name' => $variantName,
                'variant_price_adjustment' => $variantPriceAdjustment,
                'addons' => $selectedAddons,
                'notes' => $request->notes ?? null,
            ];
        }
        
        session()->put('cart', $cart);
        
        return back()->with('success', 'Menu ditambahkan ke keranjang.');
    }

    public function update(Request $request, Menu $menu)
    {
        $qty = $request->get('qty', 1);
        
        $cart = session()->get('cart', []);
        
        if ($qty <= 0) {
            unset($cart[$menu->id]);
        } else {
            if (isset($cart[$menu->id])) {
                $cart[$menu->id]['qty'] = $qty;
            }
        }
        
        session()->put('cart', $cart);
        
        return back();
    }

    public function remove(Menu $menu)
    {
        $cart = session()->get('cart', []);
        unset($cart[$menu->id]);
        session()->put('cart', $cart);
        
        return back()->with('success', 'Menu dihapus dari keranjang.');
    }

    public function checkout(Request $request)
    {
        $request->validate([
            'pickup_slot' => 'required|in:istirahat_1,istirahat_2',
            'classroom' => 'required|string|max:50',
        ]);

        $cart = session()->get('cart', []);
        
        if (empty($cart)) {
            return back()->with('error', 'Keranjang kosong.');
        }

        // Refresh prices before checkout
        $cart = $this->refreshCartPrices($cart);
        
        // Check if there are any unavailable items
        $unavailableItems = [];
        foreach ($cart as $key => $item) {
            $isAvailable = isset($item['is_available_today']) ? $item['is_available_today'] : true;
            if (!$isAvailable) {
                $unavailableItems[] = $item['name'] ?? 'Menu';
            }
        }
        
        if (!empty($unavailableItems)) {
            return back()->with('error', 'Ada menu yang tidak tersedia hari ini: ' . implode(', ', $unavailableItems) . '. Silakan hapus menu tersebut dari keranjang.');
        }
        
        $user = Auth::user();
        
        // Group cart items by seller (store)
        $cartBySeller = $this->groupCartBySeller($cart);
        
        // Calculate total amount across all sellers
        $totalAmount = 0;
        foreach ($cartBySeller as $sellerId => $items) {
            foreach ($items as $item) {
                $totalAmount += $item['price'] * $item['qty'];
            }
        }

        // Check balance - user must have enough to pay
        if ($user->balance < $totalAmount) {
            return back()->with('error', 'Saldo tidak cukup. Saldo Anda: Rp ' . number_format($user->balance, 0, ',', '.'));
        }

        // Check daily spending limit
        if ($user->wouldExceedDailyLimit($totalAmount)) {
            $remainingLimit = $user->getRemainingDailyLimit();
            return back()->with('error', 'Melebihi batas pengeluaran harian! Sisa batas harian Anda: Rp ' . number_format($remainingLimit, 0, ',', '.'));
        }

        // Create orders with DB transaction - ESCROW payment
        // Money is held by system, not transferred to seller yet
        try {
            DB::beginTransaction();
            
            // Deduct balance - money goes to "escrow" (system holds it)
            $balanceBefore = $user->balance;
            $newBalance = $balanceBefore - $totalAmount;
            DB::table('users')
                ->where('id', $user->id)
                ->update(['balance' => $newBalance]);
            
            // Create separate order for each seller
            $createdOrders = [];
            foreach ($cartBySeller as $sellerId => $items) {
                $sellerTotalAmount = 0;
                foreach ($items as $item) {
                    $sellerTotalAmount += $item['price'] * $item['qty'];
                }
                
                // Create order with status "pending"
                $order = Order::create([
                    'user_id' => $user->id,
                    'pickup_slot' => $request->pickup_slot,
                    'classroom' => $request->classroom,
                    'status' => 'pending',
                    'total_amount' => $sellerTotalAmount,
                    'is_paid' => true,
                    'is_confirmed_by_user' => false,
                    'is_confirmed_by_seller' => false,
                    'is_completed' => false,
                    'seller_id' => $sellerId, // Track which seller this order belongs to
                ]);
                
                // Create order items for this seller
                foreach ($items as $item) {
                    $addonsJson = null;
                    if (!empty($item['addons'])) {
                        $addonsJson = json_encode($item['addons']);
                    }
                    
                    OrderItem::create([
                        'order_id' => $order->id,
                        'menu_id' => $item['menu_id'],
                        'qty' => $item['qty'],
                        'price' => $item['price'],
                        'subtotal' => $item['price'] * $item['qty'],
                        'variant_name' => $item['variant_name'] ?? null,
                        'addons_json' => $addonsJson,
                        'notes' => $item['notes'] ?? null,
                    ]);
                }
                
                $createdOrders[] = $order;
            }
            
            // Create ONE balance transaction for the total - debit from user
            BalanceTransaction::create([
                'user_id' => $user->id,
                'order_id' => $createdOrders[0]->id, // Link to first order
                'type' => 'debit',
                'amount' => $totalAmount,
                'balance_before' => $balanceBefore,
                'balance_after' => $newBalance,
                'notes' => 'Pembayaran pesanan dari ' . count($createdOrders) . ' toko (Dana di Escrow)',
            ]);
            
            DB::commit();
            
            // Clear cart
            session()->forget('cart');
            
            return redirect()->route('user.orders.index')->with('success', 'Pesanan berhasil dibuat! (' . count($createdOrders) . ' pesanan dari toko berbeda) Menunggu konfirmasi dari masing-masing penjual.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan. Silakan coba lagi. Error: ' . $e->getMessage());
        }
    }
    
    /**
     * Group cart items by seller (store)
     */
    private function groupCartBySeller($cart)
    {
        $cartBySeller = [];
        
        foreach ($cart as $menuId => $item) {
            // Get the menu to find its seller (user_id)
            $menu = Menu::find($menuId);
            if ($menu) {
                $sellerId = $menu->user_id;
                if (!isset($cartBySeller[$sellerId])) {
                    $cartBySeller[$sellerId] = [];
                }
                $cartBySeller[$sellerId][] = $item;
            }
        }
        
        return $cartBySeller;
    }

    // User requests to cancel their order
    public function requestCancel(Request $request, Order $order)
    {
        $user = Auth::user();
        
        // Verify ownership
        if ($order->user_id !== $user->id) {
            return back()->with('error', 'Pesanan tidak ditemukan.');
        }
        
        // CRITICAL: Refresh order from database to get latest status
        // This prevents race condition where seller has already accepted/changed status
        $order = Order::find($order->id);
        
        if (!$order) {
            return back()->with('error', 'Pesanan tidak ditemukan.');
        }
        
        // Only allow cancellation when still pending
        if (!$order->canUserRequestCancel() || $order->status !== 'pending') {
            return back()->with('error', 'Pesanan tidak dapat dibatalkan pada status ini.');
        }
        
        try {
            DB::beginTransaction();
            
            // Since only pending is allowed, we simply mark cancel accepted and refund
            $order->update([
                'cancel_request' => 'accepted',
                'cancel_requested_at' => now(),
                'cancel_responded_at' => now(),
                'is_completed' => true,
                'completed_at' => now(),
            ]);
            
            // refund
            $user = Auth::user();
            $balanceBefore = $user->balance;
            $user->balance += $order->total_amount;
            $user->save();

            BalanceTransaction::create([
                'user_id' => $user->id,
                'order_id' => $order->id,
                'type' => 'credit',
                'amount' => $order->total_amount,
                'balance_before' => $balanceBefore,
                'balance_after' => $user->balance,
                'notes' => 'Pembatalan pesanan #' . $order->id,
            ]);

            DB::commit();
            return back()->with('success', 'Pesanan berhasil dibatalkan. Menunggu dana dikembalikan ke saldo Anda.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan. Silakan coba lagi.');
        }
    }

    // User confirms they received the order - THIS TRIGGERS AUTO TRANSFER TO SELLER
    public function confirmReceipt(Order $order)
    {
        $user = Auth::user();
        
        // Verify ownership
        if ($order->user_id !== $user->id) {
            return back()->with('error', 'Pesanan tidak ditemukan.');
        }
        
        // Can only confirm if seller has confirmed handover first
        if (!$order->canUserConfirm()) {
            return back()->with('error', 'Tidak dapat mengkonfirmasi. Penjual harus mengkonfirmasi serah terima terlebih dahulu.');
        }
        
        // Get the correct seller from the order (the seller who owns this order)
        $seller = User::find($order->seller_id);
        
        if (!$seller) {
            return back()->with('error', 'Akun seller tidak ditemukan.');
        }
        
        try {
            DB::beginTransaction();
            
            // Update order - user confirms receipt
            $order->update(['is_confirmed_by_user' => true]);
            
            // AUTO TRANSFER to the correct seller immediately after buyer confirms
            $sellerBalanceBefore = $seller->balance;
            $sellerNewBalance = $sellerBalanceBefore + $order->total_amount;
            
            DB::table('users')
                ->where('id', $seller->id)
                ->update(['balance' => $sellerNewBalance]);
            
            // Mark order as completed
            $order->update([
                'is_completed' => true,
                'completed_at' => now(),
            ]);
            
            // Create credit transaction for the correct seller
            BalanceTransaction::create([
                'user_id' => $seller->id,
                'order_id' => $order->id,
                'type' => 'credit',
                'amount' => $order->total_amount,
                'balance_before' => $sellerBalanceBefore,
                'balance_after' => $sellerNewBalance,
                'notes' => 'Penerimaan dana dari pesanan #' . $order->id . ' (Escrow Release - Buyer Confirmed)',
            ]);
            
            DB::commit();
            
            return back()->with('success', 'Terima kasih! Pesanan selesai. Dana telah ditransfer ke rekening penjual.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan. Silakan coba lagi.');
        }
    }

    // Auto-confirm and transfer after 6 hours (called by scheduled task or manually)
    public function autoConfirmOrder(Order $order)
    {
        if (!$order->canAutoConfirm()) {
            return false;
        }
        
        // Get the correct seller from the order
        $seller = User::find($order->seller_id);
        
        if (!$seller) {
            return false;
        }
        
        try {
            DB::beginTransaction();
            
            // Mark as auto-confirmed
            $order->update([
                'is_confirmed_by_user' => true,
                'is_completed' => true,
                'completed_at' => now(),
                'is_auto_confirmed' => true,
            ]);
            
            // Transfer to the correct seller
            $sellerBalanceBefore = $seller->balance;
            $sellerNewBalance = $sellerBalanceBefore + $order->total_amount;
            
            DB::table('users')
                ->where('id', $seller->id)
                ->update(['balance' => $sellerNewBalance]);
            
            // Create credit transaction for the correct seller
            BalanceTransaction::create([
                'user_id' => $seller->id,
                'order_id' => $order->id,
                'type' => 'credit',
                'amount' => $order->total_amount,
                'balance_before' => $sellerBalanceBefore,
                'balance_after' => $sellerNewBalance,
                'notes' => 'Penerimaan dana dari pesanan #' . $order->id . ' (Auto-Confirm after 6 hours)',
            ]);
            
            DB::commit();
            
            return true;
            
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }

    // Check if order can be completed (both confirmed)
    public function canComplete(Order $order)
    {
        return $order->is_confirmed_by_user && $order->is_confirmed_by_seller && !$order->is_completed;
    }
}
