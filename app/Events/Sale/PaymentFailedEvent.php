<?php

namespace App\Events\Sale;

use App\Events\Event;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Class PaymentFailedEvent.
 */
class PaymentFailedEvent extends Event
{
    use Dispatchable, SerializesModels;


    public $payment;
    public $title;

    /**
     * PaymentFailedEvent constructor.
     * @param $payment
     * @param null $title
     */
    public function __construct($payment, $title = null)
    {
        $this->payment = $payment;
        $this->title = $title;
    }
}
