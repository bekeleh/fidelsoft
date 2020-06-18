<?php

namespace App\Policies;

class SchedulePolicy extends EntityPolicy
{
    protected function getEntity()
    {
        return ENTITY_SCHEDULE;
    }
}
