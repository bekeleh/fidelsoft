<?php

namespace App\Events\Setting;

use App\Events\Event;
use App\Models\ItemRequest;
use Illuminate\Queue\SerializesModels;

class ItemRequestWasUpdatedEvent extends Event
{
    use SerializesModels;

    public $itemRequest;

    /**
     * Create a new event instance.
     *
     * @param ItemRequest $itemRequest
     */
    public function __construct(ItemRequest $itemRequest)
    {
        $this->itemRequest = $itemRequest;
    }
}
