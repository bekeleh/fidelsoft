<?php

namespace App\Listeners\Purchase;

use App\Events\Purchase\BillInvitationWasViewedEvent;
use App\Events\Purchase\BillPaymentFailedEvent;
use App\Events\Purchase\BillPaymentWasCreatedEvent;
use App\Events\Purchase\BillPaymentWasDeletedEvent;
use App\Events\Purchase\BillPaymentWasRefundedEvent;
use App\Events\Purchase\BillPaymentWasRestoredEvent;
use App\Events\Purchase\BillPaymentWasVoidedEvent;
use App\Events\Purchase\BillWasCreatedEvent;
use App\Events\Purchase\BillWasDeletedEvent;
use App\Events\Purchase\BillWasEmailedEvent;
use App\Events\Purchase\BillWasUpdatedEvent;
use App\Libraries\Utils;
use App\Models\Activity;
use App\Models\Common\Subscription;
use App\Models\User;
use App\Notifications\Purchase\NotifyBillCreated;
use App\Notifications\Purchase\NotifyBillDeleted;
use App\Notifications\Purchase\NotifyBillUpdated;
use Illuminate\Queue\Events\JobExceptionOccurred;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;

/**
 * Class BillListener.
 */
class BillListener
{
    public function createdBill(BillWasCreatedEvent $event)
    {
        $bill = $event->bill;
        // Make sure the account has the same design set as the bill does
        if (Auth::check()) {
            $account = Auth::user()->account;

            if ($bill->invoice_design_id && $account->invoice_design_id != $bill->invoice_design_id) {
                $account->invoice_design_id = $bill->invoice_design_id;

                $account->save();
            }
        }

//   send notification for subscribers
        $this->notifyBillCreated($bill);
    }

    public function updatedBill(BillWasUpdatedEvent $event)
    {
        $bill = $event->bill;

        $bill->updatePaidStatus(false, false);

        //     send notification for subscribers
        $this->notifyBillCreated($bill);
    }

    public function deletedBill(BillWasDeletedEvent $event)
    {
        $bill = $event->bill;

//      send notification for subscribers
        $this->notifyBillDeleted($bill);

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

        // store a backup of the bill
        $activity = Activity::where('payment_id', $paymentBill->id)
            ->where('activity_type_id', ACTIVITY_TYPE_CREATE_BILL_PAYMENT)
            ->first();
        $activity->json_backup = $bill->hidePrivateFields()->toJSON();
        $activity->save();

        if ($bill->balance == 0 && $paymentBill->account->auto_archive_bill) {
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

    private function notifyBillCreated($bill): void
    {
//      get event subscriber
        $subscribers = Subscription::subscriber(EVENT_UPDATE_BILL);
        $users = User::getNotifyUserId($subscribers);
        if ($users) {
            Notification::send($users, new NotifyBillCreated($bill));
        }
    }

    private function notifyBillUpdated($bill): void
    {
//      get event subscriber
        $subscribers = Subscription::subscriber(EVENT_UPDATE_BILL);
        $users = User::getNotifyUserId($subscribers);
        if ($users) {
            Notification::send($users, new NotifyBillUpdated($bill));
        }
    }

    private function notifyBillDeleted($bill): void
    {
//      get event subscriber
        $subscribers = Subscription::subscriber(EVENT_UPDATE_BILL);
        $users = User::getNotifyUserId($subscribers);
        if ($users) {
            Notification::send($users, new NotifyBillDeleted($bill));
        }
    }
}
