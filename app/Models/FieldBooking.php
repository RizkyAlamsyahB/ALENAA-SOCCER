<?php
namespace App\Models;

use App\Models\User;
use App\Models\Field;
use App\Models\Payment;
use App\Models\RentalBooking;
use App\Models\MembershipSession;
use App\Models\PhotographerBooking;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FieldBooking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'field_id',
        'start_time',
        'end_time',
        'total_price',
        'status',
        'payment_id',
        'is_membership',
        'membership_session_id',
        'reminder_sent_24hours',
        'reminder_sent_1hour',
        'reminder_sent_30minutes'
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'total_price' => 'decimal:2',
        'is_membership' => 'boolean',
    ];

    /**
     * Get the user that owns the booking.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the field that is booked.
     */
    public function field(): BelongsTo
    {
        return $this->belongsTo(Field::class);
    }

    /**
     * Get the payment associated with this booking.
     */
    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    /**
     * Get the membership session associated with this booking (if any).
     */
    public function membershipSession()
    {
        return $this->belongsTo(MembershipSession::class, 'membership_session_id');
    }

    public function photographerBookings()
    {
        return $this->hasMany(PhotographerBooking::class, 'field_booking_id');
    }

    public function rentalBookings()
    {
        return $this->hasMany(RentalBooking::class, 'field_booking_id');
    }

    /**
     * Check if booking is pending.
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if booking is confirmed.
     */
    public function isConfirmed(): bool
    {
        return $this->status === 'confirmed';
    }

    /**
     * Check if booking is cancelled.
     */
    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    /**
     * Check if booking is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Check if this booking is from a membership session.
     */
    public function isMembershipBooking(): bool
    {
        return $this->is_membership === true;
    }
}
