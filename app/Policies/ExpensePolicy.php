<?php

namespace App\Policies;

class ExpensePolicy extends EntityPolicy
{
    protected function getEntity()
    {
        return ENTITY_EXPENSE;
    }
}
