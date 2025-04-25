<?php

namespace App\Models;

use App\Models\ProductSale;
use App\Models\FieldBooking;
use App\Models\RentalBooking;
use App\Models\PhotographerBooking;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone_number',
        'notes'
    ];

    // Relasi ke booking lapangan
    public function fieldBookings()
    {
        return $this->hasMany(FieldBooking::class);
    }

    // Relasi ke booking rental
    public function rentalBookings()
    {
        return $this->hasMany(RentalBooking::class);
    }

    // Relasi ke booking fotografer
    public function photographerBookings()
    {
        return $this->hasMany(PhotographerBooking::class);
    }

    // Relasi ke pembelian produk
    public function productSales()
    {
        return $this->hasMany(ProductSale::class);
    }
}
