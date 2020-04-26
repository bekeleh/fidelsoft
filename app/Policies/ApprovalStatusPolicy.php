<?php

namespace App\Policies;

/**
 * Class ApprovalStatusPolicy.
 */
class ApprovalStatusPolicy extends EntityPolicy
{
    public function tableName()
    {
        return 'approval_statuses';
    }
}
