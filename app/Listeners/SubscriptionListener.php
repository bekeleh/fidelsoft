<?php

namespace App\Listeners;

use App\Events\ClientWasCreated;
use App\Events\ClientWasDeleted;
use App\Events\ClientWasUpdated;
use App\Events\ExpenseWasCreated;
use App\Events\ExpenseWasDeleted;
use App\Events\ExpenseWasUpdated;
use App\Events\InvoiceItemsWereCreated;
use App\Events\InvoiceItemsWereUpdated;
use App\Events\InvoiceWasDeleted;
use App\Events\PaymentWasCreated;
use App\Events\PaymentWasDeleted;
use App\Events\QuoteInvitationWasApproved;
use App\Events\QuoteItemsWereCreated;
use App\Events\QuoteItemsWereUpdated;
use App\Events\QuoteWasDeleted;
use App\Events\TaskWasCreated;
use App\Events\TaskWasDeleted;
use App\Events\TaskWasUpdated;
use App\Events\VendorWasCreated;
use App\Events\VendorWasDeleted;
use App\Events\VendorWasUpdated;
use App\Ninja\Transformers\ClientTransformer;
use App\Ninja\Transformers\ExpenseTransformer;
use App\Ninja\Transformers\InvoiceTransformer;
use App\Ninja\Transformers\PaymentTransformer;
use App\Ninja\Transformers\TaskTransformer;
use App\Ninja\Transformers\VendorTransformer;

/**
 * Class SubscriptionListener.
 */
class SubscriptionListener extends EntityListener
{

//  client Listener
    public function createdClient(ClientWasCreated $event)
    {
        $transformer = new ClientTransformer($event->client->account);
        $this->checkSubscriptions(EVENT_CREATE_CLIENT, $event->client, $transformer);
    }

    public function updatedClient(ClientWasUpdated $event)
    {
        $transformer = new ClientTransformer($event->client->account);
        $this->checkSubscriptions(EVENT_UPDATE_CLIENT, $event->client, $transformer);
    }

    public function deletedClient(ClientWasDeleted $event)
    {
        $transformer = new ClientTransformer($event->client->account);
        $this->checkSubscriptions(EVENT_DELETE_CLIENT, $event->client, $transformer);
    }

//  payment Listener
    public function createdPayment(PaymentWasCreated $event)
    {
        $transformer = new PaymentTransformer($event->payment->account);
        $this->checkSubscriptions(EVENT_CREATE_PAYMENT, $event->payment, $transformer, [ENTITY_CLIENT, ENTITY_INVOICE]);
    }

    public function deletedPayment(PaymentWasDeleted $event)
    {
        $transformer = new PaymentTransformer($event->payment->account);
        $this->checkSubscriptions(EVENT_DELETE_PAYMENT, $event->payment, $transformer, [ENTITY_CLIENT, ENTITY_INVOICE]);
    }

// Invoice Listener
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

//     Quote Listener
    public function createdQuote(QuoteItemsWereCreated $event)
    {
        $transformer = new InvoiceTransformer($event->quote->account);
        $this->checkSubscriptions(EVENT_CREATE_QUOTE, $event->quote, $transformer, ENTITY_CLIENT);
    }

    public function updatedQuote(QuoteItemsWereUpdated $event)
    {
        $transformer = new InvoiceTransformer($event->quote->account);
        $this->checkSubscriptions(EVENT_UPDATE_QUOTE, $event->quote, $transformer, ENTITY_CLIENT);
    }

    public function approvedQuote(QuoteInvitationWasApproved $event)
    {
        $transformer = new InvoiceTransformer($event->quote->account);
        $this->checkSubscriptions(EVENT_APPROVE_QUOTE, $event->quote, $transformer, ENTITY_CLIENT);
    }

    public function deletedQuote(QuoteWasDeleted $event)
    {
        $transformer = new InvoiceTransformer($event->quote->account);
        $this->checkSubscriptions(EVENT_DELETE_QUOTE, $event->quote, $transformer, ENTITY_CLIENT);
    }

//     Vendor Listener
    public function createdVendor(VendorWasCreated $event)
    {
        $transformer = new VendorTransformer($event->vendor->account);
        $this->checkSubscriptions(EVENT_CREATE_VENDOR, $event->vendor, $transformer);
    }

    public function updatedVendor(VendorWasUpdated $event)
    {
        $transformer = new VendorTransformer($event->vendor->account);
        $this->checkSubscriptions(EVENT_UPDATE_VENDOR, $event->vendor, $transformer);
    }

    public function deletedVendor(VendorWasDeleted $event)
    {
        $transformer = new VendorTransformer($event->vendor->account);
        $this->checkSubscriptions(EVENT_DELETE_VENDOR, $event->vendor, $transformer);
    }

//     Expense Listener
    public function createdExpense(ExpenseWasCreated $event)
    {
        $transformer = new ExpenseTransformer($event->expense->account);
        $this->checkSubscriptions(EVENT_CREATE_EXPENSE, $event->expense, $transformer);
    }

    public function updatedExpense(ExpenseWasUpdated $event)
    {
        $transformer = new ExpenseTransformer($event->expense->account);
        $this->checkSubscriptions(EVENT_UPDATE_EXPENSE, $event->expense, $transformer);
    }

    public function deletedExpense(ExpenseWasDeleted $event)
    {
        $transformer = new ExpenseTransformer($event->expense->account);
        $this->checkSubscriptions(EVENT_DELETE_EXPENSE, $event->expense, $transformer);
    }

//     Task listener
    public function createdTask(TaskWasCreated $event)
    {
        $transformer = new TaskTransformer($event->task->account);
        $this->checkSubscriptions(EVENT_CREATE_TASK, $event->task, $transformer);
    }

    public function updatedTask(TaskWasUpdated $event)
    {
        $transformer = new TaskTransformer($event->task->account);
        $this->checkSubscriptions(EVENT_UPDATE_TASK, $event->task, $transformer);
    }

    public function deletedTask(TaskWasDeleted $event)
    {
        $transformer = new TaskTransformer($event->task->account);
        $this->checkSubscriptions(EVENT_DELETE_TASK, $event->task, $transformer);
    }

}
