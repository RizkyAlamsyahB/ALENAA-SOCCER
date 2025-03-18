<?php

namespace App\Models;

use App\Models\Field;
use App\Models\MembershipSubscription;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Membership extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'field_id',
        'type', // bronze, silver, gold
        'price',
        'description',
        'sessions_per_week', // jumlah sesi per minggu (default 3)
        'session_duration', // dalam jam (1, 2, atau 3 jam)
        'includes_ball',
        'includes_water',
        'includes_photographer',
        'photographer_duration', // dalam jam
        'status', // active, inactive
        'image',
    ];

    protected $casts = [
        'price' => 'integer',
        'sessions_per_week' => 'integer',
        'session_duration' => 'integer',
        'photographer_duration' => 'integer',
        'includes_ball' => 'boolean',
        'includes_water' => 'boolean',
        'includes_photographer' => 'boolean',
    ];

    public function field()
    {
        return $this->belongsTo(Field::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(MembershipSubscription::class);
    }
}
