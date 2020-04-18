<?php

namespace App\Policies;

/**
 * Class ItemCategoryPolicy.
 */
class ItemCategoryPolicy extends EntityPolicy
{
    public function tableName()
    {
        return 'item_categories';
    }
}
