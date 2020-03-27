<?php

namespace App\Policies;

class ContactPolicy extends EntityPolicy
{
    protected function tableName()
    {
        return 'contacts';
    }
}
