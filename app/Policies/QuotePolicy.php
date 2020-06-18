<?php

namespace App\Policies;

class QuotePolicy extends EntityPolicy
{
    protected function getEntity()
    {
        return ENTITY_QUOTE;
    }
}
