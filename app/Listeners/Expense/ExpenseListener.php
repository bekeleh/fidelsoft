<?php

namespace App\Listeners\Expense;

use App\Events\Expense\ExpenseWasCreatedEvent;
use App\Events\Expense\ExpenseWasDeletedEvent;
use App\Events\Expense\ExpenseWasUpdatedEvent;
use App\Events\Sale\InvoiceWasDeletedEvent;
use App\Models\Expense;
use App\Ninja\Repositories\ExpenseRepository;
use App\Ninja\Transformers\ExpenseTransformer;
use App\Listeners\EntityListener;

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
        return Expense::where('invoice_id', $event->invoice->id)
            ->update(['invoice_id' => null]);
    }
}
