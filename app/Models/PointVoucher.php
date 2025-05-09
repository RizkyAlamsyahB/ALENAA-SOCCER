<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\PointRedemption;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PointVoucher extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'code',
        'name',
        'description',
        'discount_type',
        'discount_value',
        'points_required',
        'min_order',
        'max_discount',
        'applicable_to',
        'usage_limit',
        'start_date',
        'end_date',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'discount_value' => 'decimal:2',
        'min_order' => 'decimal:2',
        'max_discount' => 'decimal:2',
        'points_required' => 'integer',
        'usage_limit' => 'integer',
        'is_active' => 'boolean',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    protected $dates = ['created_at', 'updated_at', 'deleted_at'];


    /**
     * Get the redemptions for this voucher.
     */
    public function redemptions()
    {
        return $this->hasMany(PointRedemption::class);
    }

    /**
     * Check if the voucher is valid.
     *
     * @return bool
     */
    public function isValid()
    {
        // Check if active
        if (!$this->is_active) {
            return false;
        }

        // Check start date
        if ($this->start_date && Carbon::parse($this->start_date)->isFuture()) {
            return false;
        }

        // Check end date
        if ($this->end_date && Carbon::parse($this->end_date)->isPast()) {
            return false;
        }

        // Check usage limit
        if ($this->usage_limit !== null) {
            $usageCount = $this->redemptions()->count();
            if ($usageCount >= $this->usage_limit) {
                return false;
            }
        }

        return true;
    }

    /**
     * Calculate discount amount
     *
     * @param float $amount
     * @return float
     */
    public function calculateDiscount($amount)
    {
        Log::info('Point Voucher Discount Calculation', [
            'amount' => $amount,
            'min_order' => $this->min_order,
            'discount_type' => $this->discount_type,
            'discount_value' => $this->discount_value,
            'max_discount' => $this->max_discount
        ]);

        if ($amount < $this->min_order) {
            Log::info('Discount not applied: Amount below minimum order');
            return 0;
        }

        $discount = 0;
        if ($this->discount_type === 'percentage') {
            $discount = ($amount * $this->discount_value) / 100;

            if ($this->max_discount && $discount > $this->max_discount) {
                Log::info('Discount capped at max_discount', [
                    'original_discount' => $discount,
                    'max_discount' => $this->max_discount
                ]);
                $discount = $this->max_discount;
            }
        } else {
            $discount = $this->discount_value;
        }

        Log::info('Final Discount Amount', [
            'discount' => $discount
        ]);

        return $discount;
    }
}
