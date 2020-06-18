<?php

namespace App\Policies;

class ProposalTemplatePolicy extends EntityPolicy
{
    protected function getEntity()
    {
        return ENTITY_PROPOSAL_TEMPLATE;
    }
}
