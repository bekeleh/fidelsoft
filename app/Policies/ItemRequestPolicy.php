<?php

namespace App\Policies;

/**
 * Class ItemRequestPolicy.
 */
class ItemRequestPolicy extends EntityPolicy
{
    protected function getEntity()
    {
        return ENTITY_ITEM_REQUEST;
    }
}
