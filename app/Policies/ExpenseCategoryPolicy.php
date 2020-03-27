<?php

namespace App\Policies;

class ExpenseCategoryPolicy extends EntityPolicy
{
    protected function tableName()
    {
        return 'expense_categories';
    }
}
