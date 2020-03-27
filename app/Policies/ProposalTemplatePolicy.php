<?php

namespace App\Policies;

class ProposalTemplatePolicy extends EntityPolicy
{
    protected function tableName()
    {
        return 'proposal_templates';
    }
}
