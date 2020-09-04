<?php

namespace App\Events\Purchase;

use App\Events\Event;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Class VendorCreditWasArchivedEvent.
 */
class VendorCreditWasArchivedEvent extends Event
{
    use Dispatchable, SerializesModels;

    public $credit;


    public function __construct($credit)
    {
        $this->credit = $credit;
    }
}
