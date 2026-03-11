<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SellerProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'store_name',
        'store_logo',
        'store_banner',
        'store_description',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function menus()
    {
        return $this->user->menus();
    }
}
