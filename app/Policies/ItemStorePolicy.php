<?php

namespace App\Policies;

/**
 * Class ItemStorePolicy.
 */
class ItemStorePolicy extends EntityPolicy
{
    public function tableName()
    {
        return 'item_stores';
    }
}
