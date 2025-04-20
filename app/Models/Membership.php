<?php

namespace App\Models;

use App\Models\Field;
use App\Models\RentalItem;
use App\Models\Photographer;
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
        'photographer_duration', // dalam jam
        'status', // active, inactive
        'image',
        'includes_photographer', // apakah termasuk fotografer
        'includes_rental_item', // apakah termasuk rental item
        'rental_item_quantity', // jumlah rental item
    ];

    protected $casts = [
        'price' => 'integer',
        'sessions_per_week' => 'integer',
        'session_duration' => 'integer',
        'photographer_duration' => 'integer',

    ];

    public function field()
    {
        return $this->belongsTo(Field::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(MembershipSubscription::class);
    }
    // Tambahkan metode ini di class Membership
public function photographer()
{
    return $this->belongsTo(Photographer::class);
}

public function rentalItem()
{
    return $this->belongsTo(RentalItem::class);
}
}
