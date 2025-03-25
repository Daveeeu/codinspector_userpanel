<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $primaryKey = "subscription_id";
    protected $fillable = [
        'user_id',
        'package_id',
        'price',
        'start_date',
        'end_date',
        'auto_renewal',
        'status',
        'stripe_subscription_id',
        'payment_frequency',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function package()
    {
        return $this->belongsTo(Package::class, 'package_id', 'package_id');
    }

    public function store()
    {
        return $this->hasOne(Store::class, 'subscription_id', 'subscription_id');
    }

}
