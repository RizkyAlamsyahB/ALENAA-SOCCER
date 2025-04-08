<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\FieldBooking;
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
        'status', // scheduled, completed, cancelled
        'session_number', // 1, 2, 3 untuk identifikasi urutan sesi
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

    /**
     * Mendapatkan status yang ditampilkan, menampilkan 'upcoming'
     * untuk sesi yang terjadwal dan belum lewat
     */
    public function getDisplayStatusAttribute()
    {
        if ($this->status === 'scheduled' &&
            $this->start_time > Carbon::now() &&
            $this->start_time < Carbon::now()->addDays(7)) {
            return 'upcoming';
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
}
