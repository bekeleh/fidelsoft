<?php

namespace App\Listeners;

use App\Events\PurchaseInvoiceInvitationWasViewed;
use App\Events\PurchaseInvoiceWasCreated;
use App\Events\PurchaseInvoiceWasEmailed;
use App\Events\PurchaseInvoiceWasUpdated;
use App\Events\PurchasePaymentFailed;
use App\Events\PurchasePaymentWasCreated;
use App\Events\PurchasePaymentWasDeleted;
use App\Events\PurchasePaymentWasRefunded;
use App\Events\PurchasePaymentWasRestored;
use App\Events\PurchasePaymentWasVoided;
use App\Libraries\Utils;
use App\Models\Activity;
use Illuminate\Queue\Events\JobExceptionOccurred;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

/**
 * Class PurchaseInvoiceListener.
 */
class PurchaseInvoiceListener
{
    public function createdInvoice(PurchaseInvoiceWasCreated $event)
    {
//        if (Utils::hasFeature(FEATURE_DIFFERENT_DESIGNS)) {
//            return false;
//        }

        // Make sure the account has the same design set as the invoice does
        if (Auth::check()) {
            $invoice = $event->purchaseInvoice;
            $account = Auth::user()->account;

            if ($invoice->invoice_design_id && $account->invoice_design_id != $invoice->invoice_design_id) {
                $account->invoice_design_id = $invoice->invoice_design_id;

                $account->save();
            }
        }
    }

    public function updatedInvoice(PurchaseInvoiceWasUpdated $event)
    {
        $invoice = $event->purchaseInvoice;

        $invoice->updatePaidStatus(false, false);
    }

    public function viewedInvoice(PurchaseInvoiceInvitationWasViewed $event)
    {
        $invitation = $event->purchaseInvitation;
        $invitation->markViewed();
    }


    public function emailedInvoice(PurchaseInvoiceWasEmailed $event)
    {
        $invoice = $event->purchaseInvoice;
        $invoice->last_sent_date = date('Y-m-d');

        $invoice->save();
    }

    public function createdPayment(PurchasePaymentWasCreated $event)
    {
        $payment = $event->purchasePayment;
        $invoice = $payment->purchase_invoice;
        $adjustment = $payment->amount * -1;
        $partial = max(0, $invoice->partial - $payment->amount);

        $invoice->updateBalances($adjustment, $partial);
        $invoice->updatePaidStatus(true);

        // store a backup of the invoice
        $activity = Activity::where('payment_id', $payment->id)
            ->where('activity_type_id', ACTIVITY_TYPE_CREATE_PURCHASE_PAYMENT)
            ->first();
        $activity->json_backup = $invoice->hidePrivateFields()->toJSON();
        $activity->save();

        if ($invoice->balance == 0 && $payment->account->auto_archive_invoice) {
            $invoiceRepo = app('App\Ninja\Repositories\PurchaseInvoiceRepository');
            $invoiceRepo->archive($invoice);
        }
    }

    public function deletedPayment(PurchasePaymentWasDeleted $event)
    {
        $payment = $event->purchasePayment;

        if ($payment->isFailedOrVoided()) {
            return;
        }

        $invoice = $payment->purchase_invoice;
        $adjustment = $payment->getCompletedAmount();

        $invoice->updateBalances($adjustment);
        $invoice->updatePaidStatus();
    }

    public function refundedPayment(PurchasePaymentWasRefunded $event)
    {
        $payment = $event->purchasePayment;
        $invoice = $payment->purchase_invoice;
        $adjustment = $event->refundAmount;

        $invoice->updateBalances($adjustment);
        $invoice->updatePaidStatus();
    }

    public function voidedPayment(PurchasePaymentWasVoided $event)
    {
        $payment = $event->purchasePayment;
        $invoice = $payment->purchase_invoice;
        $adjustment = $payment->amount;

        $invoice->updateBalances($adjustment);
        $invoice->updatePaidStatus();
    }

    public function failedPayment(PurchasePaymentFailed $event)
    {
        $payment = $event->purchasePayment;
        $invoice = $payment->purchase_invoice;
        $adjustment = $payment->getCompletedAmount();

        $invoice->updateBalances($adjustment);
        $invoice->updatePaidStatus();
    }

    public function restoredPayment(PurchasePaymentWasRestored $event)
    {
        if (!$event->fromDeleted) {
            return;
        }

        $payment = $event->purchasePayment;

        if ($payment->isFailedOrVoided()) {
            return;
        }

        $invoice = $payment->purchase_invoice;
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
