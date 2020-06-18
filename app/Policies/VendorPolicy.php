<?php

namespace App\Policies;

class VendorPolicy extends EntityPolicy
{
    protected function getEntity()
    {
        return ENTITY_VENDOR;
    }
}
