<?php

namespace App\Policies;

class TaskPolicy extends EntityPolicy
{
    protected function getEntity()
    {
        return ENTITY_TASK;
    }
}
