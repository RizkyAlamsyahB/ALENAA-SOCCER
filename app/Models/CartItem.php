<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CartItem extends Model
{
    protected $fillable = [
        'cart_id',
        'type',
        'item_id',
        'start_time',
        'end_time',
        'quantity',
        'price',
        'membership_sessions',
    ];

    protected $dates = ['start_time', 'end_time'];

    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    public function field()
    {
        return $this->belongsTo(Field::class, 'item_id');
    }

    /**
     * Scope to filter by specific type
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }
}
