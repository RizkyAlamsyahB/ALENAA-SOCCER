<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Field extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'type',
        'regular_price',
        'peak_price',
        'facilities',
        'is_active',
        'image'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'regular_price' => 'integer',
        'peak_price' => 'integer',
        'is_active' => 'boolean',
        'facilities' => 'array'
    ];
}
