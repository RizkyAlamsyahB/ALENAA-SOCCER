<?php

namespace App\Models;

use App\Models\PointsTransaction;
use App\Models\PhotographerBooking;
use Illuminate\Notifications\Notifiable;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone_number',
        'address',
        'birthdate',
        'points',
        'profile_picture',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'birthdate' => 'date',
            'points' => 'integer',
        ];
    }

    public function hasRole($role)
    {
        return $this->role === $role;
    }

    public function getRoleNames()
    {
        return collect([$this->role]);
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(FieldBooking::class);
    }

    public function cart(): HasOne
    {
        return $this->hasOne(Cart::class);
    }

    public function reviews()
{
    return $this->hasMany(Review::class);
}
public function pointsTransactions()
{
    return $this->hasMany(PointsTransaction::class);
}

/**
 * Get the photographer bookings for the user.
 */
public function photographerBookings(): HasMany
{
    return $this->hasMany(PhotographerBooking::class);
}
}
