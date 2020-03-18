<?php

namespace App\Policies;

/**
 * Class ItemPricePolicy.
 */
class ItemPrice extends EntityPolicy
{
    public function tableName()
    {
        return 'item_prices';
    }
}
