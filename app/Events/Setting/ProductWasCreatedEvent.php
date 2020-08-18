<?php

namespace App\Events\Setting;

use App\Events\Event;
use App\Models\Product;
use Illuminate\Queue\SerializesModels;

class ProductWasCreatedEvent extends Event
{
    use SerializesModels;

    /**
     * @var Product
     */
    public $product;

    /**
     * @var array
     **/
    public $input;

    /**
     * Create a new event instance.
     *
     * @param Product $product
     * @param null $input
     */
    public function __construct(Product $product, $input = null)
    {
        $this->product = $product;
        $this->input = $input;
    }
}
