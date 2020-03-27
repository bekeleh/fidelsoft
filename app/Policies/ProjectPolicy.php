<?php

namespace App\Policies;

class ProjectPolicy extends EntityPolicy
{
    protected function tableName()
    {
        return 'projects';
    }
}
