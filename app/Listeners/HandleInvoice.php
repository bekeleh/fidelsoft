<?php

namespace App\Listeners;

use App\Events\InvoiceInvitationWasViewedEvent;
use App\Events\InvoiceWasCreatedEvent;
use App\Events\InvoiceWasEmailedEvent;
use App\Events\InvoiceWasUpdatedEvent;
use App\Events\PaymentFailedEvent;
use App\Events\PaymentWasCreatedEvent;
use App\Events\PaymentWasDeletedEvent;
use App\Events\PaymentWasRefundedEvent;
use App\Events\PaymentWasRestoredEvent;
use App\Events\PaymentWasVoidedEvent;
use App\Libraries\Utils;
use App\Models\Activity;
use App\Models\Subscription;
use App\Notifications\NotifyInvoiceCreated;
use App\Notifications\NotifyInvoiceUpdated;
use Illuminate\Notifications\Notifiable;
use Illuminate\Queue\Events\JobExceptionOccurred;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

/**
 * Class HandleInvoice.
 */
class HandleInvoice
{
    use Notifiable;

    /**
     * @param InvoiceWasCreatedEvent $event
     */
    public function createdInvoice(InvoiceWasCreatedEvent $event)
    {
        // Make sure the account has the same design set as the invoice does
        if (Auth::check()) {
            $invoice = $event->invoice;
            $account = Auth::user()->account;

            if ($invoice->invoice_design_id && $account->invoice_design_id != $invoice->invoice_design_id) {
                $account->invoice_design_id = $invoice->invoice_design_id;

                $account->save();
            }
        }

//     send notification for subscribers
        $invoice = $event->invoice->toArray();
        $users = Subscription::subscriber(EVENT_CREATE_INVOICE);
        if ($users) {
            foreach ($users as $user) {
                $user->notify(new NotifyInvoiceCreated($invoice), $invoice);
            }
        }

    }

    public function updatedInvoice(InvoiceWasUpdatedEvent $event)
    {
        $invoice = $event->invoice;

        $invoice->updatePaidStatus(false, false);

//      send notification for subscribers
        $this->notifyInvoiceUpdate($invoice);

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
            $invoiceRepo = app('App\Ninja\Repositories\InvoiceRepository');
            $invoiceRepo->archive($invoice);
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
    private function notifyInvoiceUpdate($invoice): void
    {
        $users = Subscription::subscriber(EVENT_UPDATE_INVOICE);
        if ($users) {
            foreach ($users as $user) {
                $user->notify(new NotifyInvoiceUpdated($invoice), $invoice);
//                $user = User::find($question->user_id);
//                Notification::send($user, new NotifyInvoiceUpdated($invoice));
            }
        }
    }
}
