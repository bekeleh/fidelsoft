<?php

namespace App\Policies;

/**
 * Class LocationPolicy.
 */
class ItemCategoryPolicy extends EntityPolicy
{
    public function tableName()
    {
        return 'item_categories';
    }
}
