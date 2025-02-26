<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RentalItem extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
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
        'image'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'rental_price' => 'integer',
        'stock_total' => 'integer',
        'stock_available' => 'integer',
        'is_active' => 'boolean'
    ];

    /**
     * Get the category options
     *
     * @return array
     */
    public static function getCategoryOptions()
    {
        return ['ball', 'jersey', 'shoes', 'other'];
    }

    /**
     * Check if the item is available for rent
     *
     * @return bool
     */
    public function isAvailable()
    {
        return $this->stock_available > 0 && $this->is_active;
    }
}
