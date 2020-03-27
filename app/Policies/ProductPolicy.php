<?php

namespace App\Policies;

/**
 * Class ProductPolicy.
 */
class ProductPolicy extends EntityPolicy
{
    protected function tableName()
    {
        return 'products';
    }
}
