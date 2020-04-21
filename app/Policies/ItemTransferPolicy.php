<?php

namespace App\Policies;

/**
 * Class ItemTransferPolicy.
 */
class ItemTransferPolicy extends EntityPolicy
{
    public function tableName()
    {
        return 'item_transfers';
    }
}
