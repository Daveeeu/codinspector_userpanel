<?php

namespace App\Models;

use App\Notifications\CustomResetPassword;
use App\Notifications\CustomVerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Cashier\Billable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, Billable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'phone_number',
        'accepted_privacy_policy',
        'accepted_terms_of_service',
        'two_factor_code',
        'two_factor_expires_at',
        'two_factor_enabled',
        'stripe_customer_id',
    ];


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];


    public function stores()
    {
        return $this->hasMany(Store::class);
    }

    public function storeFeedbacks()
    {
        return $this->hasManyThrough(Feedback::class, Store::class,'user_id', 'store_id', 'id', 'store_id');
    }

    public function notifications()
    {
        return $this->hasMany(Notifications::class, 'user_id', 'id');

    }

    public function userNotifications()
    {
        return $this->hasMany(UserNotification::class, 'user_id', 'id');

    }

    public function partnerRequest()
    {
        return $this->hasOne(PartnerRequest::class, 'user_id', 'id')->where('status','approved');
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new CustomResetPassword($token));

        activity()
            ->causedBy($this)
            ->withProperties([
                'email' => $this->email,
                'user_id' => $this->id,
                'requested_at' => now()->toDateTimeString(),
            ])
            ->log('Password reset request sent.');
    }

    public function sendEmailVerificationNotification()
    {
        $this->notify(new CustomVerifyEmail);

        activity()
            ->causedBy($this)
            ->withProperties([
                'email' => $this->email,
                'user_id' => $this->id,
                'requested_at' => now()->toDateTimeString(),
            ])
            ->log('Email verification request sent.');
    }
}
