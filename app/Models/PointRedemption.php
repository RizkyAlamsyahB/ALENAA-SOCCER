<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PointRedemption extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'point_voucher_id',
        'points_used',
        'discount_code',
        'status',
        'used_at',
        'expires_at',
        'payment_id'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'points_used' => 'integer',
        'used_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    /**
     * Get the user who redeemed the voucher.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the voucher that was redeemed.
     */
    public function pointVoucher()
    {
        return $this->belongsTo(PointVoucher::class);
    }

    /**
     * Get the payment where the voucher was used.
     */
    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    /**
     * Check if the redemption is expired.
     *
     * @return bool
     */
    public function isExpired()
    {
        return $this->status === 'expired' ||
               ($this->expires_at && Carbon::parse($this->expires_at)->isPast());
    }

    /**
     * Check if the redemption is active.
     *
     * @return bool
     */
    public function isActive()
    {
        return $this->status === 'active' &&
               (!$this->expires_at || Carbon::parse($this->expires_at)->isFuture());
    }

    /**
     * Mark this redemption as used.
     *
     * @param int $paymentId
     * @return bool
     */
    public function markAsUsed($paymentId)
    {
        $this->status = 'used';
        $this->used_at = now();
        $this->payment_id = $paymentId;

        return $this->save();
    }

    /**
     * Mark this redemption as expired.
     *
     * @return bool
     */
    public function markAsExpired()
    {
        if ($this->status !== 'used') {
            $this->status = 'expired';
            return $this->save();
        }

        return false;
    }
}
