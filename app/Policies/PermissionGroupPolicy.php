<?php

namespace App\Policies;

/**
 * Class PermissionGroupPolicy.
 */
class PermissionGroupPolicy extends EntityPolicy
{
    protected function getEntity()
    {
        return ENTITY_PERMISSION_GROUP;
    }
}
