<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhotoPackage extends Model
{
    use HasFactory;

    /**
     * Atribut yang dapat diisi secara massal
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'price',
        'duration_minutes',
        'number_of_photos',
        'includes_editing',
    ];

    /**
     * Atribut yang harus dikonversi ke tipe data tertentu
     *
     * @var array
     */
    protected $casts = [
        'includes_editing' => 'boolean',
        'price' => 'integer',
        'duration_minutes' => 'integer',
        'number_of_photos' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];


    /**
     * Relasi ke model TransactionDetail (jika dibutuhkan nanti)
     */
    // public function transactionDetails()
    // {
    //     return $this->morphMany(TransactionDetail::class, 'item');
    // }

    /**
     * Relasi ke model PhotographerBooking (jika dibutuhkan nanti)
     */
    // public function photographerBookings()
    // {
    //     return $this->hasMany(PhotographerBooking::class);
    // }

    /**
     * Mendapatkan durasi dalam format yang lebih manusiawi
     */
    public function getFormattedDurationAttribute()
    {
        if ($this->duration_minutes >= 60) {
            $hours = floor($this->duration_minutes / 60);
            $minutes = $this->duration_minutes % 60;
            return $hours . ' jam ' . ($minutes > 0 ? $minutes . ' menit' : '');
        }
        return $this->duration_minutes . ' menit';
    }
}
