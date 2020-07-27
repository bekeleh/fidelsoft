<?php

namespace App\Listeners;

use App\Events\InvoiceItemsWereCreated;
use App\Events\InvoiceItemsWereUpdated;
use App\Events\InvoiceWasDeleted;
use App\Events\QuoteItemsWereCreated;
use App\Events\QuoteItemsWereUpdated;
use App\Ninja\Transformers\InvoiceTransformer;

/**
 * Class InvoiceItemListener.
 */
class InvoiceItemListener extends EntityListener
{

    public function createdInvoice(InvoiceItemsWereCreated $event)
    {
        $transformer = new InvoiceTransformer($event->invoice->account);

        $this->checkSubscriptions(EVENT_CREATE_INVOICE, $event->invoice, $transformer, ENTITY_CLIENT);
    }

    public function updatedInvoice(InvoiceItemsWereUpdated $event)
    {
        $transformer = new InvoiceTransformer($event->invoice->account);
        $this->checkSubscriptions(EVENT_UPDATE_INVOICE, $event->invoice, $transformer, ENTITY_CLIENT);
    }

    public function deletedInvoice(InvoiceWasDeleted $event)
    {
        $transformer = new InvoiceTransformer($event->invoice->account);
        $this->checkSubscriptions(EVENT_DELETE_INVOICE, $event->invoice, $transformer, ENTITY_CLIENT);
    }

    public function createdQuote(QuoteItemsWereCreated $event)
    {
        $transformer = new InvoiceTransformer($event->invoice->account);
        $this->checkSubscriptions(EVENT_CREATE_QUOTE, $event->invoice, $transformer, ENTITY_CLIENT);
    }

    public function updatedQuote(QuoteItemsWereUpdated $event)
    {
        $transformer = new InvoiceTransformer($event->invoice->account);
        $this->checkSubscriptions(EVENT_UPDATE_QUOTE, $event->invoice, $transformer, ENTITY_CLIENT);
    }

    public function deletedQuote(QuoteItemsWereDeleted $event)
    {
        $transformer = new InvoiceTransformer($event->invoice->account);
        $this->checkSubscriptions(EVENT_DELETE_QUOTE, $event->invoice, $transformer, ENTITY_CLIENT);
    }
}
