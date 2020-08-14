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
    public function __construct()
    {
    }

    public function createdBill(BillWasCreated $event)
    {
//        if (Utils::hasFeature(FEATURE_DIFFERENT_DESIGNS)) {
//            return false;
//        }

        // Make sure the account has the same design set as the invoice does
        if (Auth::check()) {
            $bill = $event->bill;
            $account = Auth::user()->account;

            if ($bill->invoice_design_id && $account->invoice_design_id != $bill->invoice_design_id) {
                $account->invoice_design_id = $bill->invoice_design_id;

                $account->save();
            }
        }
    }

    public function updatedBill(BillWasUpdated $event)
    {
        $bill = $event->bill;

        $bill->updatePaidStatus(false, false);
    }

    public function viewedBill(BillInvitationWasViewed $event)
    {
        $invitation = $event->billInvitation;
        $invitation->markViewed();
    }


    public function emailedBill(BillWasEmailed $event)
    {
        $bill = $event->bill;
        $bill->last_sent_date = date('Y-m-d');

        $bill->save();
    }

    public function createdPayment(BillPaymentWasCreated $event)
    {
        $paymentBill = $event->billPayment;
        $bill = $paymentBill->bill;
        $adjustment = $paymentBill->amount * -1;
        $partial = max(0, $bill->partial - $paymentBill->amount);

        $bill->updateBalances($adjustment, $partial);
        $bill->updatePaidStatus(true);

        // store a backup of the invoice
        $activity = Activity::where('payment_id', $paymentBill->id)
            ->where('activity_type_id', ACTIVITY_TYPE_CREATE_Bill_PAYMENT)
            ->first();
        $activity->json_backup = $bill->hidePrivateFields()->toJSON();
        $activity->save();

        if ($bill->balance == 0 && $paymentBill->account->auto_archive_invoice) {
            $billRepo = app('App\Ninja\Repositories\BillRepository');
            $billRepo->archive($bill);
        }
    }

    public function deletedPayment(BillPaymentWasDeleted $event)
    {
        $paymentBill = $event->billPayment;

        if ($paymentBill->isFailedOrVoided()) {
            return;
        }

        $bill = $paymentBill->bill;
        $adjustment = $paymentBill->getCompletedAmount();

        $bill->updateBalances($adjustment);
        $bill->updatePaidStatus();
    }

    public function refundedPayment(BillPaymentWasRefunded $event)
    {
        $paymentBill = $event->billPayment;
        $bill = $paymentBill->bill;
        $adjustment = $event->refundAmount;

        $bill->updateBalances($adjustment);
        $bill->updatePaidStatus();
    }

    public function voidedPayment(BillPaymentWasVoided $event)
    {
        $paymentBill = $event->billPayment;
        $bill = $paymentBill->bill;
        $adjustment = $paymentBill->amount;

        $bill->updateBalances($adjustment);
        $bill->updatePaidStatus();
    }

    public function failedPayment(BillPaymentFailed $event)
    {
        $paymentBill = $event->billPayment;
        $bill = $paymentBill->bill;
        $adjustment = $paymentBill->getCompletedAmount();

        $bill->updateBalances($adjustment);
        $bill->updatePaidStatus();
    }

    public function restoredPayment(BillPaymentWasRestored $event)
    {
        if (!$event->fromDeleted) {
            return;
        }

        $paymentBill = $event->billPayment;

        if ($paymentBill->isFailedOrVoided()) {
            return;
        }

        $bill = $paymentBill->bill;
        $adjustment = $paymentBill->getCompletedAmount() * -1;

        $bill->updateBalances($adjustment);
        $bill->updatePaidStatus();
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
