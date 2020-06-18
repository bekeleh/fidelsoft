<?php

namespace App\Policies;

class ClientPolicy extends EntityPolicy
{
    protected function getEntity()
    {
        return ENTITY_CLIENT;
    }
}
