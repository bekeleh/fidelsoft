<?php

namespace App\Policies;

class QuotePolicy extends EntityPolicy
{
    protected function tableName()
    {
        return 'invitations';
    }
}
