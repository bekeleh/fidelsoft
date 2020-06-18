<?php

namespace App\Policies;

class PaymentPolicy extends EntityPolicy
{
    protected function getEntity()
    {
        return ENTITY_PAYMENT;
    }
}
