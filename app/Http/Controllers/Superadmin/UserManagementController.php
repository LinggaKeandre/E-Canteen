<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Order;
use App\Models\Menu;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserManagementController extends Controller
{
    public function index(Request $request)
    {
        $role = $request->get('role', 'all');
        $status = $request->get('status', 'all');
        $search = $request->get('search', '');

        $query = User::query();

        // Role filter
        if ($role === 'user') {
            $query->where('role', 'user');
        } elseif ($role === 'seller') {
            $query->where('role', 'admin');
        }

        // Status filter
        if ($status === 'blocked') {
            $query->where(function ($q) {
                $q->where('block_type', 'permanent')
                  ->orWhere(function ($q2) {
                      $q2->where('block_type', 'temporary')
                         ->where('blocked_until', '>', now());
                  });
            });
        } elseif ($status === 'active') {
            $query->where(function ($q) {
                $q->whereNull('block_type')
                  ->orWhere('block_type', '!=', 'permanent')
                  ->where(function ($q2) {
                      $q2->whereNull('blocked_until')
                         ->orWhere('blocked_until', '<=', now());
                  });
            });
        }

        // Search
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%");
            });
        }

        // Exclude superadmin
        $query->where('role', '!=', 'superadmin');

        $users = $query->orderBy('created_at', 'desc')->paginate(20);

        // Get stats
        $stats = [
            'total_users' => User::where('role', 'user')->count(),
            'total_sellers' => User::where('role', 'admin')->count(),
            'blocked' => User::where(function ($q) {
                $q->where('block_type', 'permanent')
                  ->orWhere(function ($q2) {
                      $q2->where('block_type', 'temporary')
                         ->where('blocked_until', '>', now());
                  });
            })->count(),
        ];

        return view('superadmin.users.index', compact('users', 'stats', 'role', 'status', 'search'));
    }

    public function show(User $user)
    {
        // Prevent viewing superadmin
        if ($user->role === 'superadmin') {
            return redirect()->route('superadmin.users.index')->with('error', 'Tidak dapat melihat detail superadmin.');
        }

        $user->load('sellerProfile');

        // Get user stats
        if ($user->role === 'admin') {
            // Seller stats
            $sellerMenuIds = Menu::where('user_id', $user->id)->pluck('id');
            $sellerOrderIds = OrderItem::whereIn('menu_id', $sellerMenuIds)->pluck('order_id')->unique();

            $stats = [
                'total_menus' => Menu::where('user_id', $user->id)->count(),
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
            ];
        } else {
            // Regular user stats
            $stats = [
                'total_orders' => Order::where('user_id', $user->id)->count(),
                'total_spent' => Order::where('user_id', $user->id)
                    ->where(function ($query) {
                        $query->where(function ($q) {
                            $q->where('is_completed', true)
                              ->orWhere('is_auto_confirmed', true);
                        })->where('is_confirmed_by_seller', true)
                           ->where('is_confirmed_by_user', true);
                    })
                    ->sum('total_amount'),
            ];
        }

        return view('superadmin.users.show', compact('user', 'stats'));
    }

    public function block(Request $request, User $user)
    {
        // Prevent blocking superadmin
        if ($user->role === 'superadmin') {
            return back()->with('error', 'Tidak dapat memblokir superadmin.');
        }

        // Prevent blocking self
        if ($user->id === Auth::id()) {
            return back()->with('error', 'Tidak dapat memblokir diri sendiri.');
        }

        $request->validate([
            'block_type' => 'required|in:temporary,permanent',
            'block_duration' => 'required_if:block_type,temporary|integer|min:1',
            'reason' => 'required|string|min:3|max:500',
        ]);

        $admin = Auth::user();

        if ($request->block_type === 'permanent') {
            $user->blockPermanently($admin, $request->reason);
            $message = "Akun {$user->name} telah diblokir secara permanen.";
        } else {
            $days = $request->block_duration;
            $user->block($admin, $request->reason, $days);
            $message = "Akun {$user->name} telah diblokir selama {$days} hari.";
        }

        return back()->with('success', $message);
    }

    public function unblock(User $user)
    {
        // Prevent unblocking superadmin
        if ($user->role === 'superadmin') {
            return back()->with('error', 'Tidak dapat membuka blokir superadmin.');
        }

        $user->unblock();

        return back()->with('success', "Akun {$user->name} telah dibuka blokirnya.");
    }

    public function destroy(Request $request, User $user)
    {
        // Prevent deleting superadmin
        if ($user->role === 'superadmin') {
            return back()->with('error', 'Tidak dapat menghapus superadmin.');
        }

        // Prevent deleting self
        if ($user->id === Auth::id()) {
            return back()->with('error', 'Tidak dapat menghapus akun sendiri.');
        }

        $request->validate([
            'confirmation' => 'required|string|in:HAPUS',
        ]);

        $userName = $user->name;

        DB::beginTransaction();
        try {
            // If it's a seller, handle related data
            if ($user->role === 'admin') {
                // Delete seller profile
                $user->sellerProfile?->delete();

                // Delete menus
                Menu::where('user_id', $user->id)->delete();
            }

            // Delete the user (this will cascade delete related records if set up)
            $user->delete();

            DB::commit();

            return redirect()->route('superadmin.users.index')->with('success', "Akuan {$userName} telah dihapus.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan saat menghapus akun.');
        }
    }
}

