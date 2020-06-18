<?php

namespace App\Policies;

class RecurringExpensePolicy extends EntityPolicy
{
    protected function getEntity()
    {
        return ENTITY_RECURRING_EXPENSE;
    }
}
