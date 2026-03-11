<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'category',
        'photo_path',
        'status',
        'average_rating',
        'rating_count',
        'user_id',
        'has_variants',
        'has_addons',
        'is_daily',
        'available_days',
    ];

    protected $casts = [
        'price' => 'integer',
        'average_rating' => 'decimal:2',
        'rating_count' => 'integer',
        'is_daily' => 'boolean',
        'available_days' => 'array',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function seller()
    {
        return $this->hasOneThrough(
            SellerProfile::class,
            User::class,
            'id',
            'user_id',
            'user_id',
            'id'
        );
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    // Relationships for Variants
    public function variants()
    {
        return $this->hasMany(MenuVariant::class);
    }

    // Relationships for Addons
    public function addons()
    {
        return $this->hasMany(MenuAddon::class);
    }

    // Wishlist relationship
    public function wishlisters()
    {
        return $this->belongsToMany(User::class, 'wishlists');
    }

    // Check if a user has wishlisted this menu
    public function isWishlistedBy($userId)
    {
        return $this->wishlisters()->where('user_id', $userId)->exists();
    }

    public function isAvailable()
    {
        return $this->status === 'tersedia';
    }

    // Check if menu is available on a specific day (1 = Monday, 5 = Friday)
    public function isAvailableOnDay($day)
    {
        if (!$this->is_daily) {
            return true; // Non-daily menus are always available
        }
        
        $availableDays = $this->available_days ?? [];
        return in_array($day, $availableDays);
    }

    // Check if menu is available today
    public function isAvailableToday()
    {
        // Get current day of week (1 = Monday, 7 = Sunday in Laravel)
        $today = now()->dayOfWeek;
        
        // Convert Laravel day (0=Sunday, 1=Monday) to our format (1=Monday, 7=Sunday)
        // Laravel: 0 = Sunday, 1 = Monday, ..., 6 = Saturday
        // Our format: 1 = Monday, ..., 5 = Friday, 6 = Saturday, 7 = Sunday
        $dayMap = [0 => 7, 1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5, 6 => 6];
        $day = $dayMap[$today];
        
        return $this->isAvailableOnDay($day);
    }

    // Get available days as array of day names
    public function getAvailableDaysDisplayAttribute()
    {
        $dayNames = [
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
            7 => 'Minggu',
        ];
        
        $days = $this->available_days ?? [];
        return collect($days)->map(fn($d) => $dayNames[$d] ?? $d)->implode(', ');
    }

    // Get all weekdays for selection
    public static function getWeekdays()
    {
        return [
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
        ];
    }

    // Get star rating display
    public function getStarRatingAttribute()
    {
        $rating = $this->average_rating ?? 0;
        $fullStars = floor($rating);
        $halfStar = ($rating - $fullStars) >= 0.5 ? 1 : 0;
        $emptyStars = 5 - $fullStars - $halfStar;
        
        return [
            'full' => $fullStars,
            'half' => $halfStar,
            'empty' => $emptyStars,
            'display' => number_format($rating, 1)
        ];
    }

    // Get all available categories
    public static function getCategories()
    {
        return [
            'Makanan Berat',
            'Makanan Ringan',
            'Bakso & Mie',
            'Jajanan Tradisional',
            'Roti & Bakery',
            'Minuman Dingin',
            'Minuman Hangat',
            'Minuman Kemasan',
            'Dessert',
            'Buah Segar',
            'Gorengan',
            'Olahan Ayam',
            'Olahan Telur',
            'Olahan Mie',
            'Olahan Nasi',
            'Makanan Pedas',
            'Makanan Sehat',
            'Minuman Susu',
            'Minuman Manis',
            'Paket Hemat',
        ];
    }
}
