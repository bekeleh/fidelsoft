<?php

namespace App\Http\Requests;

use App\Models\Subscription;

class UpdateSubscriptionRequest extends SubscriptionRequest
{
    protected $entityType = ENTITY_SUBSCRIPTION;

    public function authorize()
    {
        return $this->user()->can('edit', $this->entityType);
    }

    public function rules()
    {
        $rules = [];

        $this->validationData();
        $subscription = $this->entity();

        if ($subscription) {
            $rules['event_id'] = 'required|max:90|unique:subscriptions,event_id,' . $subscription->id . ',id,account_id,' . $subscription->account_id;
        }

        return $rules;
    }

    public function validationData()
    {
        $input = $this->input();
        if (count($input)) {
            $this->request->add([
                'account_id' => Subscription::getAccountId(),
            ]);
        }

        return $this->request->all();
    }
}
