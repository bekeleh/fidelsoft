<?php

namespace App\Listeners;

use App\Events\BillInvitationWasViewedEvent;
use App\Events\BillWasCreatedEvent;
use App\Events\BillWasEmailedEvent;
use App\Events\BillWasUpdatedEvent;
use App\Events\BillPaymentFailedEvent;
use App\Events\BillPaymentWasCreatedEvent;
use App\Events\BillPaymentWasDeletedEvent;
use App\Events\BillPaymentWasRefundedEvent;
use App\Events\BillPaymentWasRestoredEvent;
use App\Events\BillPaymentWasVoidedEvent;
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

    public function createdBill(BillWasCreatedEvent $event)
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

    public function updatedBill(BillWasUpdatedEvent $event)
    {
        $bill = $event->bill;

        $bill->updatePaidStatus(false, false);
    }

    public function viewedBill(BillInvitationWasViewedEvent $event)
    {
        $invitation = $event->billInvitation;
        $invitation->markViewed();
    }


    public function emailedBill(BillWasEmailedEvent $event)
    {
        $bill = $event->bill;
        $bill->last_sent_date = date('Y-m-d');

        $bill->save();
    }

    public function createdPayment(BillPaymentWasCreatedEvent $event)
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

    public function deletedPayment(BillPaymentWasDeletedEvent $event)
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

    public function refundedPayment(BillPaymentWasRefundedEvent $event)
    {
        $paymentBill = $event->billPayment;
        $bill = $paymentBill->bill;
        $adjustment = $event->refundAmount;

        $bill->updateBalances($adjustment);
        $bill->updatePaidStatus();
    }

    public function voidedPayment(BillPaymentWasVoidedEvent $event)
    {
        $paymentBill = $event->billPayment;
        $bill = $paymentBill->bill;
        $adjustment = $paymentBill->amount;

        $bill->updateBalances($adjustment);
        $bill->updatePaidStatus();
    }

    public function failedPayment(BillPaymentFailedEvent $event)
    {
        $paymentBill = $event->billPayment;
        $bill = $paymentBill->bill;
        $adjustment = $paymentBill->getCompletedAmount();

        $bill->updateBalances($adjustment);
        $bill->updatePaidStatus();
    }

    public function restoredPayment(BillPaymentWasRestoredEvent $event)
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
