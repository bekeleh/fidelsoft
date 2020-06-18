<?php

namespace App\Policies;

class ProjectPolicy extends EntityPolicy
{
    protected function getEntity()
    {
        return ENTITY_PROJECT;
    }
}
