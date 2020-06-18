<?php

namespace App\Policies;

class ProposalCategoryPolicy extends EntityPolicy
{
    protected function getEntity()
    {
        return ENTITY_PROPOSAL_CATEGORY;
    }
}
