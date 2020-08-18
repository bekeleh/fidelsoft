<?php namespace App\Listeners\Sale;

use App\Events\Sale\InvoiceInvitationWasViewedEvent;
use App\Events\Sale\InvoiceWasEmailedEvent;
use App\Events\Sale\PaymentWasCreatedEvent;
use App\Events\Sale\QuoteInvitationWasApprovedEvent;
use App\Events\Sale\QuoteInvitationWasViewedEvent;
use App\Events\Sale\QuoteWasEmailedEvent;
use App\Jobs\SendNotificationEmail;
use App\Jobs\SendPaymentEmail;
use App\Ninja\Mailers\ContactMailer;
use App\Ninja\Mailers\UserMailer;
use App\Notifications\Sale\NotifyInvoicePaymentCreated;
use App\Services\PushService;

/**
 * Class SendInvoiceNotification
 */
class SendInvoiceNotification
{
    /**
     * @var UserMailer
     */
    protected $userMailer;
    /**
     * @var ContactMailer
     */
    protected $contactMailer;
    /**
     * @var PushService
     */
    protected $pushService;

    /**
     * SendInvoiceNotification constructor.
     * @param UserMailer $userMailer
     * @param ContactMailer $contactMailer
     * @param PushService $pushService
     */
    public function __construct(
        UserMailer $userMailer,
        ContactMailer $contactMailer,
        PushService $pushService)
    {
        $this->userMailer = $userMailer;
        $this->contactMailer = $contactMailer;
        $this->pushService = $pushService;
    }

    private function sendNotifications($invoice, $type, $payment = null, $notes = false)
    {
        foreach ($invoice->account->users as $user) {
            if ($user->{"notify_{$type}"}) {
                dispatch(new SendNotificationEmail($user, $invoice, $type, $payment, $notes));
            }

            if ($payment && $user->slack_webhook_url) {
                $user->notify(new NotifyInvoicePaymentCreated($payment, $invoice));
            }
        }
    }

    /**
     * @param InvoiceWasEmailedEvent $event
     */
    public function emailedInvoice(InvoiceWasEmailedEvent $event)
    {
        $this->sendNotifications($event->invoice, 'sent', null, $event->notes);
        $this->pushService->sendNotification($event->invoice, 'sent');
    }

    /**
     * @param QuoteWasEmailedEvent $event
     */
    public function emailedQuote(QuoteWasEmailedEvent $event)
    {
        $this->sendNotifications($event->quote, 'sent', null, $event->notes);
        $this->pushService->sendNotification($event->quote, 'sent');
    }

    /**
     * @param InvoiceInvitationWasViewedEvent $event
     */
    public function viewedInvoice(InvoiceInvitationWasViewedEvent $event)
    {
        if (!floatval($event->invoice->balance)) {
            return;
        }

        $this->sendNotifications($event->invoice, 'viewed');
        $this->pushService->sendNotification($event->invoice, 'viewed');
    }

    /**
     * @param QuoteInvitationWasViewedEvent $event
     */
    public function viewedQuote(QuoteInvitationWasViewedEvent $event)
    {
        if ($event->quote->quote_invoice_id) {
            return;
        }

        $this->sendNotifications($event->quote, 'viewed');
        $this->pushService->sendNotification($event->quote, 'viewed');
    }

    /**
     * @param QuoteInvitationWasApprovedEvent $event
     */
    public function approvedQuote(QuoteInvitationWasApprovedEvent $event)
    {
        $this->sendNotifications($event->quote, 'approved');
        $this->pushService->sendNotification($event->quote, 'approved');
    }

    /**
     * @param PaymentWasCreatedEvent $event
     */
    public function createdPayment(PaymentWasCreatedEvent $event)
    {
        // only send emails for online payments
        if (!$event->payment->account_gateway_id) {
            return;
        }

        dispatch(new SendPaymentEmail($event->payment));
        $this->sendNotifications($event->payment->invoice, 'paid', $event->payment);

        $this->pushService->sendNotification($event->payment->invoice, 'paid');
    }

}
