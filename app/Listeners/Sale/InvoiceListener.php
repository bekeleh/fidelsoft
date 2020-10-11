<?php

namespace App\Listeners\Sale;

use App\Events\Sale\InvoiceInvitationWasViewedEvent;
use App\Events\Sale\InvoiceWasArchivedEvent;
use App\Events\Sale\InvoiceWasCreatedEvent;
use App\Events\Sale\InvoiceWasDeletedEvent;
use App\Events\Sale\InvoiceWasEmailedEvent;
use App\Events\Sale\InvoiceWasUpdatedEvent;
use App\Events\Sale\PaymentFailedEvent;
use App\Events\Sale\PaymentWasCreatedEvent;
use App\Events\Sale\PaymentWasDeletedEvent;
use App\Events\Sale\PaymentWasRefundedEvent;
use App\Events\Sale\PaymentWasRestoredEvent;
use App\Events\Sale\PaymentWasVoidedEvent;
use App\Libraries\Utils;
use App\Models\Activity;
use App\Models\Subscription;
use App\Models\User;
use App\Notifications\Sale\NotifyInvoiceCreated;
use App\Notifications\Sale\NotifyInvoiceDeleted;
use App\Notifications\Sale\NotifyInvoiceUpdated;
use Illuminate\Queue\Events\JobExceptionOccurred;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;

/**
 * Class InvoiceListener.
 */
class InvoiceListener
{
    public $title;

    public function createdInvoice(InvoiceWasCreatedEvent $event)
    {
        $invoice = $event->invoice;
        // Make sure the account has the same design set as the invoice does
        if (Auth::check()) {
            $account = Auth::user()->account;
            if ($invoice->invoice_design_id && $account->invoice_design_id != $invoice->invoice_design_id) {
                $account->invoice_design_id = $invoice->invoice_design_id;

                $account->save();
            }
        }

//     send notification for subscribers
        $this->notifyInvoiceCreated($invoice);

    }

    public function updatedInvoice(InvoiceWasUpdatedEvent $event)
    {
        $invoice = $event->invoice;

        $invoice->updatePaidStatus(false, false);

//      send notification for subscribers
//        $this->notifyInvoiceUpdated($invoice);

    }

    public function deletedInvoice(InvoiceWasDeletedEvent $event)
    {
        $invoice = $event->invoice;

//      send notification for subscribers
        $this->notifyInvoiceDeleted($invoice);

    }

    public function viewedInvoice(InvoiceInvitationWasViewedEvent $event)
    {
        $invitation = $event->invitation;
        $invitation->markViewed();
    }


    public function emailedInvoice(InvoiceWasEmailedEvent $event)
    {
        $invoice = $event->invoice;
        $invoice->last_sent_date = date('Y-m-d');

        $invoice->save();
    }

    public function createdPayment(PaymentWasCreatedEvent $event)
    {
        $payment = $event->payment;
        $invoice = $payment->invoice;
        $adjustment = $payment->amount * -1;
        $partial = max(0, $invoice->partial - $payment->amount);

        $invoice->updateBalances($adjustment, $partial);
        $invoice->updatePaidStatus(true);

        // store a backup of the invoice
        $activity = Activity::where('payment_id', $payment->id)
            ->where('activity_type_id', ACTIVITY_TYPE_CREATE_PAYMENT)
            ->first();
        $activity->json_backup = $invoice->hidePrivateFields()->toJSON();
        $activity->save();

        if ($invoice->balance == 0 && $payment->account->auto_archive_invoice) {
//          $invoiceRepo = app('App\Ninja\Repositories\InvoiceRepository');
//          $invoiceRepo->archive($invoice);
            $invoice->delete();
            event(new InvoiceWasArchivedEvent($invoice));
        }
    }

    public function deletedPayment(PaymentWasDeletedEvent $event)
    {
        $payment = $event->payment;

        if ($payment->isFailedOrVoided()) {
            return;
        }

        $invoice = $payment->invoice;
        $adjustment = $payment->getCompletedAmount();

        $invoice->updateBalances($adjustment);
        $invoice->updatePaidStatus();
    }

    public function refundedPayment(PaymentWasRefundedEvent $event)
    {
        $payment = $event->payment;
        $invoice = $payment->invoice;
        $adjustment = $event->refundAmount;

        $invoice->updateBalances($adjustment);
        $invoice->updatePaidStatus();
    }

    public function voidedPayment(PaymentWasVoidedEvent $event)
    {
        $payment = $event->payment;
        $invoice = $payment->invoice;
        $adjustment = $payment->amount;

        $invoice->updateBalances($adjustment);
        $invoice->updatePaidStatus();
    }

    public function failedPayment(PaymentFailedEvent $event)
    {
        $payment = $event->payment;
        $invoice = $payment->invoice;
        $adjustment = $payment->getCompletedAmount();

        $invoice->updateBalances($adjustment);
        $invoice->updatePaidStatus();
    }

    public function restoredPayment(PaymentWasRestoredEvent $event)
    {
        if (!$event->fromDeleted) {
            return;
        }

        $payment = $event->payment;

        if ($payment->isFailedOrVoided()) {
            return;
        }

        $invoice = $payment->invoice;
        $adjustment = $payment->getCompletedAmount() * -1;

        $invoice->updateBalances($adjustment);
        $invoice->updatePaidStatus();
    }

    public function jobFailed(JobExceptionOccurred $exception)
    {
        if ($errorEmail = env('ERROR_EMAIL')) {
            Mail::raw(print_r($exception->data, true), function ($message) use ($errorEmail) {
                $message->to($errorEmail)
                    ->from(CONTACT_EMAIL)
                    ->subject('Job failed');
            });
        }

        Utils::logError($exception->exception);
    }

    /**
     * @param $invoice
     */
    private function notifyInvoiceCreated($invoice): void
    {
//      get event subscriber
        $subscribers = Subscription::subscriber(EVENT_CREATE_INVOICE);
        $users = User::whereIn('id', $subscribers)->get();
        $this->title = trans('texts.created_invoice');
        if ($users) {
            Notification::send($users, new NotifyInvoiceCreated($invoice, $this->title));
        }
    }

    /**
     * @param $invoice
     */
    private function notifyInvoiceUpdated($invoice): void
    {
//      get event subscriber
        $subscribers = Subscription::subscriber(EVENT_UPDATE_INVOICE);
        $users = User::whereIn('id', $subscribers)->get();
        $this->title = trans('texts.updated_invoice');
        if ($users) {
            Notification::send($users, new NotifyInvoiceUpdated($invoice, $this->title));
        }
    }

    /**
     * @param $invoice
     */
    private function notifyInvoiceDeleted($invoice): void
    {
//      get event subscriber
        $subscribers = Subscription::subscriber(EVENT_DELETE_INVOICE);
        $users = User::whereIn('id', $subscribers)->get();
        $this->title = trans('texts.deleted_invoice');
        if ($users) {
            Notification::send($users, new NotifyInvoiceDeleted($invoice, $this->title));
        }
    }
}
