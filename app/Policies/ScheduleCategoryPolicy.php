<?php

namespace App\Policies;

class ScheduleCategoryPolicy extends EntityPolicy
{
    protected function getEntity()
    {
        return ENTITY_SCHEDULE_CATEGORY;
    }
}
