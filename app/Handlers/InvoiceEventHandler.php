<?php

namespace App\Handlers;

use App\Ninja\Mailers\ClientMailer;
use App\Ninja\Mailers\InvoiceMailer;

class InvoiceEventHandler
{
    protected $userMailer;
    protected $contactMailer;

    public function __construct(InvoiceMailer $userMailer, ClientMailer $contactMailer)
    {
        $this->userMailer = $userMailer;
        $this->contactMailer = $contactMailer;
    }

    public function subscribe($events)
    {
        $events->listen('invoice.sent', 'InvoiceEventHandler@onSent');
        $events->listen('invoice.viewed', 'InvoiceEventHandler@onViewed');
        $events->listen('invoice.paid', 'InvoiceEventHandler@onPaid');
    }

    public function onSent($invoice)
    {
        $this->sendNotifications($invoice, 'sent');
    }

    public function onViewed($invoice)
    {
        $this->sendNotifications($invoice, 'viewed');
    }

    public function onPaid($payment)
    {
        $this->contactMailer->sendPaymentConfirmation($payment);

        $this->sendNotifications($payment->invoice, 'paid', $payment);
    }

    private function sendNotifications($invoice, $type, $payment = null)
    {
        foreach ($invoice->account->users as $user) {
            if ($user->{'notify_' . $type}) {
                $this->userMailer->sendNotification($user, $invoice, $type, $payment);
            }
        }
    }
}
