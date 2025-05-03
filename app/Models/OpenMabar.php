<?php

namespace App\Models;

use App\Models\User;
use App\Models\FieldBooking;
use App\Models\MabarParticipant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OpenMabar extends Model
{
    use HasFactory;

    protected $table = 'open_mabars';

    protected $fillable = [
        'field_booking_id',
        'user_id',
        'title',
        'description',
        'start_time',
        'end_time',
        'price_per_slot',
        'total_slots',
        'filled_slots',
        'status',
        'level',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'price_per_slot' => 'decimal:2',
    ];

    /**
     * Get the field booking associated with the open mabar.
     */
    public function fieldBooking()
    {
        return $this->belongsTo(FieldBooking::class, 'field_booking_id');
    }

    /**
     * Get the user that created the open mabar.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the participants for the open mabar.
     */
    public function participants()
    {
        return $this->hasMany(MabarParticipant::class);
    }

    /**
     * Check if open mabar is full.
     */
    public function isFull()
    {
        return $this->filled_slots >= $this->total_slots;
    }

    /**
     * Check if open mabar has ended.
     */
    public function hasEnded()
    {
        return now() > $this->end_time;
    }

    /**
     * Get the remaining slots.
     */
    public function remainingSlots()
    {
        return max(0, $this->total_slots - $this->filled_slots);
    }

    /**
     * Get the percentage of filled slots.
     */
    public function filledPercentage()
    {
        if ($this->total_slots == 0) return 100;
        return round(($this->filled_slots / $this->total_slots) * 100);
    }
}
