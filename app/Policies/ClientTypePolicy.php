<?php

namespace App\Policies;

/**
 * Class ClientTypePolicy.
 */
class ClientTypePolicy extends EntityPolicy
{
    protected function getEntity()
    {
        return ENTITY_CLIENT_TYPE;
    }
}
