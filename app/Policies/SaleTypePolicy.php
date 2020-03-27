<?php

namespace App\Policies;

/**
 * Class SaleTypePolicy.
 */
class SaleTypePolicy extends EntityPolicy
{
    protected function tableName()
    {
        return 'sale_types';
    }
}
