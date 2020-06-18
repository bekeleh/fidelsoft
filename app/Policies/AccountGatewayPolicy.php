<?php

namespace App\Policies;

/**
 * Class AccountGatewayPolicy.
 */
class AccountGatewayPolicy extends EntityPolicy
{
    protected function getEntity()
    {
        return ENTITY_ACCOUNT_GATEWAY;
    }

}
