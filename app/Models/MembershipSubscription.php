<?php

namespace App\Models;

use App\Models\User;
use App\Models\Payment;
use App\Models\Membership;
use App\Models\MembershipSession;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MembershipSubscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'membership_id',
        'payment_id',
        'price',
        'status', // pending, active, expired, cancelled
        'start_date',
        'end_date',
        'invoice_sent', // flag untuk menandai apakah invoice sudah dikirim
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'price' => 'integer',
        'invoice_sent' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function membership()
    {
        return $this->belongsTo(Membership::class);
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function sessions()
    {
        return $this->hasMany(MembershipSession::class);
    }
}
