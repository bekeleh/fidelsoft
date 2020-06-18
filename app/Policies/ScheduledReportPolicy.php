<?php

namespace App\Policies;

class ScheduledReportPolicy extends EntityPolicy
{
    protected function getEntity()
    {
        return ENTITY_SCHEDULED_REPORT;
    }
}
