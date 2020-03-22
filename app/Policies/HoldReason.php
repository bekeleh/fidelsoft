<?php

namespace App\Policies;

/**
 * Class HoldReasonPolicy.
 */
class HoldReason extends EntityPolicy
{
    public function tableName()
    {
        return 'hold_reasons';
    }
}
