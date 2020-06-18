<?php

namespace App\Policies;

class CreditPolicy extends EntityPolicy
{
    protected function getEntity()
    {
        return ENTITY_CREDIT;
    }
}
