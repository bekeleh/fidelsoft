<?php

namespace App\Policies;

class CustomerPolicy extends EntityPolicy
{
    protected function tableName()
    {
        return 'customer_polices';
    }
}
