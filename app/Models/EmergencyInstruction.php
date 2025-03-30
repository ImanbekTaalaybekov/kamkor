<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmergencyInstruction extends Model
{
    protected $fillable = [
        'title',
        'content'
    ];
}
