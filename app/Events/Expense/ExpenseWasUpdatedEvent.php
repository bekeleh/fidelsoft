<?php

namespace App\Events\Expense;

use App\Events\Event;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Class ExpenseWasUpdatedEvent.
 */
class ExpenseWasUpdatedEvent extends Event
{
    use Dispatchable, SerializesModels;


    public $expense;


    public function __construct($expense)
    {
        $this->expense = $expense;
    }
}
