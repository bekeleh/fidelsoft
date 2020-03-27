<?php

namespace App\Policies;

use App\Models\User;

/**
 * Class PaymentTermPolicy.
 */
class PaymentTermPolicy extends EntityPolicy
{
    protected function tableName()
    {
        return 'payment_terms';
    }
}
