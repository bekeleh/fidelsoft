<?php

namespace App\Events\Client;

use App\Events\Event;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SaleTypeWasUpdatedEvent extends Event
{
    use Dispatchable, SerializesModels;


    public $saleType;
    public $input;


    public function __construct($saleType)
    {
        $this->saleType = $saleType;
    }
}
