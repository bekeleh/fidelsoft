<?php

namespace App\Policies;


class ManufacturerPolicy extends EntityPolicy
{
    protected function getEntity()
    {
        return ENTITY_MANUFACTURER;
    }
}
