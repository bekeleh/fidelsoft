<?php

namespace App\Policies;

class TokenPolicy extends EntityPolicy
{
    protected function getEntity()
    {
        return ENTITY_TOKEN;
    }
}
