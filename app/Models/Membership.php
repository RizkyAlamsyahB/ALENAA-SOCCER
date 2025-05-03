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
        'field_id',
        'name',
        'type',
        'price',
        'description',
        'sessions_per_week',
        'session_duration',
        'photographer_duration',
        'status',
        'image',
        'includes_photographer',
        'photographer_id',
        'includes_rental_item',
        'rental_item_id',
        'rental_item_quantity',
    ];

    protected $casts = [
        'price' => 'float',
        'sessions_per_week' => 'integer',
        'session_duration' => 'integer',
        'photographer_duration' => 'integer',
        'includes_photographer' => 'boolean',
        'includes_rental_item' => 'boolean',
        'rental_item_quantity' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function field()
    {
        return $this->belongsTo(Field::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(MembershipSubscription::class);
    }

      /**
     * Get the rental item associated with the membership.
     */
    public function rentalItem()
    {
        return $this->belongsTo(RentalItem::class, 'rental_item_id');
    }
    // Tambahkan metode ini di class Membership
public function photographer()
{
    return $this->belongsTo(Photographer::class);
}

    /**
     * Get rental item total value
     */
    public function getRentalItemTotalValueAttribute()
    {
        if ($this->includes_rental_item && $this->rental_item_id && $this->rentalItem) {
            return $this->rentalItem->rental_price * $this->rental_item_quantity;
        }

        return 0;
    }

    /**
     * Get total duration per week
     */
    public function getTotalDurationAttribute()
    {
        return $this->sessions_per_week * $this->session_duration;
    }

    /**
     * Get membership type badge class
     */
    public function getTypeBadgeClassAttribute()
    {
        switch ($this->type) {
            case 'bronze':
                return 'bg-secondary';
            case 'silver':
                return 'bg-light text-dark';
            case 'gold':
                return 'bg-warning';
            case 'platinum':
                return 'bg-info';
            default:
                return 'bg-primary';
        }
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeClassAttribute()
    {
        return $this->status === 'active' ? 'bg-success' : 'bg-danger';
    }

    /**
     * Get formatted price
     */
    public function getFormattedPriceAttribute()
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }

}
