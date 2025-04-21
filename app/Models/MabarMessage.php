<?php

namespace App\Models;

use App\Models\User;
use App\Models\OpenMabar;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MabarMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'open_mabar_id',
        'user_id',
        'message',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function openMabar()
    {
        return $this->belongsTo(OpenMabar::class);
    }
}
