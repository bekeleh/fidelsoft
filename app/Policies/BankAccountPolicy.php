<?php

namespace App\Policies;

/**
 * Class BankAccountPolicy.
 */
class BankAccountPolicy extends EntityPolicy
{
    protected function getEntity()
    {
        return ENTITY_BANK_ACCOUNT;
    }
}
