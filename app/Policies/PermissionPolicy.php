<?php

namespace App\Policies;

class PermissionPolicy extends EntityPolicy
{
    protected function tableName()
    {
        return 'permissions';
    }
}
