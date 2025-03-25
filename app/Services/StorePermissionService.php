<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;

class StorePermissionService
{
    public function getStoresWithPermission(string $permissionName)
    {
        $user = Auth::user();

        return $user->stores()
            ->whereHas('subscription.package.role.permissions', function ($query) use ($permissionName) {
                $query->where('name', $permissionName);
            })
            ->get();
    }
}
