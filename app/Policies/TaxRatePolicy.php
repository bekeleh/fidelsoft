<?php

namespace App\Policies;

class TaxRatePolicy extends EntityPolicy
{
    protected function tableName()
    {
        return 'tax_rates';
    }
}
