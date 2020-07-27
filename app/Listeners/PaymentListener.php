<?php

namespace App\Listeners;

use App\Events\PaymentWasCreated;
use App\Events\PaymentWasDeleted;
use App\Events\PaymentWasUpdated;
use App\Ninja\Transformers\PaymentTransformer;

/**
 * Class PaymentListener.
 */
class PaymentListener extends EntityListener
{

    public function createdPayment(PaymentWasCreated $event)
    {
        $transformer = new PaymentTransformer($event->payment->account);
        $this->checkSubscriptions(EVENT_CREATE_PAYMENT, $event->payment, $transformer, [ENTITY_CLIENT, ENTITY_INVOICE]);
    }

    public function updatedPayment(PaymentWasUpdated $event)
    {
        $transformer = new PaymentTransformer($event->payment->account);
        $this->checkSubscriptions(EVENT_CREATE_PAYMENT, $event->payment, $transformer, [ENTITY_CLIENT, ENTITY_INVOICE]);
    }

    public function deletedPayment(PaymentWasDeleted $event)
    {
        $transformer = new PaymentTransformer($event->payment->account);
        $this->checkSubscriptions(EVENT_DELETE_PAYMENT, $event->payment, $transformer, [ENTITY_CLIENT, ENTITY_INVOICE]);
    }

}
