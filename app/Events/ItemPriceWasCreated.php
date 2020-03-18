<?php

namespace App\Events;

use App\Models\ItemPrice;
use Illuminate\Queue\SerializesModels;

/**
 * Class PriceWasCreated.
 */
class ItemPriceWasCreated extends Event
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
