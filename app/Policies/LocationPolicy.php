<?php

namespace App\Policies;

/**
 * Class LocationPolicy.
 */
class LocationPolicy extends EntityPolicy
{
    protected function getEntity()
    {
        return ENTITY_LOCATION;
    }
}
