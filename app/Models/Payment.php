<?php
namespace App\Models;

use App\Models\User;
use App\Models\FieldBooking;
use App\Models\Discount;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'user_id',
        'amount',
        'original_amount',
        'discount_id',
        'point_redemption_id',  // Tambahkan ini
        'discount_amount',
        'transaction_id',
        'transaction_status',
        'transaction_time',
        'payment_type',
        'payment_details',
        'expires_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'original_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'transaction_time' => 'datetime',
        'expires_at' => 'datetime',
    ];

    /**
     * Get the user that made the payment.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the discount that was applied to this payment.
     */
    public function discount(): BelongsTo
    {
        return $this->belongsTo(Discount::class);
    }

    /**
     * Get the field bookings associated with this payment.
     */
    public function fieldBookings(): HasMany
    {
        return $this->hasMany(FieldBooking::class);
    }

    /**
     * Get the rental bookings associated with this payment.
     */
    public function rentalBookings(): HasMany
    {
        return $this->hasMany(RentalBooking::class);
    }

    /**
     * Get the membership subscriptions associated with this payment.
     */
    public function membershipSubscriptions(): HasMany
    {
        return $this->hasMany(MembershipSubscription::class);
    }

    /**
     * Get the photographer bookings associated with this payment.
     */
    public function photographerBookings(): HasMany
    {
        return $this->hasMany(PhotographerBooking::class);
    }

    /**
     * Check if payment is pending.
     */
    public function isPending(): bool
    {
        return $this->transaction_status === 'pending';
    }

    /**
     * Check if payment is success.
     */
    public function isSuccess(): bool
    {
        return $this->transaction_status === 'success';
    }

    /**
     * Check if payment is failed.
     */
    public function isFailed(): bool
    {
        return $this->transaction_status === 'failed';
    }

    /**
     * Check if payment is for membership renewal.
     */
    public function isRenewalPayment(): bool
    {
        return $this->payment_type === 'membership_renewal' || strpos($this->order_id, 'RENEW-MEM-') === 0;
    }
}
