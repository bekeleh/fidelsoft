<?php

namespace App\Http\Requests;

class SubscriptionRequest extends EntityRequest
{
    protected $entityType = ENTITY_SUBSCRIPTION;

    public function authorize()
    {
        return true;
    }
}
