<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Permission\Models\Role;


class Package extends Model
{
    use HasRoles;
    protected $primaryKey = 'package_id';
    protected $guard_name = 'web';

    protected $fillable = [
        'name',
        'description',
        'query_limit',
        'cost_per_query',
        'cost',
        'permissions',
        'cost_yearly',
        'premium',
    ];

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function role()
    {
        return $this->morphToMany(Role::class, 'model', 'model_has_roles', 'model_id', 'role_id');
    }

    /**
     * Visszaadja a csomag funkcióit.
     */
    public function features()
    {
        return $this->hasMany(PackageFeature::class, 'package_id', 'package_id');
    }

}
