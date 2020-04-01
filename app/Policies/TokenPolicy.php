<?php

namespace App\Policies;

use App\Models\User;

abstract class TokenPolicy extends EntityPolicy
{
    public function edit(User $user, $item)
    {
        return $user->hasAccess('admin');
    }

    public function create(User $user, $item)
    {
        return $user->hasAccess('admin');
    }
}
