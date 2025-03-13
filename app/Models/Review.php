<?php

namespace App\Models;

use App\Models\User;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'item_id',
        'item_type',
        'payment_id',
        'rating',
        'comment',
        'status',
    ];

    // Relasi ke user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi polymorphic ke item (field, rental item, photographer, dll)
    public function reviewable()
    {
        return $this->morphTo(null, 'item_type', 'item_id');
    }

    // Relasi ke payment
    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }
}
