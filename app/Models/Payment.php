<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'user_id',
        'amount',
        'payment_type',
        'transaction_id',
        'transaction_status',
        'transaction_time',
        'payment_details',
    ];

    /**
     * Get the user that owns the payment.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the field bookings associated with this payment.
     */
    public function fieldBookings()
    {
        return $this->hasMany(FieldBooking::class);
    }
}
