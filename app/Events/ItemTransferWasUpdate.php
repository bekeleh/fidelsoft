<?php

namespace App\Events;

use App\Models\ItemTransfer;
use Illuminate\Queue\SerializesModels;

class ItemTransferWasUpdate extends Event
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
