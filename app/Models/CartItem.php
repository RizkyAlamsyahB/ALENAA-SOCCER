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

    // =========================================
    // RELATIONSHIPS
    // =========================================

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
        return $this->belongsTo(Field::class, 'item_id');
    }

    /**
     * Relasi ke RentalItem (jika type = rental_item).
     */
    public function rentalItem()
    {
        return $this->belongsTo(RentalItem::class, 'item_id');
    }

    /**
     * Relasi ke Membership (jika type = membership).
     */
    public function membership()
    {
        return $this->belongsTo(Membership::class, 'item_id');
    }

    /**
     * Relasi ke Photographer (jika type = photographer).
     */
    public function photographer()
    {
        return $this->belongsTo(Photographer::class, 'item_id');
    }

    /**
     * Relasi ke Product (jika type = product).
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'item_id');
    }

    // =========================================
    // MAIN ACCESSORS
    // =========================================

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
     * Mendapatkan nama item berdasarkan tipe (NAMA SPESIFIK PRODUK)
     */
    public function getNameAttribute()
    {
        return match ($this->type) {
            'field_booking' => $this->field?->name ?? 'Booking Lapangan',
            'rental_item' => $this->rentalItem?->name ?? 'Penyewaan Peralatan',
            'membership' => $this->membership?->name ?? 'Keanggotaan',
            'photographer' => $this->photographer?->name ?? 'Jasa Fotografer',
            'product' => $this->product?->name ?? 'Produk',
            default => 'Item',
        };
    }

    /**
     * Mendapatkan gambar item berdasarkan tipe
     */
    public function getImageAttribute()
    {
        return match ($this->type) {
            'field_booking' => $this->field?->image,
            'rental_item' => $this->rentalItem?->image,
            'membership' => $this->membership?->image,
            'photographer' => $this->photographer?->image,
            'product' => $this->product?->image,
            default => null,
        };
    }

    // =========================================
    // DISPLAY & FORMATTING ACCESSORS
    // =========================================

    /**
     * Mendapatkan kategori simple untuk badge utama
     */
    public function getCategoryAttribute()
    {
        return match ($this->type) {
            'field_booking' => 'Lapangan',
            'rental_item' => 'Rental',
            'membership' => 'Member',
            'photographer' => 'Foto',
            'product' => 'Produk',
            default => 'Item',
        };
    }

    /**
     * Mendapatkan CSS class untuk badge kategori
     */
    public function getCategoryClassAttribute()
    {
        return strtolower($this->category);
    }

    /**
     * Mendapatkan nama tipe item dalam bahasa manusia (LENGKAP)
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
     * Mendapatkan tanggal yang sudah diformat
     */
    public function getFormattedDateAttribute()
    {
        if ($this->start_time) {
            return \Carbon\Carbon::parse($this->start_time)->format('d M Y');
        }
        return null;
    }

    /**
     * Mendapatkan deskripsi atau detail tambahan
     */
    public function getDetailsAttribute()
    {
        return match ($this->type) {
            'field_booking' => 'Booking lapangan futsal',
            'rental_item' => 'Penyewaan peralatan olahraga',
            'membership' => $this->membership ? "Paket {$this->membership->type}" : 'Paket membership',
            'photographer' => 'Jasa dokumentasi olahraga',
            'product' => $this->product?->description ?? 'Produk olahraga',
            default => 'Item booking',
        };
    }

    // =========================================
    // MEMBERSHIP SPECIFIC ACCESSORS
    // =========================================

    /**
     * Mendapatkan membership type dengan CSS class (untuk badge khusus)
     */
    public function getMembershipTypeWithClassAttribute()
    {
        if ($this->type === 'membership' && $this->membership && $this->membership->type) {
            $type = strtolower($this->membership->type);
            return [
                'name' => ucfirst($this->membership->type),
                'class' => "membership-type {$type}"
            ];
        }
        return null;
    }

    /**
     * Mendapatkan periode pembayaran membership yang sudah diformat
     */
    public function getFormattedPaymentPeriodAttribute()
    {
        if ($this->type === 'membership') {
            $period = $this->payment_period ?? 'weekly';
            return $period === 'monthly' ? 'Bulanan (4 Minggu)' : 'Mingguan';
        }
        return null;
    }

    // =========================================
    // TIME & DATE ACCESSORS
    // =========================================

    /**
     * Mendapatkan waktu mulai yang sudah diformat
     */
    public function getFormattedStartTimeAttribute()
    {
        if ($this->start_time) {
            return \Carbon\Carbon::parse($this->start_time)->format('H:i');
        }
        return null;
    }

    /**
     * Mendapatkan waktu selesai yang sudah diformat
     */
    public function getFormattedEndTimeAttribute()
    {
        if ($this->end_time) {
            return \Carbon\Carbon::parse($this->end_time)->format('H:i');
        }
        return null;
    }

    /**
     * Mendapatkan range waktu yang sudah diformat
     */
    public function getFormattedTimeRangeAttribute()
    {
        if ($this->start_time && $this->end_time) {
            return $this->formatted_start_time . ' - ' . $this->formatted_end_time;
        }
        return null;
    }

    /**
     * Mendapatkan datetime lengkap yang sudah diformat
     */
    public function getFormattedDateTimeAttribute()
    {
        if ($this->start_time) {
            return \Carbon\Carbon::parse($this->start_time)->format('d M Y H:i');
        }
        return null;
    }

    // =========================================
    // PRICE & QUANTITY ACCESSORS
    // =========================================

    /**
     * Mendapatkan harga yang sudah diformat
     */
    public function getFormattedPriceAttribute()
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }

    /**
     * Mendapatkan harga per unit untuk rental item
     */
    public function getPricePerUnitAttribute()
    {
        if ($this->type === 'rental_item' && $this->quantity > 0) {
            return $this->price / $this->quantity;
        }
        return $this->price;
    }

    /**
     * Mendapatkan harga per unit yang sudah diformat
     */
    public function getFormattedPricePerUnitAttribute()
    {
        return 'Rp ' . number_format($this->price_per_unit, 0, ',', '.');
    }

    // =========================================
    // BOOLEAN CHECKS
    // =========================================

    /**
     * Cek apakah item memiliki waktu booking
     */
    public function getHasTimeSlotAttribute()
    {
        return in_array($this->type, ['field_booking', 'rental_item', 'photographer']);
    }

    /**
     * Cek apakah item bisa diedit quantity
     */
    public function getIsQuantityEditableAttribute()
    {
        return $this->type === 'rental_item';
    }

    /**
     * Cek apakah item sudah expired (waktu sudah lewat)
     */
    public function getIsExpiredAttribute()
    {
        if ($this->has_time_slot && $this->end_time) {
            return \Carbon\Carbon::parse($this->end_time)->isPast();
        }
        return false;
    }

    // =========================================
    // SCOPES
    // =========================================

    /**
     * Scope untuk memfilter berdasarkan tipe.
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope untuk item yang belum expired
     */
    public function scopeNotExpired($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('end_time')
              ->orWhere('end_time', '>', now());
        });
    }

    /**
     * Scope untuk item yang expired
     */
    public function scopeExpired($query)
    {
        return $query->whereNotNull('end_time')
                    ->where('end_time', '<=', now());
    }

    /**
     * Scope dengan eager loading untuk semua relasi
     */
    public function scopeWithRelations($query)
    {
        return $query->with([
            'field',
            'rentalItem',
            'membership',
            'photographer',
            'product'
        ]);
    }
}
