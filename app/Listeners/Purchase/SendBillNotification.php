<?php namespace App\Listeners\Purchase;

use App\Ninja\Mailers\BillUserMailer;
use App\Ninja\Mailers\VendorContactMailer;
use App\Events\Purchase\BillWasEmailedEvent;
use App\Events\Purchase\BillQuoteWasEmailedEvent;
use App\Events\Purchase\BillInvitationWasViewedEvent;
use App\Events\Purchase\BillQuoteInvitationWasViewedEvent;
use App\Events\Purchase\BillQuoteInvitationWasApprovedEvent;
use App\Events\Purchase\BillPaymentWasCreatedEvent;
use App\Services\PushService;
use App\Jobs\SendBillNotificationEmail;
use App\Jobs\SendBillPaymentEmail;
use App\Notifications\Purchase\NotifyBillPaymentCreated;

/**
 * Class SendBillNotification
 */
class SendBillNotification
{
    protected $billMailer;
    protected $contactMailer;
    protected $pushService;

    /**
     * SendInvoiceNotification constructor.
     * @param BillUserMailer $billMailer
     * @param VendorContactMailer $contactMailer
     * @param PushService $pushService
     */
    public function __construct(BillUserMailer $billMailer, VendorContactMailer $contactMailer, PushService $pushService)
    {
        $this->billMailer = $billMailer;
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
        $this->sendNotifications($event->bill, 'sent', null, $event->notes);

        $this->pushService->sendNotification($event->bill, 'sent');
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
        if (!floatval($event->bill->balance)) {
            return;
        }

        $this->sendNotifications($event->bill, 'viewed');

        $this->pushService->sendNotification($event->bill, 'viewed');
    }

    /**
     * @param BillQuoteInvitationWasViewedEvent $event
     */
    public function viewedQuote(BillQuoteInvitationWasViewedEvent $event)
    {
        if ($event->quote->quote_bill_id) {
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
     * @param BillPaymentWasCreatedEvent $event
     */
    public function createdPayment(BillPaymentWasCreatedEvent $event)
    {
        // only send emails for online payments
        if (!$event->payment->account_gateway_id) {
            return;
        }

        dispatch(new SendBillPaymentEmail($event->payment));

        $this->sendNotifications($event->payment->bill, 'paid', $event->payment);

        $this->pushService->sendNotification($event->payment->bill, 'paid');
    }

}
