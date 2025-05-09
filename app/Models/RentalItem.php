<?php

namespace App\Models;

use App\Models\Review;
use App\Models\CartItem;
use App\Models\RentalBooking;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RentalItem extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Atribut yang dapat diisi secara massal
     *
     * @var array
     */
    protected $fillable = ['name', 'description', 'category', 'rental_price', 'stock_total', 'stock_available', 'condition', 'image'];

    /**
     * Atribut yang harus dikonversi ke tipe data tertentu
     *
     * @var array
     */
    protected $casts = [
        'rental_price' => 'integer',
        'stock_total' => 'integer',
        'stock_available' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    /**
     * Relasi ke model RentalBooking
     */
    public function rentalBookings()
    {
        return $this->hasMany(RentalBooking::class);
    }

    /**
     * Relasi ke model CartItem
     */
    public function cartItems()
    {
        return $this->hasMany(CartItem::class, 'item_id')->where('type', 'rental_item');
    }

    /**
     * Relasi ke model Review
     */
    // public function reviews()
    // {
    //     return $this->morphMany(Review::class, 'reviewable');
    // }

    /**
     * Mendapatkan persentase ketersediaan item
     */
    public function getAvailabilityPercentageAttribute()
    {
        if ($this->stock_total <= 0) {
            return 0;
        }

        return ($this->stock_available / $this->stock_total) * 100;
    }

    /**
     * Mendapatkan kategori dalam bahasa Indonesia
     */
    public function getCategoryNameAttribute()
    {
        switch ($this->category) {
            case 'ball':
                return 'Bola';
            case 'jersey':
                return 'Jersey';
            case 'shoes':
                return 'Sepatu';
            case 'other':
                return 'Lainnya';
            default:
                return ucfirst($this->category);
        }
    }

    // Di model Field.php
    // Tambahkan di Model RentalItem.php

    /**
     * Relasi dengan review
     */
    public function reviews()
    {
        return $this->morphMany(Review::class, 'reviewable', 'item_type', 'item_id');
    }

    /**
     * Mendapatkan rata-rata rating item rental
     */
    public function getRatingAttribute()
    {
        return $this->reviews()->where('status', 'active')->avg('rating') ?: 0;
    }

    /**
     * Mendapatkan jumlah review item rental
     */
    public function getReviewsCountAttribute()
    {
        return $this->reviews()->where('status', 'active')->count();
    }
    /**
     * Get the memberships that use this rental item.
     */
    public function memberships()
    {
        return $this->hasMany(Membership::class, 'rental_item_id');
    }
}
