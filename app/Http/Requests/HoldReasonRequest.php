<?php

namespace App\Http\Requests;

use App\Models\HoldReason;

class HoldReasonRequest extends EntityRequest
{
    protected $entityType = ENTITY_HOLD_REASON;

    public function authorize()
    {
        return true;
    }
}
