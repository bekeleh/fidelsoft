<?php

namespace App\Policies;

class ExpensePolicy extends EntityPolicy
{
    protected function tableName()
    {
        return 'expenses';
    }
}
