<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RentalItem extends Model
{
    use HasFactory;

    /**
     * Atribut yang dapat diisi secara massal
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'category',
        'rental_price',
        'stock_total',
        'stock_available',
        'condition',
        'is_active',
        'image',
    ];

    /**
     * Atribut yang harus dikonversi ke tipe data tertentu
     *
     * @var array
     */
    protected $casts = [
        'is_active' => 'boolean',
        'rental_price' => 'integer',
        'stock_total' => 'integer',
        'stock_available' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Scope untuk item yang aktif
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope untuk item yang tersedia
     */
    public function scopeAvailable($query)
    {
        return $query->where('stock_available', '>', 0)->where('is_active', true);
    }

    /**
     * Relasi ke model ItemRental (rental records)
     */
    // public function itemRentals()
    // {
    //     return $this->hasMany(ItemRental::class);
    // }

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
     * Mendapatkan status ketersediaan item
     */
    public function getAvailabilityStatusAttribute()
    {
        $percentage = $this->availability_percentage;

        if ($percentage <= 0) {
            return 'Habis';
        } elseif ($percentage <= 20) {
            return 'Hampir Habis';
        } elseif ($percentage <= 50) {
            return 'Terbatas';
        } else {
            return 'Tersedia';
        }
    }
}
