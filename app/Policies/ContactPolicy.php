<?php

namespace App\Policies;

class ContactPolicy extends EntityPolicy
{
    protected function getEntity()
    {
        return ENTITY_CLIENT_CONTACT;
    }
}
