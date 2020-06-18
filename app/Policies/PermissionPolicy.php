<?php

namespace App\Policies;

class PermissionPolicy extends EntityPolicy
{
    protected function getEntity()
    {
        return ENTITY_PERMISSION;
    }
}
