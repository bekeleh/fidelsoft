<?php

namespace App\Listeners\Purchase;

use App\Events\Purchase\BillItemsWereCreatedEvent;
use App\Events\Purchase\BillItemsWereUpdatedEvent;
use App\Events\Purchase\BillWasDeletedEvent;
use App\Events\Purchase\BillQuoteItemsWereCreatedEvent;
use App\Events\Purchase\BillQuoteItemsWereDeletedEvent;
use App\Events\Purchase\BillQuoteItemsWereUpdatedEvent;
use App\Ninja\Transformers\BillTransformer;
use App\Listeners\EntityListener;

/**
 * Class InvoiceItemListener.
 */
class BillItemListener extends EntityListener
{
    public function __construct()
    {
    }

    public function createdInvoice(BillItemsWereCreatedEvent $event)
    {
        $transformer = new BillTransformer($event->Bill->account);

        $this->checkSubscriptions(EVENT_CREATE_BILL, $event->Bill, $transformer, ENTITY_VENDOR);
    }

    public function updatedInvoice(BillItemsWereUpdatedEvent $event)
    {
        $transformer = new BillTransformer($event->Bill->account);
        $this->checkSubscriptions(EVENT_UPDATE_BILL, $event->Bill, $transformer, ENTITY_VENDOR);
    }

    public function deletedInvoice(BillWasDeletedEvent $event)
    {
        $transformer = new BillTransformer($event->Bill->account);
        $this->checkSubscriptions(EVENT_DELETE_BILL, $event->Bill, $transformer, ENTITY_VENDOR);
    }

    public function createdQuote(BillQuoteItemsWereCreatedEvent $event)
    {
        $transformer = new BillTransformer($event->BillQuote->account);
        $this->checkSubscriptions(EVENT_CREATE_bill_quote, $event->BillQuote, $transformer, ENTITY_VENDOR);
    }

    public function updatedQuote(BillQuoteItemsWereUpdatedEvent $event)
    {
        $transformer = new BillTransformer($event->BillQuote->account);
        $this->checkSubscriptions(EVENT_UPDATE_bill_quote, $event->BillQuote, $transformer, ENTITY_VENDOR);
    }

    public function deletedQuote(BillQuoteItemsWereDeletedEvent $event)
    {
        $transformer = new BillTransformer($event->BillQuote->account);
        $this->checkSubscriptions(EVENT_DELETE_bill_quote, $event->BillQuote, $transformer, ENTITY_VENDOR);
    }
}
