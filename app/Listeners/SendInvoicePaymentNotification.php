<?php

namespace App\Listeners;

use App\Events\PaymentWasCreatedEvent;
use App\Events\PaymentWasDeletedEvent;
use App\Events\PaymentWasUpdatedEvent;
use App\Ninja\Transformers\PaymentTransformer;

/**
 * Class SendInvoicePaymentNotification.
 */
class SendInvoicePaymentNotification extends EntityListener
{
    public function __construct()
    {
        //
    }

    public function createdPayment(PaymentWasCreatedEvent $event)
    {
        $transformer = new PaymentTransformer($event->payment->account);
        $this->checkSubscriptions(EVENT_CREATE_PAYMENT, $event->payment, $transformer, [ENTITY_CLIENT, ENTITY_INVOICE]);
    }

    public function updatedPayment(PaymentWasUpdatedEvent $event)
    {
        $transformer = new PaymentTransformer($event->payment->account);
        $this->checkSubscriptions(EVENT_CREATE_PAYMENT, $event->payment, $transformer, [ENTITY_CLIENT, ENTITY_INVOICE]);
    }

    public function deletedPayment(PaymentWasDeletedEvent $event)
    {
        $transformer = new PaymentTransformer($event->payment->account);
        $this->checkSubscriptions(EVENT_DELETE_PAYMENT, $event->payment, $transformer, [ENTITY_CLIENT, ENTITY_INVOICE]);
    }

}
