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
        'created_by_admin_id',
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
        'access_link_token',
        'access_link_created_at',
        'kamkor_sync_status',
        'kamkor_sync_error',
        'kamkor_last_synced_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',
        'access_link_created_at' => 'datetime',
        'kamkor_last_synced_at' => 'datetime',
    ];

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    public function createdByAdmin()
    {
        return $this->belongsTo(AdminUser::class, 'created_by_admin_id');
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
