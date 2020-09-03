<?php

namespace App\Jobs;

use App\Models\BillPayment;
use App\Ninja\Mailers\VendorContactMailer;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Class SendBillPaymentEmail.
 */
class SendBillPaymentEmail extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;


    protected $payment;
    protected $server;

    /**
     * Create a new job instance.
     * @param BillPayment $payment
     */
    public function __construct($payment)
    {
        $this->payment = $payment;
        $this->server = config('database.default');
    }

    /**
     * Execute the job.
     *
     * @param VendorContactMailer $contactMailer
     */
    public function handle(VendorContactMailer $contactMailer)
    {
        $contactMailer->sendBillPaymentConfirmation($this->payment);
    }
}
