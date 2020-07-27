<?php

namespace App\Listeners;

use App\Events\QuoteInvitationWasApproved;
use App\Events\QuoteInvitationWasViewed;
use App\Events\QuoteItemsWereCreated;
use App\Events\QuoteItemsWereUpdated;
use App\Events\QuoteWasDeleted;
use App\Events\QuoteWasEmailed;
use App\Ninja\Transformers\InvoiceTransformer;

/**
 * Class QuoteListener.
 */
class QuoteListener extends EntityListener
{
    public function viewedQuote(QuoteInvitationWasViewed $event)
    {
        $invitation = $event->invitation;
        $invitation->markViewed();
    }

    public function emailedQuote(QuoteWasEmailed $event)
    {
        $quote = $event->quote;
        $quote->last_sent_date = date('Y-m-d');

        $quote->save();
    }

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

}
