<?php

namespace App\Listeners\Purchase;

use App\Events\Purchase\BillQuoteInvitationWasApprovedEvent;
use App\Events\Purchase\BillQuoteInvitationWasViewedEvent;
use App\Events\Purchase\BillQuoteItemsWereCreatedEvent;
use App\Events\Purchase\BillQuoteItemsWereUpdatedEvent;
use App\Events\Purchase\BillQuoteWasDeletedEvent;
use App\Events\Purchase\BillQuoteWasEmailedEvent;
use App\Ninja\Transformers\BillTransformer;
use App\Listeners\EntityListener;

/**
 * Class QuoteListener.
 */
class BillQuoteListener extends EntityListener
{
    public function viewedQuote(BillQuoteInvitationWasViewedEvent $event)
    {
        $invitation = $event->invitation;
        $invitation->markViewed();
    }

    public function emailedQuote(BillQuoteWasEmailedEvent $event)
    {
        $quote = $event->quote;
        $quote->last_sent_date = date('Y-m-d');

        $quote->save();
    }

    public function createdQuote(BillQuoteItemsWereCreatedEvent $event)
    {
        $transformer = new BillTransformer($event->quote->account);

        $this->checkSubscriptions(EVENT_CREATE_QUOTE, $event->quote, $transformer, ENTITY_VENDOR);
    }

    public function updatedQuote(BillQuoteItemsWereUpdatedEvent $event)
    {
        $transformer = new BillTransformer($event->quote->account);

        $this->checkSubscriptions(EVENT_UPDATE_QUOTE, $event->quote, $transformer, ENTITY_VENDOR);
    }

    public function approvedQuote(BillQuoteInvitationWasApprovedEvent $event)
    {
        $transformer = new BillTransformer($event->quote->account);

        $this->checkSubscriptions(EVENT_APPROVE_QUOTE, $event->quote, $transformer, ENTITY_VENDOR);
    }

    public function deletedQuote(BillQuoteWasDeletedEvent $event)
    {
        $transformer = new BillTransformer($event->quote->account);

        $this->checkSubscriptions(EVENT_DELETE_QUOTE, $event->quote, $transformer, ENTITY_VENDOR);
    }

}
