<?php

namespace App\Listeners;

use App\Events\ExpenseWasCreatedEvent;
use App\Events\ExpenseWasDeletedEvent;
use App\Events\ExpenseWasUpdatedEvent;
use App\Events\InvoiceWasDeletedEvent;
use App\Models\Expense;
use App\Ninja\Repositories\ExpenseRepository;
use App\Ninja\Transformers\ExpenseTransformer;

/**
 * Class ExpenseListener.
 */
class ExpenseListener extends EntityListener
{
    protected $expenseRepo;

    /**
     * ExpenseListener constructor.
     *
     * @param ExpenseRepository $expenseRepo
     */
    public function __construct(ExpenseRepository $expenseRepo)
    {
        $this->expenseRepo = $expenseRepo;
    }

    public function createdExpense(ExpenseWasCreatedEvent $event)
    {
        $transformer = new ExpenseTransformer($event->expense->account);
        $this->checkSubscriptions(EVENT_CREATE_EXPENSE, $event->expense, $transformer);
    }

    public function updatedExpense(ExpenseWasUpdatedEvent $event)
    {
        $transformer = new ExpenseTransformer($event->expense->account);
        $this->checkSubscriptions(EVENT_UPDATE_EXPENSE, $event->expense, $transformer);
    }

    public function deletedExpense(ExpenseWasDeletedEvent $event)
    {
        $transformer = new ExpenseTransformer($event->expense->account);
        $this->checkSubscriptions(EVENT_DELETE_EXPENSE, $event->expense, $transformer);
    }

    public function deletedInvoice(InvoiceWasDeletedEvent $event)
    {
        // Release any tasks associated with the deleted invoice
        Expense::where('invoice_id', $event->invoice->id)
            ->update(['invoice_id' => null]);
    }
}
