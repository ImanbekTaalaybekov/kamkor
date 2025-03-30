<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrustedContacts extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'phone_number'
    ];
}
