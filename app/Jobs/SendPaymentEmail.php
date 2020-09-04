<?php

namespace App\Jobs;

use App\Models\Payment;
use App\Ninja\Mailers\ClientMailer;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Class SendInvoiceEmail.
 */
class SendPaymentEmail extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;


    protected $payment;
    protected $server;

    /**
     * Create a new job instance.

     * @param Payment $payment
     */
    public function __construct($payment)
    {
        $this->payment = $payment;
        $this->server = config('database.default');
    }

    /**
     * Execute the job.
     *
     * @param ClientMailer $contactMailer
     */
    public function handle(ClientMailer $contactMailer)
    {
        $contactMailer->sendPaymentConfirmation($this->payment);
    }
}
