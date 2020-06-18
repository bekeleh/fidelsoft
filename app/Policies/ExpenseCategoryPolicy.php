<?php

namespace App\Policies;

class ExpenseCategoryPolicy extends EntityPolicy
{
    protected function getEntity()
    {
        return ENTITY_EXPENSE_CATEGORY;
    }
}
