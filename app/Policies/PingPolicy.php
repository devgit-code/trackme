<?php

namespace App\Policies;

use App\Models\Tag;
use App\Models\Ping;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PingPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Ping $ping): bool
    {
        return true; //TODO: Lost and found tags, prevent ping from view unless they own
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Ping $ping): bool
    {
        return $ping->user()->is($user);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Ping $ping): bool
    {
        return $ping->user == $user || $ping->tag->user == $user;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Ping $ping): bool
    {
        return $ping->user->is($user) || $ping->tag->user->is($user);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Ping $ping): bool
    {
        //
    }
}
