<?php

namespace App\Policies;

/**
 * Class ItemCategoryPolicy.
 */
class ItemCategoryPolicy extends EntityPolicy
{
    protected function getEntity()
    {
        return ENTITY_ITEM_CATEGORY;
    }
}
