<?php

namespace App\Events\Client;

use App\Events\Event;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Class CreditWasRestoredEvent.
 */
class CreditWasRestoredEvent extends Event
{
    use Dispatchable, SerializesModels;


    public $credit;


    public function __construct($credit)
    {
        $this->credit = $credit;
    }
}
