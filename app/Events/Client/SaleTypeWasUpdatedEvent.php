<?php

namespace App\Events\Client;

use App\Events\Event;
use App\Models\Setting\SaleType;
use Illuminate\Queue\SerializesModels;

class SaleTypeWasUpdatedEvent extends Event
{
    use SerializesModels;

    /**
     * @var SaleType
     */
    public $saleType;

    /**
     * @var array
     **/
    public $input;

    /**
     * Create a new event instance.
     *
     * @param SaleType $saleType
     */
    public function __construct(SaleType $saleType)
    {
        $this->saleType = $saleType;
    }
}
