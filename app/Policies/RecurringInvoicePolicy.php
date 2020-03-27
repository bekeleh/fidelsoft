<?php

namespace App\Policies;

class RecurringInvoicePolicy extends EntityPolicy
{
    protected function tableName()
    {
        return 'invoices';
    }
}
