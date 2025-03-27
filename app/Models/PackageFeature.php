<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackageFeature extends Model
{
    use HasFactory;

    /**
     * A tömegesen feltölthető attribútumok.
     *
     * @var array
     */
    protected $fillable = [
        'package_id',
        'name',
        'is_included',
    ];

    /**
     * Az attribútumok alapértelmezett értékei.
     *
     * @var array
     */
    protected $attributes = [
        'is_included' => true,
    ];

    /**
     * Az attribútumok típusai.
     *
     * @var array
     */
    protected $casts = [
        'is_included' => 'boolean',
    ];

    /**
     * Get the package that owns the feature.
     */
    public function package()
    {
        return $this->belongsTo(Package::class, 'package_id', 'package_id');
    }
}
