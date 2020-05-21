<?php

namespace App\Policies;

class DepartmentPolicy extends EntityPolicy
{
    protected function tableName()
    {
        return 'departments';
    }
}
