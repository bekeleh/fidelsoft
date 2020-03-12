<?php

namespace App\Events;

use App\Models\ItemStore;
use Illuminate\Queue\SerializesModels;

class ItemStoreWasUpdated extends Event
{
    use SerializesModels;

    /**
     * @var ItemStore
     */
    public $itemStore;

    /**
     * @var array
     **/
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
