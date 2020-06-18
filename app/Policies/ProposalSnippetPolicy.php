<?php

namespace App\Policies;

class ProposalSnippetPolicy extends EntityPolicy
{
    protected function getEntity()
    {
        return ENTITY_PROPOSAL_SNIPPET;
    }
}
