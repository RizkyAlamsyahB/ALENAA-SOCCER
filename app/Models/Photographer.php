<?php

namespace App\Models;

use App\Models\Field;
use App\Models\Review;
use App\Models\PhotographerBooking;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Photographer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'package_type', // 'favorite', 'plus', 'exclusive'
        'duration',     // Durasi dalam jam
        'field_id',     // ID lapangan jika terkait dengan lapangan tertentu
        'image',        // Gambar/foto fotografer
        'status',       // aktif/tidak aktif
        'features',     // JSON untuk menyimpan fitur tambahan
    ];

    protected $casts = [
        'features' => 'array',
        'price' => 'float',
    ];

    /**
     * Relasi dengan lapangan
     */
    public function field()
    {
        return $this->belongsTo(Field::class);
    }

    /**
     * Relasi dengan booking fotografer
     */
    public function bookings()
    {
        return $this->hasMany(PhotographerBooking::class);
    }
        /**
     * Get the reviews for this photographer.
     */// In Photographer model
public function reviews()
{
    return $this->morphMany(Review::class, 'item');
}
}
