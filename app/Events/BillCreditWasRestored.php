<?php

namespace App\Events;

use App\Models\BillCredit;
use Illuminate\Queue\SerializesModels;

class BillCreditWasRestored extends Event
{
    use SerializesModels;

    /**
     * @var BillCredit
     */
    public $billCredit;

    /**
     * Create a new event instance.
     *
     * @param BillCredit $billCredit
     */
    public function __construct(BillCredit $billCredit)
    {
        $this->billCredit = $billCredit;
    }
}
