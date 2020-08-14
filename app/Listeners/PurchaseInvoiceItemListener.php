<?php

namespace App\Listeners;

use App\Events\BillItemsWereCreated;
use App\Events\BillItemsWereUpdated;
use App\Events\BillWasDeleted;
use App\Events\BillQuoteItemsWereCreated;
use App\Events\BillQuoteItemsWereDeleted;
use App\Events\BillQuoteItemsWereUpdated;
use App\Ninja\Transformers\BillTransformer;

/**
 * Class InvoiceItemListener.
 */
class BillItemListener extends EntityListener
{

    public function createdInvoice(BillItemsWereCreated $event)
    {
        $transformer = new BillTransformer($event->Bill->account);

        $this->checkSubscriptions(EVENT_CREATE_BILL, $event->Bill, $transformer, ENTITY_VENDOR);
    }

    public function updatedInvoice(BillItemsWereUpdated $event)
    {
        $transformer = new BillTransformer($event->Bill->account);
        $this->checkSubscriptions(EVENT_UPDATE_BILL, $event->Bill, $transformer, ENTITY_VENDOR);
    }

    public function deletedInvoice(BillWasDeleted $event)
    {
        $transformer = new BillTransformer($event->Bill->account);
        $this->checkSubscriptions(EVENT_DELETE_BILL, $event->Bill, $transformer, ENTITY_VENDOR);
    }

    public function createdQuote(BillQuoteItemsWereCreated $event)
    {
        $transformer = new BillTransformer($event->purchaseQuote->account);
        $this->checkSubscriptions(EVENT_CREATE_bill_quote, $event->purchaseQuote, $transformer, ENTITY_VENDOR);
    }

    public function updatedQuote(BillQuoteItemsWereUpdated $event)
    {
        $transformer = new BillTransformer($event->purchaseQuote->account);
        $this->checkSubscriptions(EVENT_UPDATE_bill_quote, $event->purchaseQuote, $transformer, ENTITY_VENDOR);
    }

    public function deletedQuote(BillQuoteItemsWereDeleted $event)
    {
        $transformer = new BillTransformer($event->purchaseQuote->account);
        $this->checkSubscriptions(EVENT_DELETE_bill_quote, $event->purchaseQuote, $transformer, ENTITY_VENDOR);
    }
}
