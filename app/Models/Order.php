<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'seller_id',
        'pickup_slot',
        'classroom',
        'status',
        'total_amount',
        'is_paid',
        'is_confirmed_by_user',
        'is_confirmed_by_seller',
        'is_completed',
        'completed_at',
        'ready_at',
        'is_auto_confirmed',
        'cancel_request',
        'cancel_requested_at',
        'cancel_responded_at',
    ];

    protected $casts = [
        'total_amount' => 'integer',
        'is_paid' => 'boolean',
        'is_confirmed_by_user' => 'boolean',
        'is_confirmed_by_seller' => 'boolean',
        'is_completed' => 'boolean',
        'completed_at' => 'datetime',
        'ready_at' => 'datetime',
        'is_auto_confirmed' => 'boolean',
        'cancel_requested_at' => 'datetime',
        'cancel_responded_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    public function report()
    {
        return $this->hasOne(Report::class);
    }

    public function getStatusLabelAttribute()
    {
        if ($this->is_completed) {
            return 'Selesai';
        }
        if ($this->is_auto_confirmed) {
            return 'Selesai (Auto)';
        }
        if ($this->cancel_request === 'accepted') {
            return 'Dibatalkan';
        }
        
        return match($this->status) {
            'pending' => 'Pending',
            'preparing' => 'Sedang Disiapkan',
            'ready' => 'Siap Diambil',
            default => $this->status,
        };
    }

    public function getPickupSlotLabelAttribute()
    {
        return $this->pickup_slot === 'istirahat_1' ? 'Istirahat 1' : 'Istirahat 2';
    }

    public function getPaymentStatusLabelAttribute()
    {
        if ($this->is_completed || $this->is_auto_confirmed) {
            return 'Selesai (Dana ke Seller)';
        }
        if ($this->is_confirmed_by_seller && !$this->is_confirmed_by_user) {
            return 'Menunggu Konfirmasi Pembeli';
        }
        if ($this->is_paid) {
            return 'Lunas (Dana di Escrow)';
        }
        return 'Belum Bayar';
    }

    // Check if user can request cancel based on status
    public function canRequestCancel()
    {
        // Can only cancel when status is still pending
        if ($this->status !== 'pending') {
            return false;
        }

        // Cannot cancel if already cancelled or completed
        if ($this->cancel_request === 'accepted' || $this->is_completed || $this->is_auto_confirmed) {
            return false;
        }

        // ensure previous cancel requests were not accepted (or still pending)
        if ($this->cancel_request === 'pending') {
            return false;
        }

        return true;
    }

    // Check if user can still request cancel (wrapper for clarity)
    public function canUserRequestCancel()
    {
        return $this->canRequestCancel();
    }

    // Check if cancel should be auto-accepted (when status is "pending")
    public function shouldAutoAcceptCancel()
    {
        return $this->status === 'pending' && 
               $this->cancel_request === 'pending';
    }

    // Check if seller can respond to cancel request
    public function canSellerRespondToCancel()
    {
        return $this->cancel_request === 'pending';
    }

    // Check if seller approval is needed for cancel (when status is "preparing")
    public function needsSellerApprovalForCancel()
    {
        return $this->status === 'preparing' && 
               $this->cancel_request === 'pending';
    }

    // Seller can confirm handover when order is ready
    public function canSellerConfirmHandover()
    {
        return $this->status === 'ready' && 
               !$this->is_confirmed_by_seller && 
               $this->cancel_request !== 'accepted';
    }

    // After seller confirms Buyer can confirm receipt handover
    public function canUserConfirm()
    {
        return $this->status === 'ready' && 
               $this->is_confirmed_by_seller && 
               !$this->is_confirmed_by_user &&
               $this->cancel_request !== 'accepted';
    }

    // Check if 6 hours have passed since order was marked ready
    public function canAutoConfirm()
    {
        if (!$this->ready_at || $this->is_confirmed_by_user || $this->is_completed || $this->is_auto_confirmed) {
            return false;
        }
        
        $hoursPassed = $this->ready_at->diffInHours(now());
        return $hoursPassed >= 6;
    }

    // Get remaining time for auto-confirm in minutes
    public function getAutoConfirmRemainingMinutes()
    {
        if (!$this->ready_at) {
            return 360; // 6 hours
        }
        
        $totalMinutes = 360; // 6 hours = 360 minutes
        $minutesPassed = $this->ready_at->diffInMinutes(now());
        $remaining = $totalMinutes - $minutesPassed;
        
        return max(0, $remaining);
    }

    // Check if timer should be shown (order is ready and waiting for user confirmation)
    public function shouldShowTimer()
    {
        return $this->status === 'ready' && 
               $this->is_confirmed_by_seller && 
               !$this->is_confirmed_by_user && 
               !$this->is_completed &&
               !$this->is_auto_confirmed;
    }

    // Check if revenue should be counted (both confirmed)
    public function shouldCountRevenue()
    {
        return ($this->is_completed || $this->is_auto_confirmed) && 
               $this->is_confirmed_by_seller && 
               $this->is_confirmed_by_user;
    }
}
