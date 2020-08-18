<?php

namespace App\Events\Setting;

use App\Events\Event;
use App\Models\ItemStore;
use Illuminate\Queue\SerializesModels;

class ItemStoreWasCreatedEvent extends Event
{
    use SerializesModels;


    public $itemStore;

    public $input;

    /**
     * Create a new event instance.
     *
     * @param ItemStore $itemStore
     */
    public function __construct(ItemStore $itemStore)
    {
        $this->itemStore = $itemStore;
    }
}
