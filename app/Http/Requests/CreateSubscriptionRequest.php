<?php

namespace App\Http\Requests;

class CreateSubscriptionRequest extends SubscriptionRequest
{
    protected $entityType = ENTITY_SUBSCRIPTION;

    public function authorize()
    {
        return $this->user()->can('create', $this->entityType);
    }

    public function rules()
    {
        $rules = [
            'event_id' => 'required|unique:subscriptions,event_id,' . $this->id . ',id,account_id,' . $this->user()->account_id,
        ];

        return $rules;
    }
}
