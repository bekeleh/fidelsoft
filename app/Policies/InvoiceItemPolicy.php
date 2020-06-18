<?php

namespace App\Policies;

class InvoiceItemPolicy extends EntityPolicy
{
    protected function getEntity()
    {
        return ENTITY_INVOICE_ITEM;
    }
}
