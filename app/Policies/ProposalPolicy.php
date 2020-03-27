<?php

namespace App\Policies;

class ProposalPolicy extends EntityPolicy
{
    protected function tableName()
    {
        return 'proposals';
    }
}
