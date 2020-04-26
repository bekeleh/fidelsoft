<?php

namespace App\Policies;

/**
 * Class StatusPolicy.
 */
class StatusPolicy extends EntityPolicy
{
    public function tableName()
    {
        return 'statuses';
    }
}
