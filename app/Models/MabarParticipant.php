<?php

namespace App\Models;

use App\Models\User;
use App\Models\OpenMabar;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MabarParticipant extends Model
{
    use HasFactory;

    protected $table = 'mabar_participants';

    protected $fillable = [
        'open_mabar_id',
        'user_id',
        'status',
        'payment_status',
        'payment_method',
        'amount_paid',
    ];

    protected $casts = [
        'amount_paid' => 'decimal:2',
    ];

    /**
     * Get the open mabar that the participant belongs to.
     */
    public function openMabar()
    {
        return $this->belongsTo(OpenMabar::class, 'open_mabar_id');
    }

    /**
     * Get the user associated with the participant.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Check if the participant has joined and not cancelled.
     */
    public function isActive()
    {
        return $this->status == 'joined' || $this->status == 'attended';
    }

    /**
     * Check if the participant has paid.
     */
    public function hasPaid()
    {
        return $this->payment_status == 'paid';
    }
}
