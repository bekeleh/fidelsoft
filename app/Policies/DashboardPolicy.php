<?php

namespace App\Policies;

class DashboardPolicy extends EntityPolicy
{
    protected function getEntity()
    {
        return ENTITY_DASHBOARD;
    }
}
