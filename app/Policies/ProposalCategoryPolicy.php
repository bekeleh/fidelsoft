<?php

namespace App\Policies;

class ProposalCategoryPolicy extends EntityPolicy
{
    protected function tableName()
    {
        return 'proposal_categories';
    }
}
