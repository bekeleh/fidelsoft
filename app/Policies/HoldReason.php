<?php

namespace App\Policies;

/**
 * Class HoldReasonPolicy.
 */
class HoldReason extends EntityPolicy
{
    protected function getEntity()
    {
        return ENTITY_HOLD_REASON;
    }
}
