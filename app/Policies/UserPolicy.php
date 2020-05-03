<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy extends EntityPolicy
{
    protected function tableName()
    {
        return 'users';
    }

    public function passwordReset(User $user, $item = null)
    {
        return $user->hasAccess('users.password_reset');
    }
}
