<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterUserController;
use App\Http\Controllers\Auth\RegisterSellerController;
use App\Http\Controllers\User\MenuBrowseController;
use App\Http\Controllers\User\CartController;
use App\Http\Controllers\User\OrderHistoryController;
use App\Http\Controllers\User\BalanceController;
use App\Http\Controllers\User\RatingController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\OrderQueueController;
use App\Http\Controllers\Admin\ReportController;
use App\Models\Menu;
use App\Models\User;

// Landing Page - Unified for logged in and guest users
Route::get('/', function () {
    // Get current day of week
    $today = now()->dayOfWeek;
    $dayMap = [0 => 7, 1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5, 6 => 6];
    $currentDay = $dayMap[$today];
    
    // Get day name for display
    $dayNames = [1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu', 7 => 'Minggu'];
    $todayName = $dayNames[$currentDay] ?? 'Hari ini';
    
    // Get sellers with available menus (including daily menus based on current day)
    $sellers = User::whereHas('menus', function($query) use ($currentDay) {
        $query->where('status', 'tersedia')
              ->where(function($q) use ($currentDay) {
                  $q->where('is_daily', false)
                    ->orWhereNull('is_daily')
                    ->orWhereRaw('JSON_CONTAINS(available_days, ?)', [json_encode($currentDay)]);
              });
    })
    ->where('role', 'admin')
    ->with(['menus' => function($query) use ($currentDay) {
        $query->where('status', 'tersedia')
              ->where(function($q) use ($currentDay) {
                  $q->where('is_daily', false)
                    ->orWhereNull('is_daily')
                    ->orWhereRaw('JSON_CONTAINS(available_days, ?)', [json_encode($currentDay)]);
              });
    }, 'sellerProfile'])
    ->latest()
    ->get()
    ->map(function($seller) use ($currentDay) {
        // Filter menus to only show available daily menus for today
        $seller->menus = $seller->menus->filter(function($menu) use ($currentDay) {
            return $menu->isAvailableToday();
        });
        return $seller;
    });

    // Get daily menus available today
    $dailyMenus = \App\Models\Menu::where('status', 'tersedia')
        ->where('is_daily', true)
        ->whereJsonContains('available_days', (string) $currentDay)
        ->with('user.sellerProfile')
        ->latest()
        ->get()
        ->filter(function($menu) {
            return $menu->isAvailableToday();
        });
    
    // Get top products today (from completed/paid orders created today)
    $topProductsToday = \App\Models\OrderItem::select(
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
    ->take(20)
    ->get()
    ->map(function($item) {
        return \App\Models\Menu::with('user.sellerProfile')
            ->where('id', $item->menu_id)
            ->first();
    })
    ->filter(function($menu) {
        return $menu && $menu->isAvailableToday();
    })
    ->take(6)
    ->values();
    
    // Get all available menus from all sellers (for "All Products" section) - exclude daily menus
    $allMenus = \App\Models\Menu::where('status', 'tersedia')
        ->where(function($query) {
            $query->where('is_daily', false)
                  ->orWhereNull('is_daily');
        })
        ->with('user.sellerProfile')
        ->latest()
        ->take(20)
        ->get();
    
    return view('welcome', compact('sellers', 'topProductsToday', 'allMenus', 'dailyMenus', 'todayName'));
})->name('home');

// Auth Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/register-user', [RegisterUserController::class, 'showRegistrationForm'])->name('register.user');
Route::post('/register-user', [RegisterUserController::class, 'register']);

Route::get('/register-seller', [RegisterSellerController::class, 'showRegistrationForm'])->name('register.seller');
Route::post('/register-seller', [RegisterSellerController::class, 'register']);

// Hidden route to create superadmin (secret slug)
Route::get('/x7K9mP2qR5tY8vW3zA', function () {
    return view('auth.create-superadmin');
})->name('create.superadmin.form');

Route::post('/x7K9mP2qR5tY8vW3zA', function (\Illuminate\Http\Request $request) {
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|string|min:8|confirmed',
        'secret_key' => 'required|string',
    ]);
    
    // Secret key check
    $secretKey = 'ecanteen_superadmin_2024';
    
    if ($request->secret_key !== $secretKey) {
        return back()->with('error', 'Secret key yang Anda masukkan salah!')->withInput();
    }
    
    $password = $request->password;
    
    $user = \App\Models\User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => bcrypt($password),
        'role' => 'superadmin',
        'balance' => 0,
    ]);
    
    return redirect()->route('create.superadmin.form')->with([
        'success' => 'Superadmin berhasil dibuat!',
        'email' => $request->email,
        'password' => $password,
    ]);
})->name('create.superadmin');

// User Routes
Route::middleware(['auth', 'role:user'])->group(function () {
    Route::get('/user/dashboard', [App\Http\Controllers\User\DashboardController::class, 'index'])->name('user.dashboard');
    
    Route::get('/menus', [MenuBrowseController::class, 'index'])->name('user.menus.index');
    Route::get('/menus/{menu}', [MenuBrowseController::class, 'show'])->name('user.menus.show');
    
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/{menu}/add', [CartController::class, 'add'])->name('cart.add');
    Route::post('/cart/{menu}/update', [CartController::class, 'update'])->name('cart.update');
    Route::post('/cart/{menu}/remove', [CartController::class, 'remove'])->name('cart.remove');
    Route::post('/checkout', [CartController::class, 'checkout'])->name('checkout');
    
    Route::get('/orders', [OrderHistoryController::class, 'index'])->name('user.orders.index');
    Route::get('/orders/{order}', [OrderHistoryController::class, 'show'])->name('user.orders.show');
    Route::post('/orders/{order}/confirm-receipt', [CartController::class, 'confirmReceipt'])->name('user.orders.confirmReceipt');
    
    // Cancel order routes
    Route::post('/orders/{order}/request-cancel', [OrderHistoryController::class, 'requestCancel'])->name('user.orders.requestCancel');
    
    // Rating routes
    Route::post('/orders/{order}/rating', [RatingController::class, 'store'])->name('user.orders.rating');
    
    Route::get('/balance', [BalanceController::class, 'index'])->name('user.balance.index');
    Route::post('/balance/topup', [BalanceController::class, 'topup'])->name('user.balance.topup');
    Route::post('/balance/quick-topup', [BalanceController::class, 'quickTopup'])->name('user.balance.quickTopup');
    
    // Daily Spending Limit Settings
    Route::get('/settings/spending-limit', [App\Http\Controllers\User\SpendingLimitController::class, 'index'])->name('user.settings.spendingLimit');
    Route::post('/settings/spending-limit/enable', [App\Http\Controllers\User\SpendingLimitController::class, 'enable'])->name('user.settings.spendingLimit.enable');
    Route::post('/settings/spending-limit/disable', [App\Http\Controllers\User\SpendingLimitController::class, 'disable'])->name('user.settings.spendingLimit.disable');
    
    // Wishlist routes
    Route::get('/wishlist', [App\Http\Controllers\User\WishlistController::class, 'index'])->name('user.wishlist.index');
    Route::post('/wishlist/{menu}/add', [App\Http\Controllers\User\WishlistController::class, 'store'])->name('user.wishlist.add');
    Route::delete('/wishlist/{menu}/remove', [App\Http\Controllers\User\WishlistController::class, 'destroy'])->name('user.wishlist.remove');
    Route::post('/wishlist/{menu}/toggle', [App\Http\Controllers\User\WishlistController::class, 'toggle'])->name('user.wishlist.toggle');
    
    // Report/Contact CS routes
    Route::get('/orders/{order}/report', [App\Http\Controllers\User\ReportController::class, 'create'])->name('user.reports.create');
    Route::post('/orders/{order}/report', [App\Http\Controllers\User\ReportController::class, 'store'])->name('user.reports.store');
});

// Guest cart route (redirects to login if not authenticated)
Route::post('/cart-guest/{menu}/add', [CartController::class, 'addGuest'])->name('cart.add.guest');

// Public API routes for getting seller menus (accessible to guests)
Route::get('/api/seller/{seller}/menus', [App\Http\Controllers\User\SellerMenuController::class, 'getMenus'])->name('api.seller.menus');

// API route for getting menu details (with variants and addons)
Route::get('/api/menu/{menu}/details', [App\Http\Controllers\User\SellerMenuController::class, 'getMenuDetails'])->name('api.menu.details');

// API route for checking order statuses (for live updates)
Route::get('/api/orders/status', function (Illuminate\Http\Request $request) {
    $idsString = $request->input('ids', '');
    $orderIds = $idsString ? explode(',', $idsString) : [];
    
    if (empty($orderIds)) {
        return response()->json([]);
    }
    
    $user = auth()->user();
    
    $query = App\Models\Order::whereIn('id', $orderIds);
    
    // If user is admin, show all orders for their shop; otherwise show only their orders
    if ($user->role === 'admin') {
        // Admin can see all orders (or filter by seller_id if needed)
        $orders = $query->get(['id', 'status', 'cancel_request', 'is_completed', 'is_auto_confirmed', 'is_confirmed_by_seller', 'is_confirmed_by_user']);
    } else {
        // Regular user can only see their own orders
        $orders = $query->where('user_id', $user->id)->get(['id', 'status', 'cancel_request', 'is_completed', 'is_auto_confirmed', 'is_confirmed_by_seller', 'is_confirmed_by_user']);
    }
    
    return response()->json($orders);
})->middleware('auth');

// API route for admin notifications (withdrawal requests count)
Route::get('/api/admin/notifications', function () {
    $user = auth()->user();
    
    // Only for admins
    if ($user->role !== 'admin') {
        return response()->json(['count' => 0]);
    }
    
    // Count pending withdrawal requests for this seller (admin's own requests)
    $pendingWithdrawals = \App\Models\WithdrawalRequest::where('user_id', $user->id)
        ->where('status', 'pending')
        ->count();
    
    return response()->json(['count' => $pendingWithdrawals]);
})->middleware('auth');

// Admin Routes
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('admin.dashboard');
    
    // Seller Profile
    Route::get('/admin/profile', [App\Http\Controllers\Admin\SellerProfileController::class, 'edit'])->name('admin.profile.edit');
    Route::put('/admin/profile', [App\Http\Controllers\Admin\SellerProfileController::class, 'update'])->name('admin.profile.update');
    
    // Menu Management
    Route::get('/admin/menus', [MenuController::class, 'index'])->name('admin.menus.index');
    Route::get('/admin/menus/create', [MenuController::class, 'create'])->name('admin.menus.create');
    Route::post('/admin/menus', [MenuController::class, 'store'])->name('admin.menus.store');
    Route::get('/admin/menus/{menu}/edit', [MenuController::class, 'edit'])->name('admin.menus.edit');
    Route::put('/admin/menus/{menu}', [MenuController::class, 'update'])->name('admin.menus.update');
    Route::delete('/admin/menus/{menu}', [MenuController::class, 'destroy'])->name('admin.menus.destroy');
    Route::post('/admin/menus/{menu}/toggle-status', [MenuController::class, 'toggleStatus'])->name('admin.menus.toggleStatus');
    
    // Order Queue
    Route::get('/admin/orders', [OrderQueueController::class, 'index'])->name('admin.orders.index');
    Route::post('/admin/orders/{order}/accept', [OrderQueueController::class, 'acceptOrder'])->name('admin.orders.accept');
    Route::put('/admin/orders/{order}/status', [OrderQueueController::class, 'updateStatus'])->name('admin.orders.updateStatus');
    Route::post('/admin/orders/{order}/confirm-handover', [OrderQueueController::class, 'confirmHandover'])->name('admin.orders.confirmHandover');
    
    // Cancel request routes
    Route::post('/admin/orders/{order}/respond-cancel', [OrderQueueController::class, 'respondCancel'])->name('admin.orders.respondCancel');
    
    // Reports
    Route::get('/admin/reports', [ReportController::class, 'index'])->name('admin.reports.index');
    Route::get('/admin/reports/export', [ReportController::class, 'export'])->name('admin.reports.export');
    Route::get('/admin/reports/export-pdf', [ReportController::class, 'exportPdf'])->name('admin.reports.exportPdf');
    
    // Withdrawal - NEW ROUTES
    Route::get('/admin/withdrawal', [App\Http\Controllers\Admin\WithdrawalController::class, 'index'])->name('admin.withdrawal.index');
    Route::post('/admin/withdrawal', [App\Http\Controllers\Admin\WithdrawalController::class, 'store'])->name('admin.withdrawal.store');
    
    // Menu Ratings
    Route::get('/admin/ratings', [App\Http\Controllers\Admin\MenuRatingController::class, 'index'])->name('admin.ratings.index');
    Route::get('/admin/ratings/{menu}', [App\Http\Controllers\Admin\MenuRatingController::class, 'show'])->name('admin.ratings.show');
});

// Superadmin Routes
Route::middleware(['auth', 'role:superadmin'])->group(function () {
    Route::get('/superadmin/dashboard', [App\Http\Controllers\Superadmin\DashboardController::class, 'index'])->name('superadmin.dashboard');
    
    // User Management (block/unblock/delete)
    Route::get('/superadmin/users', [App\Http\Controllers\Superadmin\UserManagementController::class, 'index'])->name('superadmin.users.index');
    Route::get('/superadmin/users/{user}', [App\Http\Controllers\Superadmin\UserManagementController::class, 'show'])->name('superadmin.users.show');
    Route::post('/superadmin/users/{user}/block', [App\Http\Controllers\Superadmin\UserManagementController::class, 'block'])->name('superadmin.users.block');
    Route::post('/superadmin/users/{user}/unblock', [App\Http\Controllers\Superadmin\UserManagementController::class, 'unblock'])->name('superadmin.users.unblock');
    Route::delete('/superadmin/users/{user}', [App\Http\Controllers\Superadmin\UserManagementController::class, 'destroy'])->name('superadmin.users.destroy');
    
    // Seller Management
    Route::get('/superadmin/sellers', [App\Http\Controllers\Superadmin\SellerController::class, 'index'])->name('superadmin.sellers.index');
    Route::get('/superadmin/sellers/{seller}', [App\Http\Controllers\Superadmin\SellerController::class, 'show'])->name('superadmin.sellers.show');
    
    // Top-Up Management
    Route::get('/superadmin/topup', [App\Http\Controllers\Superadmin\TopUpController::class, 'index'])->name('superadmin.topup.index');
    Route::post('/superadmin/topup/{topUpRequest}/approve', [App\Http\Controllers\Superadmin\TopUpController::class, 'approve'])->name('superadmin.topup.approve');
    Route::post('/superadmin/topup/{topUpRequest}/reject', [App\Http\Controllers\Superadmin\TopUpController::class, 'reject'])->name('superadmin.topup.reject');
    
    // Global QR Code Management
    Route::post('/superadmin/topup/upload-global-qr', [App\Http\Controllers\Superadmin\TopUpController::class, 'uploadGlobalQrCode'])->name('superadmin.topup.uploadGlobalQr');
    Route::post('/superadmin/topup/delete-global-qr', [App\Http\Controllers\Superadmin\TopUpController::class, 'deleteGlobalQrCode'])->name('superadmin.topup.deleteGlobalQr');
    
    // Withdrawal Management
    Route::get('/superadmin/withdrawal', [App\Http\Controllers\Superadmin\WithdrawalController::class, 'index'])->name('superadmin.withdrawal.index');
    Route::post('/superadmin/withdrawal/{withdrawalRequest}/approve', [App\Http\Controllers\Superadmin\WithdrawalController::class, 'approve'])->name('superadmin.withdrawal.approve');
    Route::post('/superadmin/withdrawal/{withdrawalRequest}/reject', [App\Http\Controllers\Superadmin\WithdrawalController::class, 'reject'])->name('superadmin.withdrawal.reject');
    
    // Reports Management
    Route::get('/superadmin/reports', [App\Http\Controllers\Superadmin\ReportController::class, 'index'])->name('superadmin.reports.index');
    Route::get('/superadmin/reports/{report}', [App\Http\Controllers\Superadmin\ReportController::class, 'show'])->name('superadmin.reports.show');
    Route::put('/superadmin/reports/{report}', [App\Http\Controllers\Superadmin\ReportController::class, 'update'])->name('superadmin.reports.update');
});

