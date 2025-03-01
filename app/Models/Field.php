<?php

namespace App\Models;

use App\Models\FieldBooking;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Field extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'price',
        'image'
    ];

    protected $casts = [
        'price' => 'integer',
    ];

    /**
     * Get the bookings for the field.
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(FieldBooking::class);
    }
}
