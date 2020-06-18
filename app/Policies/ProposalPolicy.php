<?php

namespace App\Policies;

class ProposalPolicy extends EntityPolicy
{
    protected function getEntity()
    {
        return ENTITY_PROPOSAL;
    }
}
