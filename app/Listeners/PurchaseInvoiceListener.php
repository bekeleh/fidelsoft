<?php

namespace App\Listeners;

use App\Events\BillInvitationWasViewed;
use App\Events\BillWasCreated;
use App\Events\BillWasEmailed;
use App\Events\BillWasUpdated;
use App\Events\BillPaymentFailed;
use App\Events\BillPaymentWasCreated;
use App\Events\BillPaymentWasDeleted;
use App\Events\BillPaymentWasRefunded;
use App\Events\BillPaymentWasRestored;
use App\Events\BillPaymentWasVoided;
use App\Libraries\Utils;
use App\Models\Activity;
use Illuminate\Queue\Events\JobExceptionOccurred;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

/**
 * Class BillListener.
 */
class BillListener
{
    public function createdInvoice(BillWasCreated $event)
    {
//        if (Utils::hasFeature(FEATURE_DIFFERENT_DESIGNS)) {
//            return false;
//        }

        // Make sure the account has the same design set as the invoice does
        if (Auth::check()) {
            $invoice = $event->Bill;
            $account = Auth::user()->account;

            if ($invoice->invoice_design_id && $account->invoice_design_id != $invoice->invoice_design_id) {
                $account->invoice_design_id = $invoice->invoice_design_id;

                $account->save();
            }
        }
    }

    public function updatedInvoice(BillWasUpdated $event)
    {
        $invoice = $event->Bill;

        $invoice->updatePaidStatus(false, false);
    }

    public function viewedInvoice(BillInvitationWasViewed $event)
    {
        $invitation = $event->purchaseInvitation;
        $invitation->markViewed();
    }


    public function emailedInvoice(BillWasEmailed $event)
    {
        $invoice = $event->Bill;
        $invoice->last_sent_date = date('Y-m-d');

        $invoice->save();
    }

    public function createdPayment(BillPaymentWasCreated $event)
    {
        $payment = $event->purchasePayment;
        $invoice = $payment->BILL;
        $adjustment = $payment->amount * -1;
        $partial = max(0, $invoice->partial - $payment->amount);

        $invoice->updateBalances($adjustment, $partial);
        $invoice->updatePaidStatus(true);

        // store a backup of the invoice
        $activity = Activity::where('payment_id', $payment->id)
            ->where('activity_type_id', ACTIVITY_TYPE_CREATE_BILL_PAYMENT)
            ->first();
        $activity->json_backup = $invoice->hidePrivateFields()->toJSON();
        $activity->save();

        if ($invoice->balance == 0 && $payment->account->auto_archive_invoice) {
            $invoiceRepo = app('App\Ninja\Repositories\BillRepository');
            $invoiceRepo->archive($invoice);
        }
    }

    public function deletedPayment(BillPaymentWasDeleted $event)
    {
        $payment = $event->purchasePayment;

        if ($payment->isFailedOrVoided()) {
            return;
        }

        $invoice = $payment->BILL;
        $adjustment = $payment->getCompletedAmount();

        $invoice->updateBalances($adjustment);
        $invoice->updatePaidStatus();
    }

    public function refundedPayment(BillPaymentWasRefunded $event)
    {
        $payment = $event->purchasePayment;
        $invoice = $payment->BILL;
        $adjustment = $event->refundAmount;

        $invoice->updateBalances($adjustment);
        $invoice->updatePaidStatus();
    }

    public function voidedPayment(BillPaymentWasVoided $event)
    {
        $payment = $event->purchasePayment;
        $invoice = $payment->BILL;
        $adjustment = $payment->amount;

        $invoice->updateBalances($adjustment);
        $invoice->updatePaidStatus();
    }

    public function failedPayment(BillPaymentFailed $event)
    {
        $payment = $event->purchasePayment;
        $invoice = $payment->BILL;
        $adjustment = $payment->getCompletedAmount();

        $invoice->updateBalances($adjustment);
        $invoice->updatePaidStatus();
    }

    public function restoredPayment(BillPaymentWasRestored $event)
    {
        if (!$event->fromDeleted) {
            return;
        }

        $payment = $event->purchasePayment;

        if ($payment->isFailedOrVoided()) {
            return;
        }

        $invoice = $payment->BILL;
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
