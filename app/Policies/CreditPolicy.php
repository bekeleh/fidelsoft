<?php

namespace App\Policies;

class CreditPolicy extends EntityPolicy
{
    protected function tableName()
    {
        return 'credits';
    }
}
