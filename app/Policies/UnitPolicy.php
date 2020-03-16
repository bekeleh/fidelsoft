<?php

namespace App\Policies;

/**
 * Class LocationPolicy.
 */
class UnitPolicy extends EntityPolicy
{
    public function tableName()
    {
        return 'units';
    }
}
