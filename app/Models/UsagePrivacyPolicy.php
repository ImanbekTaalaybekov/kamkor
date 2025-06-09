<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UsagePrivacyPolicy extends Model
{
    protected $fillable = [
        'title',
        'text',
    ];
}
