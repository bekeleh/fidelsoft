<?php namespace App\Listeners;

use App\Ninja\Mailers\UserMailer;
use App\Ninja\Mailers\ContactMailer;
use App\Events\BillWasEmailed;
use App\Events\BillQuoteWasEmailed;
use App\Events\BillInvitationWasViewed;
use App\Events\BillQuoteInvitationWasViewed;
use App\Events\BillQuoteInvitationWasApproved;
use App\Events\PaymentWasCreated;
use App\Services\PushService;
use App\Jobs\SendBillNotificationEmail;
use App\Jobs\SendBillPaymentEmail;
use App\Notifications\NotifyBillPaymentCreated;

/**
 * Class NotifyListener
 */
class NotifyBillListener
{
    protected $userMailer;
    protected $contactMailer;
    protected $pushService;

    /**
     * NotifyListener constructor.
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
     * @param BillWasEmailed $event
     */
    public function emailedInvoice(BillWasEmailed $event)
    {
        $this->sendNotifications($event->invoice, 'sent', null, $event->notes);
        $this->pushService->sendNotification($event->invoice, 'sent');
    }

    /**
     * @param BillQuoteWasEmailed $event
     */
    public function emailedQuote(BillQuoteWasEmailed $event)
    {
        $this->sendNotifications($event->quote, 'sent', null, $event->notes);
        $this->pushService->sendNotification($event->quote, 'sent');
    }

    /**
     * @param BillInvitationWasViewed $event
     */
    public function viewedInvoice(BillInvitationWasViewed $event)
    {
        if (!floatval($event->invoice->balance)) {
            return;
        }

        $this->sendNotifications($event->invoice, 'viewed');
        $this->pushService->sendNotification($event->invoice, 'viewed');
    }

    /**
     * @param BillQuoteInvitationWasViewed $event
     */
    public function viewedQuote(BillQuoteInvitationWasViewed $event)
    {
        if ($event->quote->quote_invoice_id) {
            return;
        }

        $this->sendNotifications($event->quote, 'viewed');
        $this->pushService->sendNotification($event->quote, 'viewed');
    }

    /**
     * @param BillQuoteInvitationWasApproved $event
     */
    public function approvedQuote(BillQuoteInvitationWasApproved $event)
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

        dispatch(new SendBillPaymentEmail($event->payment));
        $this->sendNotifications($event->payment->invoice, 'paid', $event->payment);

        $this->pushService->sendNotification($event->payment->invoice, 'paid');
    }

}
