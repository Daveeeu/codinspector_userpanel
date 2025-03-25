<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Commission extends Model
{

    protected $fillable = ['referral_id', 'amount', 'earned_at'];

    public function referral()
    {
        return $this->belongsTo(Referral::class);
    }
}
