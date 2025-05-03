<?php

namespace App\Models;

use App\Models\User;
use App\Models\Review;
use App\Models\FieldBooking;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Field extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'type', 'price', 'image', 'photographer_id', 'description'];
    protected $hidden = ['created_at', 'updated_at'];
    protected $appends = ['rating', 'reviews_count'];

    protected $casts = [
        'price' => 'integer',
        'features' => 'array',
    ];
    protected $dates = ['created_at', 'updated_at'];



    /**
     * Get the bookings for the field.
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(FieldBooking::class);
    }

    // Di model Field.php
    public function reviews()
    {
        return $this->morphMany(Review::class, 'reviewable', 'item_type', 'item_id');
    }

    /**
     * Mendapatkan rata-rata rating lapangan
     */
    public function getRatingAttribute()
    {
        return $this->reviews()->where('status', 'active')->avg('rating') ?: 0;
    }

    /**
     * Mendapatkan jumlah review lapangan
     */
    public function getReviewsCountAttribute()
    {
        return $this->reviews()->where('status', 'active')->count();
    }
    public function photographer()
    {
        return $this->belongsTo(Photographer::class, 'photographer_id');
    }
}
