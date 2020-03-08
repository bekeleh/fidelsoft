<?php

namespace App\Events;

use App\Models\Location;
use Illuminate\Queue\SerializesModels;

class LocationWasUpdated extends Event
{
    use SerializesModels;

    /**
     * @var Location
     */
    public $location;

    /**
     * @var array
     **/
    public $input;

    /**
     * Create a new event instance.
     *
     * @param Location $location
     * @param null $input
     */
    public function __construct(Location $location, $input = null)
    {
        $this->location = $location;
        $this->input = $input;
    }
}
