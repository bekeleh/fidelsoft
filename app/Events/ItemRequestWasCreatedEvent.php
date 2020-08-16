<?php

namespace App\Events;

use App\Models\ItemRequest;
use Illuminate\Queue\SerializesModels;

class ItemRequestWasCreatedEvent extends Event
{
    use SerializesModels;

    public $itemRequest;

    public $input;

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
