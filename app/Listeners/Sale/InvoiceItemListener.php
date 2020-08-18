<?php

namespace App\Listeners\Sale;

use App\Events\Sale\InvoiceItemsWereCreatedEvent;
use App\Events\Sale\InvoiceItemsWereUpdatedEvent;
use App\Events\Sale\InvoiceWasDeletedEvent;
use App\Events\Sale\QuoteItemsWereCreatedEvent;
use App\Events\Sale\QuoteItemsWereDeletedEvent;
use App\Events\Sale\QuoteItemsWereUpdatedEvent;
use App\Ninja\Transformers\InvoiceTransformer;
use App\Listeners\EntityListener;

/**
 * Class InvoiceItemListener.
 */
class InvoiceItemListener extends EntityListener
{

    public function createdInvoice(InvoiceItemsWereCreatedEvent $event)
    {
        $transformer = new InvoiceTransformer($event->invoice->account);

        $this->checkSubscriptions(EVENT_CREATE_INVOICE, $event->invoice, $transformer, ENTITY_CLIENT);
    }

    public function updatedInvoice(InvoiceItemsWereUpdatedEvent $event)
    {
        $transformer = new InvoiceTransformer($event->invoice->account);
        $this->checkSubscriptions(EVENT_UPDATE_INVOICE, $event->invoice, $transformer, ENTITY_CLIENT);
    }

    public function deletedInvoice(InvoiceWasDeletedEvent $event)
    {
        $transformer = new InvoiceTransformer($event->invoice->account);
        $this->checkSubscriptions(EVENT_DELETE_INVOICE, $event->invoice, $transformer, ENTITY_CLIENT);
    }

    public function createdQuote(QuoteItemsWereCreatedEvent $event)
    {
        $transformer = new InvoiceTransformer($event->quote->account);
        $this->checkSubscriptions(EVENT_CREATE_QUOTE, $event->quote, $transformer, ENTITY_CLIENT);
    }

    public function updatedQuote(QuoteItemsWereUpdatedEvent $event)
    {
        $transformer = new InvoiceTransformer($event->quote->account);
        $this->checkSubscriptions(EVENT_UPDATE_QUOTE, $event->quote, $transformer, ENTITY_CLIENT);
    }

    public function deletedQuote(QuoteItemsWereDeletedEvent $event)
    {
        $transformer = new InvoiceTransformer($event->quote->account);
        $this->checkSubscriptions(EVENT_DELETE_QUOTE, $event->quote, $transformer, ENTITY_CLIENT);
    }
}
