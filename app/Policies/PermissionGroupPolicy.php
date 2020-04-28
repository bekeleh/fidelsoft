<?php

namespace App\Policies;

/**
 * Class PermissionGroupPolicy.
 */
class PermissionGroupPolicy extends EntityPolicy
{
    public function tableName()
    {
        return 'permission_groups';
    }
}
