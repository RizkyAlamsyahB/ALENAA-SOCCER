<?php

namespace App\Models;

use App\Models\User;
use App\Models\Field;
use App\Models\Review;
use App\Models\Membership;
use App\Models\PhotographerBooking;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Photographer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
       'user_id',     // ID pengguna yang membuat fotografer
        'name',
        'description',
        'price',
        'package_type', // 'favorite', 'plus', 'exclusive'
        'duration',     // Durasi dalam jam
        'field_id',     // ID lapangan jika terkait dengan lapangan tertentu
        'image',        // Gambar/foto fotografer
        'status',       // aktif/tidak aktif
        'features',     // JSON untuk menyimpan fitur tambahan
        ''
    ];
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

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
// Tambahkan di model Photographer.php

/**
 * Relasi dengan review
 */
public function reviews()
{
    return $this->morphMany(Review::class, 'reviewable', 'item_type', 'item_id');
}

/**
 * Mendapatkan rata-rata rating fotografer
 */
public function getRatingAttribute()
{
    return $this->reviews()->where('status', 'active')->avg('rating') ?: 0;
}

/**
 * Mendapatkan jumlah review fotografer
 */
public function getReviewsCountAttribute()
{
    return $this->reviews()->where('status', 'active')->count();
}

/**
 * Get the user who created this photographer
 */
public function user()
{
    return $this->belongsTo(User::class);
}
/**
 * Get the memberships that use this photographer
 */
public function memberships()
{
    return $this->hasMany(Membership::class);
}

}
