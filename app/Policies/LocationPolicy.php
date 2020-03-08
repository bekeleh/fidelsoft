<?php

namespace App\Policies;

/**
 * Class LocationPolicy.
 */
class LocationPolicy extends EntityPolicy
{
    public function tableName()
    {
        return 'locations';
    }
}
