<?php

namespace App\Events\Expense;

use App\Events\Event;
use App\Models\Expense;
use Illuminate\Queue\SerializesModels;

/**
 * Class ExpenseWasArchivedEvent.
 */
class ExpenseWasArchivedEvent extends Event
{
    use SerializesModels;

    /**
     * @var Expense
     */
    public $expense;

    /**
     * Create a new event instance.
     *
     * @param Expense $expense
     */
    public function __construct(Expense $expense)
    {
        $this->expense = $expense;
    }
}
