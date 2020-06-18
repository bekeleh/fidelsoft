<?php

namespace App\Policies;

class DepartmentPolicy extends EntityPolicy
{
    protected function getEntity()
    {
        return ENTITY_DEPARTMENT;
    }
}
