<?php

namespace App\Policies;

class InvoicePolicy extends EntityPolicy
{
    protected function tableName()
    {
        return 'invoices';
    }
}
