<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'menu_id',
        'name',
        'price_adjustment',
    ];

    protected $casts = [
        'price_adjustment' => 'integer',
    ];

    // Relationships
    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }

    // Get the final price (base price + variant adjustment)
    public function getFinalPriceAttribute()
    {
        return $this->menu->price + $this->price_adjustment;
    }
}

