<?php

namespace App\Policies;

/**
 * Class ItemBrandPolicy.
 */
class ItemBrandPolicy extends EntityPolicy
{
    public function tableName()
    {
        return 'item_brands';
    }
}
