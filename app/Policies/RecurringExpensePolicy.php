<?php

namespace App\Policies;

class RecurringExpensePolicy extends EntityPolicy
{
    protected function tableName()
    {
        return 'recurring_expenses';
    }
}
