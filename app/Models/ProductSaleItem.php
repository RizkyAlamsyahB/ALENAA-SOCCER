<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductSaleItem extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'product_id',
        'payment_id',
        'quantity',
        'price',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'price' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the product associated with this sale item.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the payment associated with this sale item.
     */
    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    /**
     * Get the subtotal for this item (price * quantity).
     *
     * @return float
     */
    public function getSubtotalAttribute()
    {
        return $this->price * $this->quantity;
    }
}
