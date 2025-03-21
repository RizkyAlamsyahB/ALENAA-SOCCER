<?php

namespace App\Models;

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

    public function subscription()
    {
        return $this->belongsTo(MembershipSubscription::class, 'membership_subscription_id');
    }

    public function fieldBooking()
{
    return $this->hasOne(FieldBooking::class);
}
}
