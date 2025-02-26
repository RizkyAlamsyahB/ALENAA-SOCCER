<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
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
        'price',
        'stock',
        'is_active',
        'image'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'price' => 'integer',
        'stock' => 'integer',
        'is_active' => 'boolean'
    ];

    /**
     * Get the category options
     *
     * @return array
     */
    public static function getCategoryOptions()
    {
        return ['food', 'beverage', 'equipment', 'other'];
    }
}
