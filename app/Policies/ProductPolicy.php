<?php

namespace App\Policies;

/**
 * Class ProductPolicy.
 */
class ProductPolicy extends EntityPolicy
{
    protected function getEntity()
    {
        return ENTITY_PRODUCT;
    }
}
