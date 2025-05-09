<?php

namespace App\Models;

use App\Models\Payment;
use App\Models\DiscountUsage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Discount extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'description',
        'type',
        'value',
        'min_order',
        'max_discount',
        'applicable_to',
        'usage_limit',
        'user_usage_limit',
        'start_date',
        'end_date',
        'is_active',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_active' => 'boolean',
    ];
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    /**
     * Relasi ke penggunaan diskon
     */
    public function usages()
    {
        return $this->hasMany(DiscountUsage::class);
    }

    /**
     * Relasi ke payment
     */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Cek apakah diskon masih valid
     */
    public function isValid()
    {
        // Cek apakah diskon aktif
        if (!$this->is_active) {
            return false;
        }

        // Cek tanggal berlaku
        $now = now();
        if ($this->start_date && $now->lt($this->start_date)) {
            return false;
        }
        if ($this->end_date && $now->gt($this->end_date)) {
            return false;
        }

        // Cek batas penggunaan total
        if ($this->usage_limit !== null && $this->usages()->count() >= $this->usage_limit) {
            return false;
        }

        return true;
    }

    /**
     * Cek apakah user masih bisa menggunakan diskon ini
     */
    public function isValidForUser($userId)
    {
        // Cek apakah diskon valid secara umum
        if (!$this->isValid()) {
            return false;
        }

        // Cek batas penggunaan per user
        $userUsageCount = $this->usages()->where('user_id', $userId)->count();
        if ($userUsageCount >= $this->user_usage_limit) {
            return false;
        }

        return true;
    }

    /**
     * Hitung jumlah diskon berdasarkan total harga
     */
    public function calculateDiscount($subtotal)
    {
        // Cek minimal pembelian
        if ($subtotal < $this->min_order) {
            return 0;
        }

        // Hitung diskon
        if ($this->type === 'percentage') {
            $discount = $subtotal * ($this->value / 100);

            // Terapkan maksimum diskon jika ada
            if ($this->max_discount !== null && $discount > $this->max_discount) {
                $discount = $this->max_discount;
            }
        } else { // fixed
            $discount = $this->value;

            // Pastikan diskon tidak melebihi subtotal
            if ($discount > $subtotal) {
                $discount = $subtotal;
            }
        }

        return $discount;
    }
}
