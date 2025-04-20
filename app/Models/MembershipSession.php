<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\FieldBooking;
use App\Models\RentalBooking;
use App\Models\PhotographerBooking;
use App\Models\MembershipSubscription;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MembershipSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'membership_subscription_id',
        'session_date',
        'start_time',
        'end_time',
        'status', // scheduled, upcoming, ongoing, completed, cancelled
        'session_number',
    ];

    protected $casts = [
        'session_date' => 'date',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'session_number' => 'integer',
    ];

    protected $appends = ['display_status'];

    public function subscription()
    {
        return $this->belongsTo(MembershipSubscription::class, 'membership_subscription_id');
    }

    public function fieldBooking()
    {
        return $this->hasOne(FieldBooking::class);
    }
    // Tambahkan metode ini di class MembershipSession
public function photographerBookings()
{
    return $this->hasMany(PhotographerBooking::class);
}

public function rentalBookings()
{
    return $this->hasMany(RentalBooking::class);
}

    /**
     * Mendapatkan status yang ditampilkan:
     * - upcoming: sesi akan dimulai dalam 7 hari ke depan
     * - ongoing: sesi sedang berlangsung sekarang
     */
    public function getDisplayStatusAttribute()
    {
        $now = Carbon::now();

        if ($this->status === 'scheduled') {
            if ($this->start_time <= $now && $this->end_time >= $now) {
                return 'ongoing';
            }

            if ($this->start_time > $now && $this->start_time < $now->copy()->addDays(7)) {
                return 'upcoming';
            }
        }

        return $this->status;
    }

    /**
     * Scope untuk sesi yang akan datang
     */
    public function scopeUpcoming($query)
    {
        return $query->where('status', 'scheduled')
                     ->where('start_time', '>', Carbon::now())
                     ->where('start_time', '<', Carbon::now()->addDays(7))
                     ->orderBy('start_time', 'asc');
    }

    /**
     * Scope untuk sesi yang sedang berlangsung
     */
    public function scopeOngoing($query)
    {
        $now = Carbon::now();
        return $query->where('status', 'scheduled')
                     ->where('start_time', '<=', $now)
                     ->where('end_time', '>=', $now);
    }
}
