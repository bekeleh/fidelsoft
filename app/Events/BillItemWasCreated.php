<?php

namespace App\Events;

use App\Models\BillItem;
use Illuminate\Queue\SerializesModels;

/**
 * Class BillItemWasCreated.
 */
class BillItemWasCreated extends Event
{
    use SerializesModels;

    /**
     * @var BillItem
     */
    public $billItem;

    /**
     * Create a new event instance.
     *
     * @param BillItem $billItem
     */
    public function __construct(BillItem $billItem)
    {
        $this->billItem = $billItem;
    }
}
