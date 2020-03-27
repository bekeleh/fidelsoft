<?php

namespace App\Policies;

class VendorPolicy extends EntityPolicy
{
    protected function tableName()
    {
        return 'vendors';
    }
}
