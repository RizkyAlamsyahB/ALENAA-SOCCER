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
        'renewal_status', // not_due, renewal_pending, renewed
        'next_invoice_date', // tanggal pengiriman invoice berikutnya
        'last_payment_date', // tanggal pembayaran terakhir
        'next_period_bookings', // booking yang sudah dilakukan untuk periode berikutnya
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'price' => 'integer',
        'invoice_sent' => 'boolean',
        'next_invoice_date' => 'datetime',
        'last_payment_date' => 'datetime',
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
