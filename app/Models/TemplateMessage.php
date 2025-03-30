<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TemplateMessage extends Model
{
    protected $fillable = [
        'user_id',
        'message_text',
        'geo_signature'
    ];
}
