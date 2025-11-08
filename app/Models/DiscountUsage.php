<?php

namespace App\Models;

use App\Models\User;
use App\Models\Payment;
use App\Models\Discount;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DiscountUsage extends Model
{
    use HasFactory;

    protected $fillable = [
        'discount_id',
        'user_id',
        'payment_id',
        'discount_amount',
    ];

    /**
     * Relasi ke diskon
     */
    public function discount()
    {
        return $this->belongsTo(Discount::class);
    }

    /**
     * Relasi ke user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke payment
     */
    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }
}
