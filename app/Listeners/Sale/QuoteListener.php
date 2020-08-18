<?php

namespace App\Listeners\Sale;

use App\Events\Sale\QuoteInvitationWasApprovedEvent;
use App\Events\Sale\QuoteInvitationWasViewedEvent;
use App\Events\Sale\QuoteItemsWereCreatedEvent;
use App\Events\Sale\QuoteItemsWereUpdatedEvent;
use App\Events\Sale\QuoteWasDeletedEvent;
use App\Events\Sale\QuoteWasEmailedEvent;
use App\Ninja\Transformers\InvoiceTransformer;
use App\Listeners\EntityListener;

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
