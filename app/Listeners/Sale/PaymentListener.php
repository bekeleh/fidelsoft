<?php

namespace App\Listeners\Sale;

use App\Events\Sale\PaymentWasCreatedEvent;
use App\Events\Sale\PaymentWasDeletedEvent;
use App\Events\Sale\PaymentWasUpdatedEvent;
use App\Ninja\Transformers\PaymentTransformer;
use App\Listeners\EntityListener;

/**
 * Class PaymentListener.
 */
class PaymentListener extends EntityListener
{

    public function createdPayment(PaymentWasCreatedEvent $event)
    {
        $transformer = new PaymentTransformer($event->payment->account);

        $this->checkSubscriptions(EVENT_CREATE_PAYMENT, $event->payment, $transformer);
    }

    public function updatedPayment(PaymentWasUpdatedEvent $event)
    {
        $transformer = new PaymentTransformer($event->payment->account);
        $this->checkSubscriptions(EVENT_UPDATE_PAYMENT, $event->payment, $transformer);
    }

    public function deletedPayment(PaymentWasDeletedEvent $event)
    {
        $transformer = new PaymentTransformer($event->payment->account);
        $this->checkSubscriptions(EVENT_DELETE_PAYMENT, $event->payment, $transformer);
    }


}
