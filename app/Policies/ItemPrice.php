<?php

namespace App\Policies;

/**
 * Class ItemPricePolicy.
 */
class ItemPrice extends EntityPolicy
{
    protected function getEntity()
    {
        return ENTITY_ITEM_PRICE;
    }
}
