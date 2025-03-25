<?php

namespace App\Policies;

use App\Models\Exception;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ExceptionPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Exception $exception): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, $store_id): bool
    {
        return $user->stores()->where('store_id', $store_id)->exists();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Exception $exception): bool
    {
        return $user->stores()->where('store_id', $exception->store_id)->exists();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Exception $exception): bool
    {
        return $user->stores()->where('store_id', $exception->store_id)->exists();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Exception $exception): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Exception $exception): bool
    {
        return false;
    }
}
