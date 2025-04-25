<?php

namespace App\Models;

use App\Models\User;
use App\Models\ProductSaleItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductSale extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'order_id',
        'user_id',
        'admin_id',
        'payment_method',
        'total_amount',
        'discount_amount',
        'status',
        'note',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'total_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user that owns the sale.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the admin that processed the sale.
     */
    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    /**
     * Get the items for the sale.
     */
    public function productSaleItems()
    {
        return $this->hasMany(ProductSaleItem::class);
    }

    /**
     * Scope a query to only include sales with a specific status.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $status
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to only include sales for a specific user.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $userId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope a query to only include sales processed by a specific admin.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $adminId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeProcessedBy($query, $adminId)
    {
        return $query->where('admin_id', $adminId);
    }

    /**
     * Get the total quantity of items in this sale.
     *
     * @return int
     */
    public function getTotalQuantityAttribute()
    {
        return $this->productSaleItems->sum('quantity');
    }

    /**
     * Get the subtotal amount (before discount).
     *
     * @return float
     */
    public function getSubtotalAttribute()
    {
        return $this->total_amount + $this->discount_amount;
    }
}
