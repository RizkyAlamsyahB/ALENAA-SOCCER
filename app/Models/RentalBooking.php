<?php

namespace App\Models;

use App\Models\User;
use App\Models\Payment;
use App\Models\RentalItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RentalBooking extends Model
{
    use HasFactory;

    /**
     * Atribut yang dapat diisi secara massal
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'rental_item_id',
        'payment_id',
        'start_time',
        'end_time',
        'quantity',
        'total_price',
        'status',
    ];

    /**
     * Atribut yang harus dikonversi ke tipe data tertentu
     *
     * @var array
     */
    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'quantity' => 'integer',
        'total_price' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relasi ke model User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke model RentalItem
     */
    public function rentalItem()
    {
        return $this->belongsTo(RentalItem::class);
    }

    /**
     * Relasi ke model Payment
     */
    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    /**
     * Mendapatkan durasi rental dalam jam
     */
    public function getDurationInHoursAttribute()
    {
        if (!$this->start_time || !$this->end_time) {
            return 0;
        }

        return $this->start_time->diffInHours($this->end_time);
    }

    /**
     * Mendapatkan durasi rental dalam format terbaca
     */
    public function getFormattedDurationAttribute()
    {
        $hours = $this->duration_in_hours;

        if ($hours < 1) {
            return 'Kurang dari 1 jam';
        } elseif ($hours == 1) {
            return '1 jam';
        } else {
            return $hours . ' jam';
        }
    }

    /**
     * Cek apakah booking masih aktif (belum dibatalkan atau selesai)
     */
    public function getIsActiveAttribute()
    {
        return !in_array($this->status, ['cancelled', 'completed']);
    }

    /**
     * Mendapatkan status dalam format terbaca
     */
    public function getFormattedStatusAttribute()
    {
        switch ($this->status) {
            case 'pending':
                return 'Menunggu Konfirmasi';
            case 'confirmed':
                return 'Terkonfirmasi';
            case 'cancelled':
                return 'Dibatalkan';
            case 'completed':
                return 'Selesai';
            default:
                return ucfirst($this->status);
        }
    }

    /**
     * Scope untuk mendapatkan booking pada rentang waktu tertentu
     */
    public function scopeOverlappingWith($query, $startTime, $endTime, $exceptId = null)
    {
        $query->where(function ($q) use ($startTime, $endTime) {
            // Booking dimulai dalam rentang waktu
            $q->where(function ($subq) use ($startTime, $endTime) {
                $subq->where('start_time', '>=', $startTime)
                     ->where('start_time', '<', $endTime);
            })
            // Booking berakhir dalam rentang waktu
            ->orWhere(function ($subq) use ($startTime, $endTime) {
                $subq->where('end_time', '>', $startTime)
                     ->where('end_time', '<=', $endTime);
            })
            // Booking mencakup seluruh rentang waktu
            ->orWhere(function ($subq) use ($startTime, $endTime) {
                $subq->where('start_time', '<=', $startTime)
                     ->where('end_time', '>=', $endTime);
            });
        });

        // Kecualikan ID tertentu jika diberikan
        if ($exceptId) {
            $query->where('id', '!=', $exceptId);
        }

        return $query;
    }

    /**
     * Scope untuk mendapatkan booking yang aktif
     */
    public function scopeActive($query)
    {
        return $query->whereNotIn('status', ['cancelled', 'completed']);
    }
}
