<?php namespace App\Listeners;

use App\Ninja\Mailers\UserMailer;
use App\Ninja\Mailers\ContactMailer;
use App\Events\InvoiceWasEmailed;
use App\Events\QuoteWasEmailed;
use App\Events\InvoiceInvitationWasViewed;
use App\Events\QuoteInvitationWasViewed;
use App\Events\QuoteInvitationWasApproved;
use App\Events\PaymentWasCreated;
use App\Services\PushService;
use App\Jobs\SendPurchaseNotificationEmail;
use App\Jobs\SendPurchasePaymentEmail;
use App\Notifications\PurchasePaymentCreated;

/**
 * Class NotificationListener
 */
class PurchaseNotificationListener
{
    protected $userMailer;
    protected $contactMailer;
    protected $pushService;

    /**
     * NotificationListener constructor.
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
     * @param $purchaseInvoice
     * @param $type
     * @param null $purchasePayment
     * @param bool $notes
     */
    private function sendNotifications($purchaseInvoice, $type, $purchasePayment = null, $notes = false)
    {
        foreach ($purchaseInvoice->account->users as $user) {
            if ($user->{"notify_{$type}"}) {
                dispatch(new SendPurchaseNotificationEmail($user, $purchaseInvoice, $type, $purchasePayment, $notes));
            }

            if ($purchasePayment && $user->slack_webhook_url) {
                $user->notify(new PurchasePaymentCreated($purchasePayment, $purchaseInvoice));
            }
        }
    }

    /**
     * @param InvoiceWasEmailed $event
     */
    public function emailedInvoice(InvoiceWasEmailed $event)
    {
        $this->sendNotifications($event->invoice, 'sent', null, $event->notes);
        $this->pushService->sendNotification($event->invoice, 'sent');
    }

    /**
     * @param QuoteWasEmailed $event
     */
    public function emailedQuote(QuoteWasEmailed $event)
    {
        $this->sendNotifications($event->quote, 'sent', null, $event->notes);
        $this->pushService->sendNotification($event->quote, 'sent');
    }

    /**
     * @param InvoiceInvitationWasViewed $event
     */
    public function viewedInvoice(InvoiceInvitationWasViewed $event)
    {
        if (!floatval($event->invoice->balance)) {
            return;
        }

        $this->sendNotifications($event->invoice, 'viewed');
        $this->pushService->sendNotification($event->invoice, 'viewed');
    }

    /**
     * @param QuoteInvitationWasViewed $event
     */
    public function viewedQuote(QuoteInvitationWasViewed $event)
    {
        if ($event->quote->quote_invoice_id) {
            return;
        }

        $this->sendNotifications($event->quote, 'viewed');
        $this->pushService->sendNotification($event->quote, 'viewed');
    }

    /**
     * @param QuoteInvitationWasApproved $event
     */
    public function approvedQuote(QuoteInvitationWasApproved $event)
    {
        $this->sendNotifications($event->quote, 'approved');
        $this->pushService->sendNotification($event->quote, 'approved');
    }

    /**
     * @param PaymentWasCreated $event
     */
    public function createdPayment(PaymentWasCreated $event)
    {
        // only send emails for online payments
        if (!$event->payment->account_gateway_id) {
            return;
        }

        dispatch(new SendPurchasePaymentEmail($event->payment));
        $this->sendNotifications($event->payment->invoice, 'paid', $event->payment);

        $this->pushService->sendNotification($event->payment->invoice, 'paid');
    }

}
