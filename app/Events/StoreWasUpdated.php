<?php

namespace App\Events;

use App\Models\Store;
use Illuminate\Queue\SerializesModels;

class StoreWasUpdated extends Event
{
    use SerializesModels;

    /**
     * @var Store
     */
    public $store;

    /**
     * @var array
     **/
    public $input;

    /**
     * Create a new event instance.
     *
     * @param Store $store
     * @param null $input
     */
    public function __construct(Store $store, $input = null)
    {
        $this->store = $store;
        $this->input = $input;
    }
}
