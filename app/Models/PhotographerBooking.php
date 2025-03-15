<?php

namespace App\Models;

use App\Models\User;
use App\Models\Payment;
use App\Models\Photographer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PhotographerBooking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'photographer_id',
        'payment_id',
        'start_time',
        'end_time',
        'price',
        'status', // pending, confirmed, cancelled
        'notes',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'price' => 'float',
    ];

    /**
     * Relasi dengan user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi dengan fotografer
     */
    public function photographer()
    {
        return $this->belongsTo(Photographer::class);
    }

    /**
     * Relasi dengan pembayaran
     */
    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }



}
