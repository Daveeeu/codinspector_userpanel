<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Platform extends Model
{
    protected $primaryKey = 'platform_id';

    protected $fillable = [
        'name',
        'description',
        'video_url'
    ];
}
