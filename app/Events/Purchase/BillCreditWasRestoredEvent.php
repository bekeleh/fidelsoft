<?php

namespace App\Events\Purchase;

use App\Events\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BillCreditWasRestoredEvent extends Event
{
    use Dispatchable, Queueable, SerializesModels;

    public $billCredit;


    public function __construct($billCredit)
    {
        $this->billCredit = $billCredit;
    }
}
