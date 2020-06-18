<?php

namespace App\Policies;

class InvoicePolicy extends EntityPolicy
{
    protected function getEntity()
    {
        return ENTITY_INVOICE;
    }
}
