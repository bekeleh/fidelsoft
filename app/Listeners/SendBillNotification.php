<?php namespace App\Listeners;

use App\Ninja\Mailers\UserMailer;
use App\Ninja\Mailers\ContactMailer;
use App\Events\BillWasEmailedEvent;
use App\Events\BillQuoteWasEmailedEvent;
use App\Events\BillInvitationWasViewedEvent;
use App\Events\BillQuoteInvitationWasViewedEvent;
use App\Events\BillQuoteInvitationWasApprovedEvent;
use App\Events\PaymentWasCreatedEvent;
use App\Services\PushService;
use App\Jobs\SendBillNotificationEmail;
use App\Jobs\SendBillPaymentEmail;
use App\Notifications\NotifyBillPaymentCreated;

/**
 * Class SendInvoiceNotification
 */
class SendBillNotification
{
    protected $userMailer;
    protected $contactMailer;
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

    /**
     * @param $bill
     * @param $type
     * @param null $billPayment
     * @param bool $notes
     */
    private function sendNotifications($bill, $type, $billPayment = null, $notes = false)
    {
        foreach ($bill->account->users as $user) {
            if ($user->{"notify_{$type}"}) {
                dispatch(new SendBillNotificationEmail($user, $bill, $type, $billPayment, $notes));
            }

            if ($billPayment && $user->slack_webhook_url) {
                $user->notify(new NotifyBillPaymentCreated($billPayment, $bill));
            }
        }
    }

    /**
     * @param BillWasEmailedEvent $event
     */
    public function emailedInvoice(BillWasEmailedEvent $event)
    {
        $this->sendNotifications($event->invoice, 'sent', null, $event->notes);
        $this->pushService->sendNotification($event->invoice, 'sent');
    }

    /**
     * @param BillQuoteWasEmailedEvent $event
     */
    public function emailedQuote(BillQuoteWasEmailedEvent $event)
    {
        $this->sendNotifications($event->quote, 'sent', null, $event->notes);
        $this->pushService->sendNotification($event->quote, 'sent');
    }

    /**
     * @param BillInvitationWasViewedEvent $event
     */
    public function viewedInvoice(BillInvitationWasViewedEvent $event)
    {
        if (!floatval($event->invoice->balance)) {
            return;
        }

        $this->sendNotifications($event->invoice, 'viewed');
        $this->pushService->sendNotification($event->invoice, 'viewed');
    }

    /**
     * @param BillQuoteInvitationWasViewedEvent $event
     */
    public function viewedQuote(BillQuoteInvitationWasViewedEvent $event)
    {
        if ($event->quote->quote_invoice_id) {
            return;
        }

        $this->sendNotifications($event->quote, 'viewed');
        $this->pushService->sendNotification($event->quote, 'viewed');
    }

    /**
     * @param BillQuoteInvitationWasApprovedEvent $event
     */
    public function approvedQuote(BillQuoteInvitationWasApprovedEvent $event)
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

        dispatch(new SendBillPaymentEmail($event->payment));
        $this->sendNotifications($event->payment->invoice, 'paid', $event->payment);

        $this->pushService->sendNotification($event->payment->invoice, 'paid');
    }

}
