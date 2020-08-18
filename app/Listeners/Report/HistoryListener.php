<?php

namespace App\Listeners\Report;

use App\Events\Sale\InvoiceWasDeletedEvent;
use App\Events\Client\ClientWasDeletedEvent;
use App\Events\Sale\QuoteWasDeletedEvent;
use App\Events\Expense\ExpenseWasDeletedEvent;
use App\Events\Setting\TaskWasDeletedEvent;
use App\Events\Setting\ProjectWasDeletedEvent;
use App\Events\Setting\ProposalWasDeletedEvent;
use App\Libraries\HistoryUtils;

/**
 * Class InvoiceListener.
 */
class HistoryListener
{
    /**
     * @param ClientWasDeletedEvent $event
     */
    public function deletedClient(ClientWasDeletedEvent $event)
    {
        HistoryUtils::deleteHistory($event->client);
    }

    /**
     * @param InvoiceWasDeletedEvent $event
     */
    public function deletedInvoice(InvoiceWasDeletedEvent $event)
    {
        HistoryUtils::deleteHistory($event->invoice);
    }

    /**
     * @param QuoteWasDeletedEvent $event
     */
    public function deletedQuote(QuoteWasDeletedEvent $event)
    {
        HistoryUtils::deleteHistory($event->quote);
    }

    /**
     * @param TaskWasDeletedEvent $event
     */
    public function deletedTask(TaskWasDeletedEvent $event)
    {
        HistoryUtils::deleteHistory($event->task);
    }

    /**
     * @param ExpenseWasDeletedEvent $event
     */
    public function deletedExpense(ExpenseWasDeletedEvent $event)
    {
        HistoryUtils::deleteHistory($event->expense);
    }

    /**
     * @param ProjectWasDeletedEvent $event
     */
    public function deletedProject(ProjectWasDeletedEvent $event)
    {
        HistoryUtils::deleteHistory($event->project);
    }

    /**
     * @param ProposalWasDeletedEvent $event
     */
    public function deletedProposal(ProposalWasDeletedEvent $event)
    {
        HistoryUtils::deleteHistory($event->proposal);
    }
}
