<?php

namespace App\Events\Purchase;

use App\Events\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Class BillCreditWasArchivedEvent.
 */
class BillCreditWasArchivedEvent extends Event
{
    use Dispatchable, Queueable, SerializesModels;

    public $billCredit;

    /**
     * Create a new event instance.
     *
     * @param $billCredit
     */
    public function __construct($billCredit)
    {
        $this->billCredit = $billCredit;
    }
}
