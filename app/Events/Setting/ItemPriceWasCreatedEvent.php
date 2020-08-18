<?php

namespace App\Events\Setting;

use App\Events\Event;
use App\Models\ItemPrice;
use Illuminate\Queue\SerializesModels;

/**
 * Class PriceWasCreated.
 */
class ItemPriceWasCreatedEvent extends Event
{
    use SerializesModels;

    /**
     * @var ItemPrice
     */
    public $price;

    /**
     * Create a new event instance.
     *
     * @param ItemPrice $price
     */
    public function __construct(ItemPrice $price)
    {
        $this->price = $price;
    }
}
