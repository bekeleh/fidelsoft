<?php

namespace App\Policies;

/**
 * Class StorePolicy.
 */
class ItemMovementPolicy extends EntityPolicy
{
    public function tableName()
    {
        return 'item_movements';
    }
}
