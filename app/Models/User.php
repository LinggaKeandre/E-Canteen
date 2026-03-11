<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'balance',
        'topup_qr_code',
        'phone_number',
        'username',
        'profile_photo',
        'description',
        'daily_spending_limit',
        'daily_spending_limit_enabled',
        'daily_spending_limit_resets_at',
        'daily_limit_enabled_at',
        'blocked_at',
        'blocked_until',
        'blocked_reason',
        'blocked_by',
        'block_type',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'blocked_at' => 'datetime',
            'blocked_until' => 'datetime',
        ];
    }

    // Relationships
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function menus()
    {
        return $this->hasMany(Menu::class);
    }

    public function balanceTransactions()
    {
        return $this->hasMany(BalanceTransaction::class);
    }

    public function sellerProfile()
    {
        return $this->hasOne(SellerProfile::class);
    }

    // Wishlist relationship
    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    // Get wishlist menu IDs for quick access
    public function getWishlistMenuIds()
    {
        return $this->wishlists()->pluck('menu_id')->toArray();
    }

    // Check if a menu is in user's wishlist
    public function hasInWishlist($menuId)
    {
        return $this->wishlists()->where('menu_id', $menuId)->exists();
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isUser()
    {
        return $this->role === 'user';
    }

    // Daily Spending Limit Methods
    
    /**
     * Check if daily spending limit is enabled
     */
    public function isDailySpendingLimitEnabled()
    {
        return $this->daily_spending_limit_enabled && $this->daily_spending_limit > 0;
    }

    /**
     * Get total spending for today (only orders made AFTER limit was enabled)
     */
    public function getTodaySpending()
    {
        // Check if we need to reset (new day)
        $this->checkAndResetDailyLimit();
        
        $query = \App\Models\Order::where('user_id', $this->id)
            ->where('is_paid', true)
            ->whereDate('created_at', today());
        
        // Only count orders made AFTER the limit was enabled
        if ($this->daily_limit_enabled_at) {
            $query->where('created_at', '>=', $this->daily_limit_enabled_at);
        }
        
        return $query->sum('total_amount');
    }

    /**
     * Get remaining daily spending limit
     */
    public function getRemainingDailyLimit()
    {
        if (!$this->isDailySpendingLimitEnabled()) {
            return null;
        }
        
        $todaySpending = $this->getTodaySpending();
        return max(0, $this->daily_spending_limit - $todaySpending);
    }

    /**
     * Check if adding amount would exceed daily limit
     */
    public function wouldExceedDailyLimit($amount)
    {
        if (!$this->isDailySpendingLimitEnabled()) {
            return false;
        }
        
        $todaySpending = $this->getTodaySpending();
        return ($todaySpending + $amount) > $this->daily_spending_limit;
    }

    /**
     * Check and reset daily limit if it's a new day
     */
    public function checkAndResetDailyLimit()
    {
        if (!$this->daily_spending_limit_resets_at) {
            return;
        }
        
        // If the reset time has passed today, we don't need to do anything
        // The spending is calculated based on today's date
        // This method is mainly for ensuring the reset_at is set correctly
        if (now()->gte($this->daily_spending_limit_resets_at)) {
            // Reset time has passed, set for next day
            $this->daily_spending_limit_resets_at = now()->endOfDay()->addSecond();
            $this->save();
        }
    }

    /**
     * Enable daily spending limit
     */
    public function enableDailySpendingLimit($limit)
    {
        $this->daily_spending_limit = $limit;
        $this->daily_spending_limit_enabled = true;
        $this->daily_spending_limit_resets_at = now()->endOfDay()->addSecond();
        $this->daily_limit_enabled_at = now(); // Track when limit was enabled
        $this->save();
    }

    /**
     * Disable daily spending limit
     */
    public function disableDailySpendingLimit()
    {
        $this->daily_spending_limit_enabled = false;
        $this->daily_spending_limit = 0;
        $this->daily_spending_limit_resets_at = null;
        $this->save();
    }
    
    /**
     * Reset daily spending (for when user re-enables limit)
     * Note: The spending is calculated based on today's orders, so it automatically resets at midnight.
     * This method is kept for any future custom reset logic.
     */
    public function resetDailySpending()
    {
        // Spending is based on date, so this is handled automatically in getTodaySpending()
        // No need to store spending separately as it's calculated from orders table
        return true;
    }

    // Blocking Methods
    
    /**
     * Check if user is currently blocked
     */
    public function isBlocked(): bool
    {
        if ($this->block_type === 'permanent') {
            return true;
        }
        
        if ($this->blocked_until && now()->lt($this->blocked_until)) {
            return true;
        }
        
        return false;
    }

    /**
     * Block user temporarily and invalidate their sessions
     */
    public function block(User $admin, string $reason, int $days): self
    {
        $blockedUntil = now()->addDays($days);
        
        $this->update([
            'blocked_at' => now(),
            'blocked_until' => $blockedUntil,
            'blocked_reason' => $reason,
            'blocked_by' => $admin->id,
            'block_type' => 'temporary',
        ]);
        
        // Invalidate all existing sessions for this user
        $this->invalidateSessions();
        
        return $this;
    }

    /**
     * Block user permanently and invalidate their sessions
     */
    public function blockPermanently(User $admin, string $reason): self
    {
        $this->update([
            'blocked_at' => now(),
            'blocked_until' => null,
            'blocked_reason' => $reason,
            'blocked_by' => $admin->id,
            'block_type' => 'permanent',
        ]);
        
        // Invalidate all existing sessions for this user
        $this->invalidateSessions();
        
        return $this;
    }

    /**
     * Invalidate all sessions for this user (force logout)
     */
    public function invalidateSessions(): void
    {
        // Delete all remember tokens to invalidate "remember me" sessions
        $this->update(['remember_token' => null]);
        
        // Delete all sessions from database session store
        \DB::table('sessions')
            ->where('user_id', $this->id)
            ->delete();
    }

    /**
     * Unblock user
     */
    public function unblock(): self
    {
        $this->update([
            'blocked_at' => null,
            'blocked_until' => null,
            'blocked_reason' => null,
            'blocked_by' => null,
            'block_type' => null,
        ]);
        
        return $this;
    }

    /**
     * Get blocker (who blocked this user)
     */
    public function blocker()
    {
        return $this->belongsTo(User::class, 'blocked_by');
    }

    /**
     * Scope to get only blocked users
     */
    public function scopeBlocked($query)
    {
        return $query->where(function ($q) {
            $q->where('block_type', 'permanent')
              ->orWhere(function ($q2) {
                  $q2->where('block_type', 'temporary')
                     ->where('blocked_until', '>', now());
              });
        });
    }

    /**
     * Scope to get only unblocked users
     */
    public function scopeUnblocked($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('block_type')
              ->orWhere(function ($q2) {
                  $q2->where('block_type', 'temporary')
                     ->where(function ($q3) {
                         $q3->whereNull('blocked_until')
                            ->orWhere('blocked_until', '<=', now());
                     });
              });
        });
    }
}
