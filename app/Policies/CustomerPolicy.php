<?php

namespace App\Policies;

class CustomerPolicy extends EntityPolicy
{
    protected function getEntity()
    {
        return ENTITY_CUSTOMER;
    }
}
