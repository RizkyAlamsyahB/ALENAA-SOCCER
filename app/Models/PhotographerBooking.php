<?php

namespace App\Models;

use App\Models\User;
use App\Models\Payment;
use App\Models\FieldBooking;
use App\Models\Photographer;
use App\Models\MembershipSession;
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
        'membership_session_id',
        'is_membership',
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

    /**
     * Relasi dengan sesi membership
     */
    public function membershipSession()
    {
        return $this->belongsTo(MembershipSession::class);
    }

    /**
     * Mendapatkan field yang terkait melalui fotografer
     */
    public function getField()
    {
        if ($this->photographer) {
            return Field::where('photographer_id', $this->photographer->id)->first();
        }

        return null;
    }

    /**
     * Mendapatkan booking lapangan yang terkait berdasarkan waktu dan lapangan
     */
    public function getRelatedFieldBooking()
    {
        $field = $this->getField();

        if (!$field) {
            return null;
        }

        return FieldBooking::where('field_id', $field->id)
            ->where('user_id', $this->user_id)
            ->where('start_time', '<=', $this->start_time)
            ->where('end_time', '>=', $this->end_time)
            ->where('status', '!=', 'cancelled')
            ->first();
    }
}
