<?php

namespace App\Policies;

class BranchPolicy extends EntityPolicy
{
    protected function getEntity()
    {
        return ENTITY_BRANCH;
    }
}
