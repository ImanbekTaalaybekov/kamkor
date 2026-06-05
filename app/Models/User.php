<?php

namespace App\Models;

use App\Notifications\ResetPasswordNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\CanResetPassword;
use Laravel\Sanctum\HasApiTokens;
use App\Models\UvdGuide;

class User extends Authenticatable implements CanResetPassword
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'pin',
        'phone_number',
        'name',
        'surname',
        'password',
        'fcm_token',
        'order_registration_date',
        'region',
        'uvd_code',
        'address',
        'icon',
        'orderNumber',
        'daysRemaining',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',
    ];

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    public function sosHistories()
    {
        return $this->hasMany(SosHistory::class);
    }

    public function uvdGuide()
    {
        return $this->belongsTo(UvdGuide::class, 'uvd_code', 'code');
    }
}
