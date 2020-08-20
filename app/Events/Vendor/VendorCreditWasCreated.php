<?php

namespace App\Events\Vendor;

use App\Events\Event;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class VendorCreditWasCreated extends Event
{
    use Dispatchable, SerializesModels;


    public $credit;


    public function __construct($credit)
    {
        $this->credit = $credit;
    }
}
