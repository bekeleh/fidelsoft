<?php

namespace App\Policies;

/**
 * Class ItemBrandPolicy.
 */
class ItemBrandPolicy extends EntityPolicy
{
    protected function getEntity()
    {
        return ENTITY_ITEM_BRAND;
    }
}
