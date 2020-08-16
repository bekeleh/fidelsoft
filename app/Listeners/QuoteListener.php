<?php

namespace App\Listeners;

use App\Events\QuoteInvitationWasApprovedEvent;
use App\Events\QuoteInvitationWasViewedEvent;
use App\Events\QuoteItemsWereCreatedEvent;
use App\Events\QuoteItemsWereUpdatedEvent;
use App\Events\QuoteWasDeletedEvent;
use App\Events\QuoteWasEmailedEvent;
use App\Ninja\Transformers\InvoiceTransformer;

/**
 * Class QuoteListener.
 */
class QuoteListener extends EntityListener
{
    public function viewedQuote(QuoteInvitationWasViewedEvent $event)
    {
        $invitation = $event->invitation;
        $invitation->markViewed();
    }

    public function emailedQuote(QuoteWasEmailedEvent $event)
    {
        $quote = $event->quote;
        $quote->last_sent_date = date('Y-m-d');

        $quote->save();
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

    public function approvedQuote(QuoteInvitationWasApprovedEvent $event)
    {
        $transformer = new InvoiceTransformer($event->quote->account);
        $this->checkSubscriptions(EVENT_APPROVE_QUOTE, $event->quote, $transformer, ENTITY_CLIENT);
    }

    public function deletedQuote(QuoteWasDeletedEvent $event)
    {
        $transformer = new InvoiceTransformer($event->quote->account);
        $this->checkSubscriptions(EVENT_DELETE_QUOTE, $event->quote, $transformer, ENTITY_CLIENT);
    }

}
