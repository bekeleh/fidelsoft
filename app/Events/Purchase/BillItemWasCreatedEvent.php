<?php

namespace App\Events\Purchase;

use App\Events\Event;
use App\Models\BillItem;
use Illuminate\Queue\SerializesModels;

/**
 * Class BillItemWasCreatedEvent.
 */
class BillItemWasCreatedEvent extends Event
{
    use SerializesModels;

    /**
     * @var BillItem
     */
    public $billItem;

    /**
     * Create a new event instance.
     *
     * @param BillItem $billItem
     */
    public function __construct(BillItem $billItem)
    {
        $this->billItem = $billItem;
    }
}
