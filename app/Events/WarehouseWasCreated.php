<?php

namespace App\Events;

use App\Models\Warehouse;
use Illuminate\Queue\SerializesModels;

class WarehouseWasCreated extends Event
{
    use SerializesModels;

    /**
     * @var Warehouse
     */
    public $warehouse;

    /**
     * @var array
     **/
    public $input;

    /**
     * Create a new event instance.
     *
     * @param Warehouse $warehouse
     * @param null $input
     */
    public function __construct(Warehouse $warehouse, $input = null)
    {
        $this->warehouse = $warehouse;
        $this->input = $input;
    }
}
