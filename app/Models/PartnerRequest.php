<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PartnerRequest extends Model
{
    protected $fillable = ['user_id', 'status', 'commission_rate', 'validity_days'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
