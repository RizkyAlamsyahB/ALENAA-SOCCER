<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PointsTransaction extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'type',
        'amount',
        'description',
        'reference_type',
        'reference_id',
        'metadata'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'integer',
        'metadata' => 'json',
    ];

    /**
     * Get the user for this transaction.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the reference model (polymorphic).
     */
    public function reference()
    {
        return $this->morphTo(__FUNCTION__, 'reference_type', 'reference_id');
    }

    /**
     * Create a transaction for earning points.
     *
     * @param int $userId
     * @param int $points
     * @param string $description
     * @param string $referenceType
     * @param int $referenceId
     * @param array $metadata
     * @return PointsTransaction
     */
    public static function createEarnTransaction($userId, $points, $description, $referenceType, $referenceId, $metadata = [])
    {
        return self::create([
            'user_id' => $userId,
            'type' => 'earn',
            'amount' => $points,
            'description' => $description,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'metadata' => $metadata
        ]);
    }

    /**
     * Create a transaction for redeeming points.
     *
     * @param int $userId
     * @param int $points
     * @param string $description
     * @param string $referenceType
     * @param int $referenceId
     * @param array $metadata
     * @return PointsTransaction
     */
    public static function createRedeemTransaction($userId, $points, $description, $referenceType, $referenceId, $metadata = [])
    {
        return self::create([
            'user_id' => $userId,
            'type' => 'redeem',
            'amount' => -abs($points), // Always negative for redemptions
            'description' => $description,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'metadata' => $metadata
        ]);
    }

    /**
     * Create a transaction for expired points.
     *
     * @param int $userId
     * @param int $points
     * @param string $description
     * @param string $referenceType
     * @param int $referenceId
     * @param array $metadata
     * @return PointsTransaction
     */
    public static function createExpiredTransaction($userId, $points, $description, $referenceType, $referenceId, $metadata = [])
    {
        return self::create([
            'user_id' => $userId,
            'type' => 'expired',
            'amount' => -abs($points), // Always negative for expirations
            'description' => $description,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'metadata' => $metadata
        ]);
    }

    /**
     * Create a transaction for administrative points adjustment.
     *
     * @param int $userId
     * @param int $points
     * @param string $description
     * @param array $metadata
     * @return PointsTransaction
     */
    public static function createAdminTransaction($userId, $points, $description, $metadata = [])
    {
        return self::create([
            'user_id' => $userId,
            'type' => 'admin',
            'amount' => $points, // Can be positive or negative
            'description' => $description,
            'reference_type' => 'App\\Models\\User',
            'reference_id' => $userId,
            'metadata' => $metadata
        ]);
    }
}
