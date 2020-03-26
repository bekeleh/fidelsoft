<?php

namespace App\Policies;

/**
 * Class GroupPolicy.
 */
class GroupPolicy extends EntityPolicy
{
    public function tableName()
    {
        return 'permission_groups';
    }
}
