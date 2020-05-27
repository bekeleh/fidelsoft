<?php

namespace App\Http\Requests;

use App\Models\ItemTransfer;
use App\Models\Store;

class ItemRequestRequest extends EntityRequest
{
    protected $entityType = ENTITY_ITEM_REQUEST;

    public function authorize()
    {
        return true;
    }
}
