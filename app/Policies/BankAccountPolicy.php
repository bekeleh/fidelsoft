<?php

namespace App\Policies;

use App\Models\User;

/**
 * Class BankAccountPolicy.
 */
class BankAccountPolicy extends EntityPolicy
{
    protected function tableName()
    {
        return 'bank_accounts';
    }
}
