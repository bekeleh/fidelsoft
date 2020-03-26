<?php

namespace App\Policies;

/**
 * Class UnitPolicy.
 */
class UnitPolicy extends EntityPolicy
{
    public function tableName()
    {
        return 'units';
    }
}
