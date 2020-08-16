<?php

namespace App\Listeners;

use App\Events\BillItemsWereCreatedEvent;
use App\Events\BillItemsWereUpdatedEvent;
use App\Events\BillWasDeletedEvent;
use App\Events\BillQuoteItemsWereCreatedEvent;
use App\Events\BillQuoteItemsWereDeletedEvent;
use App\Events\BillQuoteItemsWereUpdatedEvent;
use App\Ninja\Transformers\BillTransformer;

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
