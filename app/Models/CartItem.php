<?php

namespace App\Models;

use App\Models\Cart;
use App\Models\User;
use App\Models\Field;
use App\Models\Product;
use App\Models\Membership;
use App\Models\RentalItem;
use App\Models\Photographer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CartItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'cart_id',
        'type',
        'item_id',
        'start_time',
        'end_time',
        'quantity',
        'price',
        'membership_sessions',
        'payment_period',
        'notes',
        'customer_id', // untuk POS
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    /**
     * Relasi ke Cart.
     */
    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    /**
     * Relasi ke User (sebagai customer).
     */
    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    /**
     * Relasi ke Field (jika type = field_booking).
     */
    public function field()
    {
        if ($this->type === 'field_booking') {
            return $this->belongsTo(Field::class, 'item_id');
        }
        return null;
    }

    /**
     * Relasi ke RentalItem (jika type = rental_item).
     */
    public function rentalItem()
    {
        if ($this->type === 'rental_item') {
            return $this->belongsTo(RentalItem::class, 'item_id');
        }
        return null;
    }

    /**
     * Relasi ke Membership (jika type = membership).
     */
    public function membership()
    {
        if ($this->type === 'membership') {
            return $this->belongsTo(Membership::class, 'item_id');
        }
        return null;
    }

    /**
     * Relasi ke Photographer (jika type = photographer).
     */
    public function photographer()
    {
        if ($this->type === 'photographer') {
            return $this->belongsTo(Photographer::class, 'item_id');
        }
        return null;
    }

    /**
     * Relasi ke Product (jika type = product).
     */
    public function product()
    {
        if ($this->type === 'product') {
            return $this->belongsTo(Product::class, 'item_id');
        }
        return null;
    }

    /**
     * Mendapatkan model relasi berdasarkan tipe item.
     */
    public function getRelatedItemAttribute()
    {
        return match ($this->type) {
            'field_booking' => $this->field,
            'rental_item' => $this->rentalItem,
            'membership' => $this->membership,
            'photographer' => $this->photographer,
            'product' => $this->product,
            default => null,
        };
    }

    /**
     * Menampilkan nama tipe item dalam bahasa manusia.
     */
    public function getTypeNameAttribute()
    {
        return [
            'field_booking' => 'Booking Lapangan',
            'rental_item' => 'Penyewaan Peralatan',
            'membership' => 'Keanggotaan',
            'photographer' => 'Jasa Fotografer',
            'product' => 'Produk',
        ][$this->type] ?? $this->type;
    }

    /**
     * Scope untuk memfilter berdasarkan tipe.
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }
}
