<?php

namespace App\Policies;

/**
 * Class StatusPolicy.
 */
class StatusPolicy extends EntityPolicy
{
    protected function getEntity()
    {
        return ENTITY_STATUS;
    }
}
