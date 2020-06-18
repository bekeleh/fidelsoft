<?php

namespace App\Policies;

/**
 * Class ItemTransferPolicy.
 */
class ItemTransferPolicy extends EntityPolicy
{
    protected function getEntity()
    {
        return ENTITY_ITEM_TRANSFER;
    }
}
