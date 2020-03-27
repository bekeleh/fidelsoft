<?php

namespace App\Policies;

class ProposalSnippetPolicy extends EntityPolicy
{
    protected function tableName()
    {
        return 'proposal_snippets';
    }
}
