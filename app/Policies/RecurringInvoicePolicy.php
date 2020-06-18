<?php

namespace App\Policies;

class RecurringInvoicePolicy extends EntityPolicy
{
    protected function getEntity()
    {
        return ENTITY_RECURRING_INVOICE;
    }
}
