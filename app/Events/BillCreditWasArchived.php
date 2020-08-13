<?php

namespace App\Events;

use App\Models\BillCredit;
use Illuminate\Queue\SerializesModels;

/**
 * Class BillCreditWasArchived.
 */
class BillCreditWasArchived extends Event
{
    use SerializesModels;

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
