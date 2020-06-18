<?php

namespace App\Policies;

class TaxRatePolicy extends EntityPolicy
{
    protected function getEntity()
    {
        return ENTITY_TAX_RATE;
    }
}
