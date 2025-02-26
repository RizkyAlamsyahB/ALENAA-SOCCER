<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhotoPackage extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
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
        'is_active'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'price' => 'integer',
        'duration_minutes' => 'integer',
        'number_of_photos' => 'integer',
        'includes_editing' => 'boolean',
        'is_active' => 'boolean'
    ];

    /**
     * Format duration in a human-readable format
     *
     * @return string
     */
    public function formatDuration()
    {
        $hours = floor($this->duration_minutes / 60);
        $minutes = $this->duration_minutes % 60;

        if ($hours > 0 && $minutes > 0) {
            return "{$hours} hour(s) and {$minutes} minute(s)";
        } elseif ($hours > 0) {
            return "{$hours} hour(s)";
        } else {
            return "{$minutes} minute(s)";
        }
    }
}
