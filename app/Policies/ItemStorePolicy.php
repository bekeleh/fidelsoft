<?php

namespace App\Policies;

/**
 * Class ItemStorePolicy.
 */
class ItemStorePolicy extends EntityPolicy
{
    protected function getEntity()
    {
        return ENTITY_ITEM_STORE;
    }
}
