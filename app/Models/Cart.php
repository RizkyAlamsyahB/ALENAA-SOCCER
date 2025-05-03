<?php

namespace App\Models;

use App\Models\Payment;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $fillable = ['user_id', 'type'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }
    // App\Models\Cart.php
    public function payment()
    {
        return $this->hasOne(Payment::class, 'cart_id', 'id');
    }
    /**
     * Scope a query to only include POS carts.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePos($query)
    {
        return $query->where('type', 'pos');
    }

    /**
     * Scope a query to only include normal (non-POS) carts.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNormal($query)
    {
        return $query->where(function ($q) {
            $q->where('type', '!=', 'pos')->orWhereNull('type');
        });
    }
}
