<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PhotographerBooking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'photographer_id',
        'payment_id',
        'membership_session_id',
        'field_booking_id', // Tambahkan field ini juga
        'start_time',
        'end_time',
        'price',
        'status',
        'notes',
        'cancellation_reason',
        'photo_gallery_link',
        'photographer_notes',
        'completed_at',
        'completion_status',
        'is_membership' // Tambahkan field ini juga
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'completed_at' => 'datetime',
        'price' => 'float',
    ];

    /**
     * Status yang tersedia untuk booking
     */
    const STATUSES = [
        'pending' => 'Menunggu Konfirmasi',
        'confirmed' => 'Dikonfirmasi',
        'completed' => 'Selesai',
        'cancelled' => 'Dibatalkan'
    ];

    /**
     * Status completion yang tersedia
     */
    const COMPLETION_STATUSES = [
        'pending' => 'Belum Dimulai',
        'confirmed' => 'Dikonfirmasi',
        'shooting_completed' => 'Pemotretan Selesai',
        'delivered' => 'Sudah Dikirim'
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

    /**
     * Cek apakah booking sudah dapat diselesaikan
     */
    public function canBeCompleted()
    {
        return $this->status === 'confirmed' &&
               $this->completion_status !== 'delivered' &&
               $this->start_time <= now();
    }

    /**
     * Cek apakah booking masih bisa dikonfirmasi
     */
    public function canBeConfirmed()
    {
        return $this->status === 'pending' &&
               $this->start_time > now();
    }

    /**
     * Get status label untuk display
     */
    public function getStatusLabel()
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    /**
     * Get completion status label untuk display
     */
    public function getCompletionStatusLabel()
    {
        return self::COMPLETION_STATUSES[$this->completion_status] ?? $this->completion_status;
    }

    /**
     * Scope untuk booking yang akan datang
     */
    public function scopeUpcoming($query)
    {
        return $query->where('start_time', '>', now())
                    ->where('status', '!=', 'cancelled');
    }

    /**
     * Scope untuk booking hari ini
     */
    public function scopeToday($query)
    {
        return $query->whereDate('start_time', today());
    }

    /**
     * Scope untuk booking yang perlu dikonfirmasi
     */
    public function scopePendingConfirmation($query)
    {
        return $query->where('status', 'pending')
                    ->where('start_time', '>', now());
    }

    /**
     * Scope untuk booking yang bisa diselesaikan
     */
    public function scopeCanBeCompleted($query)
    {
        return $query->where('status', 'confirmed')
                    ->where('completion_status', '!=', 'delivered')
                    ->where('start_time', '<=', now());
    }
}
