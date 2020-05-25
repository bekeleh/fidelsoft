<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy extends EntityPolicy
{
    public static function edit(User $user, $item)
    {
        return $user->hasPermission('admin');
    }

    public static function create(User $user, $item)
    {
        return $user->hasPermission('admin');
    }
}
