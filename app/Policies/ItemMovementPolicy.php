<?php

namespace App\Policies;

/**
 * Class StorePolicy.
 */
class ItemMovementPolicy extends EntityPolicy
{
    protected function getEntity()
    {
        return ENTITY_ITEM_MOVEMENT;
    }
}
