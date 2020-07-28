<?php

namespace App\Listeners;

use App\Events\PurchaseInvoiceItemsWereCreated;
use App\Events\PurchaseInvoiceItemsWereUpdated;
use App\Events\PurchaseInvoiceWasDeleted;
use App\Events\PurchaseQuoteItemsWereCreated;
use App\Events\PurchaseQuoteItemsWereDeleted;
use App\Events\PurchaseQuoteItemsWereUpdated;
use App\Ninja\Transformers\PurchaseInvoiceTransformer;

/**
 * Class InvoiceItemListener.
 */
class PurchaseInvoiceItemListener extends EntityListener
{

    public function createdInvoice(PurchaseInvoiceItemsWereCreated $event)
    {
        $transformer = new PurchaseInvoiceTransformer($event->purchaseInvoice->account);

        $this->checkSubscriptions(EVENT_CREATE_PURCHASE_INVOICE, $event->purchaseInvoice, $transformer, ENTITY_VENDOR);
    }

    public function updatedInvoice(PurchaseInvoiceItemsWereUpdated $event)
    {
        $transformer = new PurchaseInvoiceTransformer($event->purchaseInvoice->account);
        $this->checkSubscriptions(EVENT_UPDATE_PURCHASE_INVOICE, $event->purchaseInvoice, $transformer, ENTITY_VENDOR);
    }

    public function deletedInvoice(PurchaseInvoiceWasDeleted $event)
    {
        $transformer = new PurchaseInvoiceTransformer($event->purchaseInvoice->account);
        $this->checkSubscriptions(EVENT_DELETE_PURCHASE_INVOICE, $event->purchaseInvoice, $transformer, ENTITY_VENDOR);
    }

    public function createdQuote(PurchaseQuoteItemsWereCreated $event)
    {
        $transformer = new PurchaseInvoiceTransformer($event->purchaseQuote->account);
        $this->checkSubscriptions(EVENT_CREATE_PURCHASE_QUOTE, $event->purchaseQuote, $transformer, ENTITY_VENDOR);
    }

    public function updatedQuote(PurchaseQuoteItemsWereUpdated $event)
    {
        $transformer = new PurchaseInvoiceTransformer($event->purchaseQuote->account);
        $this->checkSubscriptions(EVENT_UPDATE_PURCHASE_QUOTE, $event->purchaseQuote, $transformer, ENTITY_VENDOR);
    }

    public function deletedQuote(PurchaseQuoteItemsWereDeleted $event)
    {
        $transformer = new PurchaseInvoiceTransformer($event->purchaseQuote->account);
        $this->checkSubscriptions(EVENT_DELETE_PURCHASE_QUOTE, $event->purchaseQuote, $transformer, ENTITY_VENDOR);
    }
}
