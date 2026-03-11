<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'menu_id',
        'qty',
        'price',
        'subtotal',
        'variant_name',
        'addons_json',
        'notes',
    ];

    protected $casts = [
        'qty' => 'integer',
        'price' => 'integer',
        'subtotal' => 'integer',
    ];

    // Relationships
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }

    // Get decoded addons
    public function getAddonsAttribute()
    {
        if ($this->addons_json) {
            return json_decode($this->addons_json, true);
        }
        return [];
    }

    // Get total addons price
    public function getAddonsPriceAttribute()
    {
        $addons = $this->addons;
        $total = 0;
        foreach ($addons as $addon) {
            $total += $addon['price'] ?? 0;
        }
        return $total;
    }
}
