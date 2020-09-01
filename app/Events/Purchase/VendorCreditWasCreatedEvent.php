<?php

namespace App\Events\Purchase;

use App\Events\Event;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class VendorCreditWasCreatedEvent extends Event
{
    use Dispatchable, SerializesModels;

    public $billCredit;


    public function __construct($billCredit)
    {
        $this->billCredit = $billCredit;
    }
}
