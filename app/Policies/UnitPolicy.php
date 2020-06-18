<?php

namespace App\Policies;

/**
 * Class UnitPolicy.
 */
class UnitPolicy extends EntityPolicy
{
    protected function getEntity()
    {
        return ENTITY_UNIT;
    }
}
