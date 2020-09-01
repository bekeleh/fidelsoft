<?php

namespace App\Listeners\Purchase;

use App\Events\Purchase\BillPaymentWasCreatedEvent;
use App\Events\Purchase\BillPaymentWasDeletedEvent;
use App\Events\Purchase\BillPaymentWasUpdatedEvent;
use App\Ninja\Transformers\PaymentTransformer;
use App\Listeners\EntityListener;

/**
 * Class PaymentListener.
 */
class BillPaymentListener extends EntityListener
{

    public function createdBillPayment(BillPaymentWasCreatedEvent $event)
    {
        $transformer = new PaymentTransformer($event->billPayment->account);

        $this->checkSubscriptions(EVENT_CREATE_BILL_PAYMENT, $event->billPayment, $transformer);
    }

    public function updatedBillPayment(BillPaymentWasUpdatedEvent $event)
    {
        $transformer = new PaymentTransformer($event->billPayment->account);
        $this->checkSubscriptions(EVENT_UPDATE_BILL_PAYMENT, $event->billPayment, $transformer);
    }

    public function deletedBillPayment(BillPaymentWasDeletedEvent $event)
    {
        $transformer = new PaymentTransformer($event->billPayment->account);
        $this->checkSubscriptions(EVENT_DELETE_BILL_PAYMENT, $event->billPayment, $transformer);
    }

}
