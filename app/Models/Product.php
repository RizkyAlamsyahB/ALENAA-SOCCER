<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

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
    ];
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

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
