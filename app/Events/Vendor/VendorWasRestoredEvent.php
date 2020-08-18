<?php

namespace App\Events\Vendor;

use App\Events\Event;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Class VendorWasRestoredEvent.
 */
class VendorWasRestoredEvent extends Event
{
    use Dispatchable, SerializesModels;


    public $vendor;


    public function __construct($vendor)
    {
        $this->vendor = $vendor;
    }
}
