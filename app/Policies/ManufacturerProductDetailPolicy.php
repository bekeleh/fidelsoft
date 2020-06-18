<?php

namespace Modules\Manufacturer\Policies;

use App\Policies\EntityPolicy;

class ManufacturerProductDetailPolicy extends EntityPolicy
{
    protected function getEntity()
    {
        return ENTITY_MANUFACTURER_PRODUCT_DETAIL;
    }
}
