<?php

namespace App\Policies;

/**
 * Class StorePolicy.
 */
class StorePolicy extends EntityPolicy
{
    protected function getEntity()
    {
        return ENTITY_STORE;
    }
}
