<?php

namespace App\Http\Requests;

use App\Models\Location;

class LocationRequest extends EntityRequest
{
    protected $entityType = ENTITY_LOCATION;

}
