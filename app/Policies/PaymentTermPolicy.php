<?php

namespace App\Policies;

use App\Models\User;

/**
 * Class PaymentTermPolicy.
 */
class PaymentTermPolicy extends EntityPolicy
{
    protected function getEntity()
    {
        return ENTITY_PAYMENT_TERM;
    }
}
