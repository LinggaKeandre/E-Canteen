<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_id',
        'reason',
        'description',
        'status',
        'reviewed_by',
        'reviewed_at',
        'resolution_notes',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'pending' => 'Menunggu',
            'reviewed' => 'Ditinjau',
            'resolved' => 'Selesai',
            'rejected' => 'Ditolak',
            default => $this->status,
        };
    }

    public function getReasonLabelAttribute()
    {
        return match($this->reason) {
            'food_quality' => 'Kualitas Makanan Buruk',
            'missing_item' => 'Item Tidak Lengkap',
            'wrong_order' => 'Pesanan Salah',
            'late_delivery' => 'Pengiriman Terlambat',
            'seller_behavior' => 'Perilaku Penjual',
            'other' => 'Lainnya',
            default => $this->reason,
        };
    }
}

