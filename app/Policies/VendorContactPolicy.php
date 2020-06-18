<?php

namespace App\Policies;

class VendorContactPolicy extends EntityPolicy
{
    protected function getEntity()
    {
        return ENTITY_VENDOR_CONTACT;
    }
}
