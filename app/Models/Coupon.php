<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code', 'type', 'value',
        'max_uses', 'max_uses_per_user', 'start_date',
        'end_date', 'status',
    ];

    public function userCoupons()
    {
        return $this->hasMany(UserCoupon::class);
    }
}
