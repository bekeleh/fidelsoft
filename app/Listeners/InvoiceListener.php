<?php

namespace App\Listeners;

use App\Events\InvoiceInvitationWasViewed;
use App\Events\InvoiceWasCreated;
use App\Events\InvoiceWasEmailed;
use App\Events\InvoiceWasUpdated;
use App\Events\PaymentFailed;
use App\Events\PaymentWasCreated;
use App\Events\PaymentWasDeleted;
use App\Events\PaymentWasRefunded;
use App\Events\PaymentWasRestored;
use App\Events\PaymentWasVoided;
use App\Libraries\Utils;
use App\Models\Activity;
use App\Models\Subscription;
use App\Notifications\InvoiceCreated;
use Illuminate\Queue\Events\JobExceptionOccurred;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Notifications\Notifiable;

/**
 * Class InvoiceListener.
 */
class InvoiceListener
{
    use Notifiable;

    public function createdInvoice(InvoiceWasCreated $event)
    {
//        if (Utils::hasFeature(FEATURE_DIFFERENT_DESIGNS)) {
//            return false;
//        }

        // Make sure the account has the same design set as the invoice does
        if (Auth::check()) {
            $invoice = $event->invoice;
            $account = Auth::user()->account;

            if ($invoice->invoice_design_id && $account->invoice_design_id != $invoice->invoice_design_id) {
                $account->invoice_design_id = $invoice->invoice_design_id;

                $account->save();
            }
        }
//          send notification for subscribers
        $invoice = $event->invoice->toArray();
        if (isset($invoice['question_id'])) {
            $user = Subscription::find($event->id);
            Notification::send($user, new InvoiceCreated($invoice));
        }
    }

    public function updatedInvoice(InvoiceWasUpdated $event)
    {
        $invoice = $event->invoice;

        $invoice->updatePaidStatus(false, false);
    }

    public function viewedInvoice(InvoiceInvitationWasViewed $event)
    {
        $invitation = $event->invitation;
        $invitation->markViewed();
    }


    public function emailedInvoice(InvoiceWasEmailed $event)
    {
        $invoice = $event->invoice;
        $invoice->last_sent_date = date('Y-m-d');

        $invoice->save();
    }

    public function createdPayment(PaymentWasCreated $event)
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

    public function deletedPayment(PaymentWasDeleted $event)
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

    public function refundedPayment(PaymentWasRefunded $event)
    {
        $payment = $event->payment;
        $invoice = $payment->invoice;
        $adjustment = $event->refundAmount;

        $invoice->updateBalances($adjustment);
        $invoice->updatePaidStatus();
    }

    public function voidedPayment(PaymentWasVoided $event)
    {
        $payment = $event->payment;
        $invoice = $payment->invoice;
        $adjustment = $payment->amount;

        $invoice->updateBalances($adjustment);
        $invoice->updatePaidStatus();
    }

    public function failedPayment(PaymentFailed $event)
    {
        $payment = $event->payment;
        $invoice = $payment->invoice;
        $adjustment = $payment->getCompletedAmount();

        $invoice->updateBalances($adjustment);
        $invoice->updatePaidStatus();
    }

    public function restoredPayment(PaymentWasRestored $event)
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
}
