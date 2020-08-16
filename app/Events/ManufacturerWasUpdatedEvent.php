<?php

namespace App\Events;

use App\Models\Manufacturer;
use Illuminate\Queue\SerializesModels;

class ManufacturerWasUpdatedEvent extends Event
{
    use SerializesModels;

    /**
     * @var Manufacturer
     */
    public $manufacturer;

    /**
     * @var array
     **/
    public $input;

    /**
     * Create a new event instance.
     *
     * @param Manufacturer $manufacturer
     * @param null $input
     */
    public function __construct(Manufacturer $manufacturer, $input = null)
    {
        $this->manufacturer = $manufacturer;
        $this->input = $input;
    }
}
