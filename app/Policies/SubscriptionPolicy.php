<?php

namespace App\Policies;

class SubscriptionPolicy extends EntityPolicy
{
    protected function getEntity()
    {
        return ENTITY_SUBSCRIPTION;
    }
}
