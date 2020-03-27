<?php

namespace App\Policies;

class TaskPolicy extends EntityPolicy
{
    protected function tableName()
    {
        return 'tasks';
    }
}
