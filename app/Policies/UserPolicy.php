<?php

namespace App\Policies;

class UserPolicy extends EntityPolicy
{
    protected function getEntity()
    {
        return ENTITY_USER;
    }

}
