<?php

namespace App\Policies;

class UserPolicy extends EntityPolicy
{
    protected function tableName()
    {
        return 'users';
    }
}
