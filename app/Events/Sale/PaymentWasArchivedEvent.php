<?php

namespace App\Events\Sale;

use App\Events\Event;
use App\Models\Payment;
use Illuminate\Queue\SerializesModels;

/**
 * Class PaymentWasArchivedEvent.
 */
class PaymentWasArchivedEvent extends Event
{
    use SerializesModels;

    /**
     * @var Payment
     */
    public $payment;

    /**
     * Create a new event instance.
     *
     * @param Payment $payment
     */
    public function __construct(Payment $payment)
    {
        $this->payment = $payment;
    }
}
