<?php

namespace App\Policies;

/**
 * Class StorePolicy.
 */
class StorePolicy extends EntityPolicy
{
    public function tableName()
    {
        return 'stores';
    }
}
