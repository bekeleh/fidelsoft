<?php

namespace App\Events\Setting;

use App\Events\Event;
use App\Models\ItemTransfer;
use Illuminate\Queue\SerializesModels;

class ItemTransferWasUpdatedEvent extends Event
{
    use SerializesModels;

    public $itemTransfer;


    public $input;

    /**
     * Create a new event instance.
     *
     * @param ItemTransfer $itemTransfer
     */
    public function __construct(ItemTransfer $itemTransfer)
    {
        $this->itemTransfer = $itemTransfer;
    }
}
