<?php

namespace App\Listeners;

use App;
use App\Events\ClientWasArchived;
use App\Events\ClientWasCreated;
use App\Events\ClientWasDeleted;
use App\Events\ClientWasRestored;
use App\Events\CreditWasArchived;
use App\Events\CreditWasCreated;
use App\Events\CreditWasDeleted;
use App\Events\CreditWasRestored;
use App\Events\ExpenseWasArchived;
use App\Events\ExpenseWasCreated;
use App\Events\ExpenseWasDeleted;
use App\Events\ExpenseWasRestored;
use App\Events\ExpenseWasUpdated;
use App\Events\InvoiceInvitationWasEmailed;
use App\Events\InvoiceInvitationWasViewed;
use App\Events\InvoiceWasArchived;
use App\Events\InvoiceWasCreated;
use App\Events\InvoiceWasDeleted;
use App\Events\InvoiceWasRestored;
use App\Events\InvoiceWasUpdated;
use App\Events\PaymentFailed;
use App\Events\PaymentWasArchived;
use App\Events\PaymentWasCreated;
use App\Events\PaymentWasDeleted;
use App\Events\PaymentWasRefunded;
use App\Events\PaymentWasRestored;
use App\Events\PaymentWasVoided;
use App\Events\BillCreditWasArchived;
use App\Events\BillCreditWasCreated;
use App\Events\BillCreditWasDeleted;
use App\Events\BillCreditWasRestored;
use App\Events\BillInvitationWasEmailed;
use App\Events\BillInvitationWasViewed;
use App\Events\BillWasArchived;
use App\Events\BillWasCreated;
use App\Events\BillWasDeleted;
use App\Events\BillWasRestored;
use App\Events\BillWasUpdated;
use App\Events\BillPaymentFailed;
use App\Events\BillPaymentWasArchived;
use App\Events\BillPaymentWasCreated;
use App\Events\BillPaymentWasDeleted;
use App\Events\BillPaymentWasRefunded;
use App\Events\BillPaymentWasRestored;
use App\Events\BillPaymentWasVoided;
use App\Events\BillQuoteInvitationWasEmailed;
use App\Events\BillQuoteInvitationWasViewed;
use App\Events\BillQuoteWasArchived;
use App\Events\BillQuoteWasCreated;
use App\Events\BillQuoteWasDeleted;
use App\Events\BillQuoteWasRestored;
use App\Events\BillQuoteWasUpdated;
use App\Events\QuoteInvitationWasApproved;
use App\Events\QuoteInvitationWasEmailed;
use App\Events\QuoteInvitationWasViewed;
use App\Events\billQuoteInvitationWasApproved;
use App\Events\QuoteWasArchived;
use App\Events\QuoteWasCreated;
use App\Events\QuoteWasDeleted;
use App\Events\QuoteWasRestored;
use App\Events\QuoteWasUpdated;
use App\Events\TaskWasArchived;
use App\Events\TaskWasCreated;
use App\Events\TaskWasDeleted;
use App\Events\TaskWasRestored;
use App\Events\TaskWasUpdated;
use App\Events\VendorWasArchived;
use App\Events\VendorWasCreated;
use App\Events\VendorWasDeleted;
use App\Events\VendorWasRestored;
use App\Models\Invoice;
use App\Models\Bill;
use App\Ninja\Repositories\ActivityRepository;

/**
 * Class ActivityListener.
 */
class ActivityListener
{

    private $activityRepo;

    public function __construct(ActivityRepository $activityRepo)
    {
        $this->activityRepo = $activityRepo;
    }

//    client activities
    public function createdClient(ClientWasCreated $event)
    {
        $this->activityRepo->create(
            $event->client,
            ACTIVITY_TYPE_CREATE_CLIENT
        );
    }

    public function deletedClient(ClientWasDeleted $event)
    {
        $this->activityRepo->create(
            $event->client,
            ACTIVITY_TYPE_DELETE_CLIENT
        );
    }

    public function archivedClient(ClientWasArchived $event)
    {
        if ($event->client->is_deleted) {
            return;
        }

        $this->activityRepo->create(
            $event->client,
            ACTIVITY_TYPE_ARCHIVE_CLIENT
        );
    }

    public function restoredClient(ClientWasRestored $event)
    {
        $this->activityRepo->create(
            $event->client,
            ACTIVITY_TYPE_RESTORE_CLIENT
        );
    }

//    vendor activities
    public function createdVendor(VendorWasCreated $event)
    {
        $this->activityRepo->create(
            $event->vendor,
            ACTIVITY_TYPE_CREATE_VENDOR
        );
    }

    public function deletedVendor(VendorWasDeleted $event)
    {
        $this->activityRepo->create(
            $event->vendor,
            ACTIVITY_TYPE_DELETE_VENDOR
        );
    }

    public function archivedVendor(VendorWasArchived $event)
    {
        if ($event->vendor->is_deleted) {
            return;
        }

        $this->activityRepo->create(
            $event->vendor,
            ACTIVITY_TYPE_ARCHIVE_VENDOR
        );
    }

    public function restoredVendor(VendorWasRestored $event)
    {
        $this->activityRepo->create(
            $event->vendor,
            ACTIVITY_TYPE_RESTORE_VENDOR
        );
    }

//  invoice activities
    public function createdInvoice(InvoiceWasCreated $event)
    {
        $this->activityRepo->create(
            $event->invoice, ACTIVITY_TYPE_CREATE_INVOICE, $event->invoice->getAdjustment()
        );
    }

    public function updatedInvoice(InvoiceWasUpdated $event)
    {
        if (!$event->invoice->isChanged()) {
            return;
        }

        $backupInvoice = Invoice::with('invoice_items', 'client.account', 'client.contacts')
            ->withTrashed()
            ->find($event->invoice->id);

        $activity = $this->activityRepo->create(
            $event->invoice,
            ACTIVITY_TYPE_UPDATE_INVOICE,
            $event->invoice->getAdjustment()
        );

        $activity->json_backup = $backupInvoice->hidePrivateFields()->toJSON();

        $activity->save();
    }

    public function deletedInvoice(InvoiceWasDeleted $event)
    {
        $invoice = $event->invoice;

        $this->activityRepo->create(
            $invoice,
            ACTIVITY_TYPE_DELETE_INVOICE,
            $invoice->affectsBalance() ? $invoice->balance * -1 : 0,
            $invoice->affectsBalance() ? $invoice->getAmountPaid() * -1 : 0
        );
    }

    public function archivedInvoice(InvoiceWasArchived $event)
    {
        if ($event->invoice->is_deleted) {
            return;
        }

        $this->activityRepo->create(
            $event->invoice,
            ACTIVITY_TYPE_ARCHIVE_INVOICE
        );
    }

    public function restoredInvoice(InvoiceWasRestored $event)
    {
        $invoice = $event->invoice;

        $this->activityRepo->create(
            $invoice,
            ACTIVITY_TYPE_RESTORE_INVOICE,
            $invoice->affectsBalance() && $event->fromDeleted ? $invoice->balance : 0,
            $invoice->affectsBalance() && $event->fromDeleted ? $invoice->getAmountPaid() : 0
        );
    }

    public function emailedInvoice(InvoiceInvitationWasEmailed $event)
    {
        $this->activityRepo->create(
            $event->invitation->invoice,
            ACTIVITY_TYPE_EMAIL_INVOICE,
            false,
            false,
            $event->invitation,
            $event->notes
        );
    }

    public function viewedInvoice(InvoiceInvitationWasViewed $event)
    {
        $this->activityRepo->create(
            $event->invoice,
            ACTIVITY_TYPE_VIEW_INVOICE,
            false,
            false,
            $event->invitation
        );
    }

//  invoice quote activities
    public function createdQuote(QuoteWasCreated $event)
    {
        $this->activityRepo->create(
            $event->quote,
            ACTIVITY_TYPE_CREATE_QUOTE
        );
    }

    public function updatedQuote(QuoteWasUpdated $event)
    {
        if (!$event->quote->isChanged()) {
            return;
        }

        $backupQuote = Invoice::with('invoice_items', 'client.account', 'client.contacts')
            ->withTrashed()
            ->find($event->quote->id);

        $activity = $this->activityRepo->create(
            $event->quote,
            ACTIVITY_TYPE_UPDATE_QUOTE
        );

        $activity->json_backup = $backupQuote->hidePrivateFields()->toJSON();
        $activity->save();
    }

    public function deletedQuote(QuoteWasDeleted $event)
    {
        $this->activityRepo->create(
            $event->quote,
            ACTIVITY_TYPE_DELETE_QUOTE
        );
    }

    public function archivedQuote(QuoteWasArchived $event)
    {
        if ($event->quote->is_deleted) {
            return;
        }

        $this->activityRepo->create(
            $event->quote,
            ACTIVITY_TYPE_ARCHIVE_QUOTE
        );
    }

    public function restoredQuote(QuoteWasRestored $event)
    {
        $this->activityRepo->create(
            $event->quote,
            ACTIVITY_TYPE_RESTORE_QUOTE
        );
    }

    public function emailedQuote(QuoteInvitationWasEmailed $event)
    {
        $this->activityRepo->create(
            $event->invitation->invoice,
            ACTIVITY_TYPE_EMAIL_QUOTE,
            false,
            false,
            $event->invitation,
            $event->notes
        );
    }

    public function viewedQuote(QuoteInvitationWasViewed $event)
    {
        $this->activityRepo->create(
            $event->quote,
            ACTIVITY_TYPE_VIEW_QUOTE,
            false,
            false,
            $event->invitation
        );
    }

    public function approvedQuote(QuoteInvitationWasApproved $event)
    {
        $this->activityRepo->create(
            $event->quote,
            ACTIVITY_TYPE_APPROVE_QUOTE,
            false,
            false,
            $event->invitation
        );
    }

//  invoice credit activities
    public function createdCredit(CreditWasCreated $event)
    {
        $this->activityRepo->create(
            $event->credit,
            ACTIVITY_TYPE_CREATE_CREDIT
        );
    }

    public function deletedCredit(CreditWasDeleted $event)
    {
        $this->activityRepo->create(
            $event->credit,
            ACTIVITY_TYPE_DELETE_CREDIT
        );
    }

    public function archivedCredit(CreditWasArchived $event)
    {
        if ($event->credit->is_deleted) {
            return;
        }

        $this->activityRepo->create(
            $event->credit,
            ACTIVITY_TYPE_ARCHIVE_CREDIT
        );
    }

    public function restoredCredit(CreditWasRestored $event)
    {
        $this->activityRepo->create(
            $event->credit,
            ACTIVITY_TYPE_RESTORE_CREDIT
        );
    }

//   invoice payment activities
    public function createdPayment(PaymentWasCreated $event)
    {
        $this->activityRepo->create(
            $event->payment,
            ACTIVITY_TYPE_CREATE_PAYMENT,
            $event->payment->amount * -1,
            $event->payment->amount,
            false,
            App::runningInConsole() ? 'auto_billed' : ''
        );
    }

    public function deletedPayment(PaymentWasDeleted $event)
    {
        $payment = $event->payment;

        $this->activityRepo->create(
            $payment,
            ACTIVITY_TYPE_DELETE_PAYMENT,
            $payment->isFailedOrVoided() ? 0 : $payment->getCompletedAmount(),
            $payment->isFailedOrVoided() ? 0 : $payment->getCompletedAmount() * -1
        );
    }

    public function refundedPayment(PaymentWasRefunded $event)
    {
        $payment = $event->payment;

        $this->activityRepo->create(
            $payment,
            ACTIVITY_TYPE_REFUNDED_PAYMENT,
            $event->refundAmount,
            $event->refundAmount * -1
        );
    }

    public function voidedPayment(PaymentWasVoided $event)
    {
        $payment = $event->payment;

        $this->activityRepo->create(
            $payment,
            ACTIVITY_TYPE_VOIDED_PAYMENT,
            $payment->is_deleted ? 0 : $payment->getCompletedAmount(),
            $payment->is_deleted ? 0 : $payment->getCompletedAmount() * -1
        );
    }

    public function failedPayment(PaymentFailed $event)
    {
        $payment = $event->payment;

        $this->activityRepo->create(
            $payment,
            ACTIVITY_TYPE_FAILED_PAYMENT,
            $payment->is_deleted ? 0 : $payment->getCompletedAmount(),
            $payment->is_deleted ? 0 : $payment->getCompletedAmount() * -1
        );
    }

    public function archivedPayment(PaymentWasArchived $event)
    {
        if ($event->payment->is_deleted) {
            return;
        }

        $this->activityRepo->create(
            $event->payment,
            ACTIVITY_TYPE_ARCHIVE_PAYMENT
        );
    }

    public function restoredPayment(PaymentWasRestored $event)
    {
        $payment = $event->payment;

        $this->activityRepo->create(
            $payment,
            ACTIVITY_TYPE_RESTORE_PAYMENT,
            $event->fromDeleted && !$payment->isFailedOrVoided() ? $payment->getCompletedAmount() * -1 : 0,
            $event->fromDeleted && !$payment->isFailedOrVoided() ? $payment->getCompletedAmount() : 0
        );
    }

//  purchase invoice activities
    public function createdBill(BillWasCreated $event)
    {
        $this->activityRepo->createBill(
            $event->Bill, ACTIVITY_TYPE_CREATE_BILL,
            $event->Bill->getAdjustment()
        );
    }

    public function updatedBill(BillWasUpdated $event)
    {
        if (!$event->Bill->isChanged()) {
            return;
        }

        $backupInvoice = Bill::with('invoice_items', 'vendor.account', 'vendor.contacts')
            ->withTrashed()
            ->find($event->Bill->id);

        $activity = $this->activityRepo->createBill(
            $event->Bill,
            ACTIVITY_TYPE_UPDATE_BILL,
            $event->Bill->getAdjustment()
        );

        $activity->json_backup = $backupInvoice->hidePrivateFields()->toJSON();

        $activity->save();
    }

    public function deletedBill(BillWasDeleted $event)
    {
        $Bill = $event->Bill;

        $this->activityRepo->createBill(
            $Bill,
            ACTIVITY_TYPE_DELETE_BILL,
            $Bill->affectsBalance() ? $Bill->balance * -1 : 0,
            $Bill->affectsBalance() ? $Bill->getAmountPaid() * -1 : 0
        );
    }

    public function archivedBill(BillWasArchived $event)
    {
        if ($event->Bill->is_deleted) {
            return;
        }

        $this->activityRepo->createBill(
            $event->Bill,
            ACTIVITY_TYPE_ARCHIVE_BILL
        );
    }

    public function restoredBill(BillWasRestored $event)
    {
        $Bill = $event->Bill;

        $this->activityRepo->createBill(
            $Bill,
            ACTIVITY_TYPE_RESTORE_INVOICE,
            $Bill->affectsBalance() && $event->fromDeleted ? $Bill->balance : 0,
            $Bill->affectsBalance() && $event->fromDeleted ? $Bill->getAmountPaid() : 0
        );
    }

    public function emailedBill(BillInvitationWasEmailed $event)
    {
        $this->activityRepo->createBill(
            $event->purchaseInvitation->BILL,
            ACTIVITY_TYPE_EMAIL_BILL,
            false,
            false,
            $event->purchaseInvitation,
            $event->notes
        );
    }

    public function viewedBill(BillInvitationWasViewed $event)
    {
        $this->activityRepo->createBill(
            $event->Bill,
            ACTIVITY_TYPE_VIEW_BILL,
            false,
            false,
            $event->purchaseInvitation
        );
    }

//  invoice quote activities
    public function createdPurchaseQuote(BillQuoteWasCreated $event)
    {
        $this->activityRepo->createBill(
            $event->quote,
            ACTIVITY_TYPE_CREATE_bill_quote
        );
    }

    public function updatedPurchaseQuote(BillQuoteWasUpdated $event)
    {
        if (!$event->quote->isChanged()) {
            return;
        }

        $backupQuote = Bill::with('invoice_items', 'vendor.account', 'vendor.contacts')
            ->withTrashed()
            ->find($event->quote->id);

        $activity = $this->activityRepo->createBill(
            $event->quote,
            ACTIVITY_TYPE_UPDATE_bill_quote
        );

        $activity->json_backup = $backupQuote->hidePrivateFields()->toJSON();
        $activity->save();
    }

    public function deletedPurchaseQuote(BillQuoteWasDeleted $event)
    {
        $this->activityRepo->createBill(
            $event->quote,
            ACTIVITY_TYPE_DELETE_bill_quote
        );
    }

    public function archivedPurchaseQuote(BillQuoteWasArchived $event)
    {
        if ($event->quote->is_deleted) {
            return;
        }

        $this->activityRepo->createBill(
            $event->quote,
            ACTIVITY_TYPE_ARCHIVE_bill_quote
        );
    }

    public function restoredPurchaseQuote(BillQuoteWasRestored $event)
    {
        $this->activityRepo->createBill(
            $event->quote,
            ACTIVITY_TYPE_RESTORE_bill_quote
        );
    }

    public function emailedPurchaseQuote(BillQuoteInvitationWasEmailed $event)
    {
        $this->activityRepo->createBill(
            $event->purchaseInvitation->BILL,
            ACTIVITY_TYPE_EMAIL_BILL_QUOTE,
            false,
            false,
            $event->purchaseInvitation,
            $event->notes
        );
    }

    public function viewedPurchaseQuote(BillQuoteInvitationWasViewed $event)
    {
        $this->activityRepo->createBill(
            $event->quote,
            ACTIVITY_TYPE_VIEW_bill_quote,
            false,
            false,
            $event->invitation
        );
    }

    public function approvedPurchaseQuote(billQuoteInvitationWasApproved $event)
    {
        $this->activityRepo->createBill(
            $event->quote,
            ACTIVITY_TYPE_APPROVE_bill_quote,
            false,
            false,
            $event->invitation
        );
    }

//  invoice credit activities
    public function createdPurchaseCredit(BillCreditWasCreated $event)
    {
        $this->activityRepo->createBill(
            $event->purchaseCredit,
            ACTIVITY_TYPE_CREATE_BILL_CREDIT
        );
    }

    public function deletedPurchaseCredit(BillCreditWasDeleted $event)
    {
        $this->activityRepo->createBill(
            $event->purchaseCredit,
            ACTIVITY_TYPE_DELETE_BILL_CREDIT
        );
    }

    public function archivedPurchaseCredit(BillCreditWasArchived $event)
    {
        if ($event->purchaseCredit->is_deleted) {
            return;
        }

        $this->activityRepo->createBill(
            $event->purchaseCredit,
            ACTIVITY_TYPE_ARCHIVE_BILL_CREDIT
        );
    }

    public function restoredPurchaseCredit(BillCreditWasRestored $event)
    {
        $this->activityRepo->createBill(
            $event->purchaseCredit,
            ACTIVITY_TYPE_RESTORE_BILL_CREDIT
        );
    }

//   invoice payment activities
    public function createdPurchasePayment(BillPaymentWasCreated $event)
    {
        $this->activityRepo->createBill(
            $event->purchasePayment,
            ACTIVITY_TYPE_CREATE_BILL_PAYMENT,
            $event->purchasePayment->amount * -1,
            $event->purchasePayment->amount,
            false,
            App::runningInConsole() ? 'auto_billed' : ''
        );
    }

    public function deletedPurchasePayment(BillPaymentWasDeleted $event)
    {
        $payment = $event->purchasePayment;

        $this->activityRepo->createBill(
            $payment,
            ACTIVITY_TYPE_DELETE_BILL_PAYMENT,
            $payment->isFailedOrVoided() ? 0 : $payment->getCompletedAmount(),
            $payment->isFailedOrVoided() ? 0 : $payment->getCompletedAmount() * -1
        );
    }

    public function refundedPurchasePayment(BillPaymentWasRefunded $event)
    {
        $payment = $event->purchasePayment;

        $this->activityRepo->createBill(
            $payment,
            ACTIVITY_TYPE_REFUNDED_BILL_PAYMENT,
            $event->refundAmount,
            $event->refundAmount * -1
        );
    }

    public function voidedPurchasePayment(BillPaymentWasVoided $event)
    {
        $payment = $event->purchasePayment;

        $this->activityRepo->createBill(
            $payment,
            ACTIVITY_TYPE_VOIDED_BILL_PAYMENT,
            $payment->is_deleted ? 0 : $payment->getCompletedAmount(),
            $payment->is_deleted ? 0 : $payment->getCompletedAmount() * -1
        );
    }

    public function failedPurchasePayment(BillPaymentFailed $event)
    {
        $payment = $event->purchasePayment;

        $this->activityRepo->createBill(
            $payment,
            ACTIVITY_TYPE_FAILED_BILL_PAYMENT,
            $payment->is_deleted ? 0 : $payment->getCompletedAmount(),
            $payment->is_deleted ? 0 : $payment->getCompletedAmount() * -1
        );
    }

    public function archivedPurchasePayment(BillPaymentWasArchived $event)
    {
        if ($event->purchasePayment->is_deleted) {
            return;
        }

        $this->activityRepo->createBill(
            $event->purchasePayment,
            ACTIVITY_TYPE_ARCHIVE_BILL_PAYMENT
        );
    }

    public function restoredPurchasePayment(BillPaymentWasRestored $event)
    {
        $payment = $event->purchasePayment;

        $this->activityRepo->createBill(
            $payment,
            ACTIVITY_TYPE_RESTORE_BILL,
            $event->fromDeleted && !$payment->isFailedOrVoided() ? $payment->getCompletedAmount() * -1 : 0,
            $event->fromDeleted && !$payment->isFailedOrVoided() ? $payment->getCompletedAmount() : 0
        );
    }

//  task activities
    public function createdTask(TaskWasCreated $event)
    {
        $this->activityRepo->create(
            $event->task,
            ACTIVITY_TYPE_CREATE_TASK
        );
    }

    public function updatedTask(TaskWasUpdated $event)
    {
        if (!$event->task->isChanged()) {
            return;
        }

        $this->activityRepo->create(
            $event->task,
            ACTIVITY_TYPE_UPDATE_TASK
        );
    }

    public function archivedTask(TaskWasArchived $event)
    {
        if ($event->task->is_deleted) {
            return;
        }

        $this->activityRepo->create(
            $event->task,
            ACTIVITY_TYPE_ARCHIVE_TASK
        );
    }

    public function deletedTask(TaskWasDeleted $event)
    {
        $this->activityRepo->create(
            $event->task,
            ACTIVITY_TYPE_DELETE_TASK
        );
    }

    public function restoredTask(TaskWasRestored $event)
    {
        $this->activityRepo->create(
            $event->task,
            ACTIVITY_TYPE_RESTORE_TASK
        );
    }

//  expense activities
    public function createdExpense(ExpenseWasCreated $event)
    {
        $this->activityRepo->create(
            $event->expense,
            ACTIVITY_TYPE_CREATE_EXPENSE
        );
    }

    public function updatedExpense(ExpenseWasUpdated $event)
    {
        if (!$event->expense->isChanged()) {
            return;
        }

        $this->activityRepo->create(
            $event->expense,
            ACTIVITY_TYPE_UPDATE_EXPENSE
        );
    }

    public function archivedExpense(ExpenseWasArchived $event)
    {
        if ($event->expense->is_deleted) {
            return;
        }

        $this->activityRepo->create(
            $event->expense,
            ACTIVITY_TYPE_ARCHIVE_EXPENSE
        );
    }

    public function deletedExpense(ExpenseWasDeleted $event)
    {
        $this->activityRepo->create(
            $event->expense,
            ACTIVITY_TYPE_DELETE_EXPENSE
        );
    }

    public function restoredExpense(ExpenseWasRestored $event)
    {
        $this->activityRepo->create(
            $event->expense,
            ACTIVITY_TYPE_RESTORE_EXPENSE
        );
    }
}
