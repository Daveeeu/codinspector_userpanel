<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BillingInfo extends Model
{
    protected $table = 'billing_info';
    protected $primaryKey = 'billing_id';
    protected $fillable = [
        'user_id',
        'company_name',
        'tax_id',
        'address',
        'city',
        'postal_code',
        'country',
        'next_package_id',
        'subscription_schedule_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
