<?php

namespace App\Models;

use App\Models\User;
use App\Models\Discount;
use App\Models\FieldBooking;
use App\Models\RentalBooking;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
        'expires_at',
        'discount_id',       // Tambahkan ini
        'discount_amount',   // Tambahkan ini
        'original_amount' 
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
 * Relasi ke diskon
 */
public function discount()
{
    return $this->belongsTo(Discount::class);
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
