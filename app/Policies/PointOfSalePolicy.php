<?php

namespace App\Policies;

class PointOfSalePolicy extends EntityPolicy
{
    protected function getEntity()
    {
        return ENTITY_POINT_OF_SALE;
    }
}
