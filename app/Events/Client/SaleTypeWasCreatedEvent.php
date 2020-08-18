<?php

namespace App\Events\Client;

use App\Events\Event;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Class SaleTypeWasDeleted.
 */
class SaleTypeWasCreatedEvent extends Event
{
    use Dispatchable, SerializesModels;


    public $saleType;


    public function __construct($saleType)
    {
        $this->saleType = $saleType;
    }
}
