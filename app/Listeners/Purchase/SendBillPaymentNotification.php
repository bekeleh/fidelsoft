<?php

namespace App\Listeners\Purchase;

use App\Events\Purchase\BillPaymentWasCreatedEvent;
use App\Events\Purchase\BillPaymentWasDeletedEvent;
use App\Events\Purchase\BillPaymentWasUpdatedEvent;
use App\Ninja\Transformers\PaymentTransformer;
use App\Listeners\EntityListener;

/**
 * Class SendBillPaymentNotification.
 */
class SendBillPaymentNotification extends EntityListener
{
    public function __construct()
    {
        //
    }

    public function createdBillPayment(BillPaymentWasCreatedEvent $event)
    {
        $transformer = new PaymentTransformer($event->payment->account);

        $this->checkSubscriptions(EVENT_CREATE_BILL_PAYMENT, $event->payment, $transformer, [ENTITY_VENDOR, ENTITY_BILL]);
    }

    public function updatedBillPayment(BillPaymentWasUpdatedEvent $event)
    {
        $transformer = new PaymentTransformer($event->payment->account);

        $this->checkSubscriptions(EVENT_CREATE_BILL_PAYMENT, $event->payment, $transformer, [ENTITY_VENDOR, ENTITY_BILL]);
    }

    public function deletedBillPayment(BillPaymentWasDeletedEvent $event)
    {
        $transformer = new PaymentTransformer($event->payment->account);

        $this->checkSubscriptions(EVENT_DELETE_BILL_PAYMENT, $event->payment, $transformer, [ENTITY_VENDOR, ENTITY_BILL]);
    }

}
