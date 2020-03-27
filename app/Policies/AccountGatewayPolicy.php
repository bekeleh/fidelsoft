<?php

namespace App\Policies;

/**
 * Class AccountGatewayPolicy.
 */
class AccountGatewayPolicy extends EntityPolicy
{
    protected function tableName()
    {
        return 'account_gateways';
    }
}
