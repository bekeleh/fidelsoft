<?php

namespace App\Events\Setting;

use App\Events\Event;
use App\Models\Product;
use Illuminate\Queue\SerializesModels;

class ProductWasDeletedEvent extends Event
{
    use SerializesModels;

    /**
     * @var Product
     */
    public $product;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Product $product)
    {
        $this->product = $product;
    }
}
