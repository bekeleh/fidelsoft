<?php

namespace App\Policies;

/**
 * Class SaleTypePolicy.
 */
class SaleTypePolicy extends EntityPolicy
{
    protected function getEntity()
    {
        return ENTITY_SALE_TYPE;
    }
}
