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
        'expires_at'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
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

    /**
     * Get the rental bookings associated with this payment.
     */
    public function rentalBookings()
    {
        return $this->hasMany(RentalBooking::class);
    }

    /**
     * Get the membership subscriptions associated with this payment.
    //  */
    // public function membershipSubscriptions()
    // {
    //     return $this->hasMany(MembershipSubscription::class);
    // }

    /**
     * Get the photographer bookings associated with this payment.
     */
    // public function photographerBookings()
    // {
    //     return $this->hasMany(PhotographerBooking::class);
    // }
}
