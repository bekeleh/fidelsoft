<?php

namespace App\Events\Vendor;

use App\Events\Event;
use App\Models\Vendor;
use Illuminate\Queue\SerializesModels;

/**
 * Class VendorWasUpdatedEvent.
 */
class VendorWasUpdatedEvent extends Event
{
    use SerializesModels;
    public $vendor;

    /**
     * Create a new event instance.
     *
     * @param Vendor $vendor
     */
    public function __construct(Vendor $vendor)
    {
        $this->vendor = $vendor;
    }
}
