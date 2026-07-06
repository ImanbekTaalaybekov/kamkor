<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SosHistory extends Model
{
    protected $casts = [
        'last_location_at' => 'datetime',
        'location_accuracy' => 'float',
    ];

    protected $fillable = [
        'user_id',
        'geo',
        'audio_file',
        'status',
        'location_accuracy',
        'last_location_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
