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
use App\Events\PurchaseCreditWasArchived;
use App\Events\PurchaseCreditWasCreated;
use App\Events\PurchaseCreditWasDeleted;
use App\Events\PurchaseCreditWasRestored;
use App\Events\PurchaseInvoiceInvitationWasEmailed;
use App\Events\PurchaseInvoiceInvitationWasViewed;
use App\Events\PurchaseInvoiceWasArchived;
use App\Events\PurchaseInvoiceWasCreated;
use App\Events\PurchaseInvoiceWasDeleted;
use App\Events\PurchaseInvoiceWasRestored;
use App\Events\PurchaseInvoiceWasUpdated;
use App\Events\PurchasePaymentFailed;
use App\Events\PurchasePaymentWasArchived;
use App\Events\PurchasePaymentWasCreated;
use App\Events\PurchasePaymentWasDeleted;
use App\Events\PurchasePaymentWasRefunded;
use App\Events\PurchasePaymentWasRestored;
use App\Events\PurchasePaymentWasVoided;
use App\Events\PurchaseQuoteInvitationWasEmailed;
use App\Events\PurchaseQuoteInvitationWasViewed;
use App\Events\PurchaseQuoteWasArchived;
use App\Events\PurchaseQuoteWasCreated;
use App\Events\PurchaseQuoteWasDeleted;
use App\Events\PurchaseQuoteWasRestored;
use App\Events\PurchaseQuoteWasUpdated;
use App\Events\QuoteInvitationWasApproved;
use App\Events\QuoteInvitationWasEmailed;
use App\Events\QuoteInvitationWasViewed;
use App\Events\purchaseQuoteInvitationWasApproved;
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
use App\Models\PurchaseInvoice;
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
    public function createdPurchaseInvoice(PurchaseInvoiceWasCreated $event)
    {
        $this->activityRepo->create(
            $event->purchaseInvoice, ACTIVITY_TYPE_CREATE_PURCHASE_INVOICE,
            $event->purchaseInvoice->getAdjustment()
        );
    }

    public function updatedPurchaseInvoice(PurchaseInvoiceWasUpdated $event)
    {
        if (!$event->purchaseInvoice->isChanged()) {
            return;
        }

        $backupInvoice = PurchaseInvoice::with('invoice_items', 'vendor.account', 'vendor.contacts')
            ->withTrashed()
            ->find($event->purchaseInvoice->id);

        $activity = $this->activityRepo->create(
            $event->purchaseInvoice,
            ACTIVITY_TYPE_UPDATE_PURCHASE_INVOICE,
            $event->purchaseInvoice->getAdjustment()
        );

        $activity->json_backup = $backupInvoice->hidePrivateFields()->toJSON();

        $activity->save();
    }

    public function deletedPurchaseInvoice(PurchaseInvoiceWasDeleted $event)
    {
        $purchaseInvoice = $event->purchaseInvoice;

        $this->activityRepo->create(
            $purchaseInvoice,
            ACTIVITY_TYPE_DELETE_PURCHASE_INVOICE,
            $purchaseInvoice->affectsBalance() ? $purchaseInvoice->balance * -1 : 0,
            $purchaseInvoice->affectsBalance() ? $purchaseInvoice->getAmountPaid() * -1 : 0
        );
    }

    public function archivedPurchaseInvoice(PurchaseInvoiceWasArchived $event)
    {
        if ($event->purchaseInvoice->is_deleted) {
            return;
        }

        $this->activityRepo->create(
            $event->purchaseInvoice,
            ACTIVITY_TYPE_ARCHIVE_PURCHASE_INVOICE
        );
    }

    public function restoredPurchaseInvoice(PurchaseInvoiceWasRestored $event)
    {
        $purchaseInvoice = $event->purchaseInvoice;

        $this->activityRepo->create(
            $purchaseInvoice,
            ACTIVITY_TYPE_RESTORE_INVOICE,
            $purchaseInvoice->affectsBalance() && $event->fromDeleted ? $purchaseInvoice->balance : 0,
            $purchaseInvoice->affectsBalance() && $event->fromDeleted ? $purchaseInvoice->getAmountPaid() : 0
        );
    }

    public function emailedPurchaseInvoice(PurchaseInvoiceInvitationWasEmailed $event)
    {
        $this->activityRepo->create(
            $event->purchaseInvitation->purchase_invoice,
            ACTIVITY_TYPE_EMAIL_PURCHASE_INVOICE,
            false,
            false,
            $event->purchaseInvitation,
            $event->notes
        );
    }

    public function viewedPurchaseInvoice(PurchaseInvoiceInvitationWasViewed $event)
    {
        $this->activityRepo->create(
            $event->purchaseInvoice,
            ACTIVITY_TYPE_VIEW_PURCHASE_INVOICE,
            false,
            false,
            $event->purchaseInvitation
        );
    }

//  invoice quote activities
    public function createdPurchaseQuote(PurchaseQuoteWasCreated $event)
    {
        $this->activityRepo->create(
            $event->quote,
            ACTIVITY_TYPE_CREATE_PURCHASE_QUOTE
        );
    }

    public function updatedPurchaseQuote(PurchaseQuoteWasUpdated $event)
    {
        if (!$event->quote->isChanged()) {
            return;
        }

        $backupQuote = PurchaseInvoice::with('invoice_items', 'vendor.account', 'vendor.contacts')
            ->withTrashed()
            ->find($event->quote->id);

        $activity = $this->activityRepo->create(
            $event->quote,
            ACTIVITY_TYPE_UPDATE_PURCHASE_QUOTE
        );

        $activity->json_backup = $backupQuote->hidePrivateFields()->toJSON();
        $activity->save();
    }

    public function deletedPurchaseQuote(PurchaseQuoteWasDeleted $event)
    {
        $this->activityRepo->create(
            $event->quote,
            ACTIVITY_TYPE_DELETE_PURCHASE_QUOTE
        );
    }

    public function archivedPurchaseQuote(PurchaseQuoteWasArchived $event)
    {
        if ($event->quote->is_deleted) {
            return;
        }

        $this->activityRepo->create(
            $event->quote,
            ACTIVITY_TYPE_ARCHIVE_PURCHASE_QUOTE
        );
    }

    public function restoredPurchaseQuote(PurchaseQuoteWasRestored $event)
    {
        $this->activityRepo->create(
            $event->quote,
            ACTIVITY_TYPE_RESTORE_PURCHASE_QUOTE
        );
    }

    public function emailedPurchaseQuote(PurchaseQuoteInvitationWasEmailed $event)
    {
        $this->activityRepo->create(
            $event->purchaseInvitation->purchase_invoice,
            ACTIVITY_TYPE_EMAIL_PURCHASE_QUOTE,
            false,
            false,
            $event->purchaseInvitation,
            $event->notes
        );
    }

    public function viewedPurchaseQuote(PurchaseQuoteInvitationWasViewed $event)
    {
        $this->activityRepo->create(
            $event->quote,
            ACTIVITY_TYPE_VIEW_PURCHASE_QUOTE,
            false,
            false,
            $event->invitation
        );
    }

    public function approvedPurchaseQuote(purchaseQuoteInvitationWasApproved $event)
    {
        $this->activityRepo->create(
            $event->quote,
            ACTIVITY_TYPE_APPROVE_PURCHASE_QUOTE,
            false,
            false,
            $event->invitation
        );
    }

//  invoice credit activities
    public function createdPurchaseCredit(PurchaseCreditWasCreated $event)
    {
        $this->activityRepo->create(
            $event->purchaseCredit,
            ACTIVITY_TYPE_CREATE_PURCHASE_CREDIT
        );
    }

    public function deletedPurchaseCredit(PurchaseCreditWasDeleted $event)
    {
        $this->activityRepo->create(
            $event->purchaseCredit,
            ACTIVITY_TYPE_DELETE_PURCHASE_CREDIT
        );
    }

    public function archivedPurchaseCredit(PurchaseCreditWasArchived $event)
    {
        if ($event->purchaseCredit->is_deleted) {
            return;
        }

        $this->activityRepo->create(
            $event->purchaseCredit,
            ACTIVITY_TYPE_ARCHIVE_PURCHASE_CREDIT
        );
    }

    public function restoredPurchaseCredit(PurchaseCreditWasRestored $event)
    {
        $this->activityRepo->create(
            $event->purchaseCredit,
            ACTIVITY_TYPE_RESTORE_PURCHASE_CREDIT
        );
    }

//   invoice payment activities
    public function createdPurchasePayment(PurchasePaymentWasCreated $event)
    {
        $this->activityRepo->create(
            $event->purchasePayment,
            ACTIVITY_TYPE_CREATE_PURCHASE_PAYMENT,
            $event->purchasePayment->amount * -1,
            $event->purchasePayment->amount,
            false,
            App::runningInConsole() ? 'auto_billed' : ''
        );
    }

    public function deletedPurchasePayment(PurchasePaymentWasDeleted $event)
    {
        $payment = $event->purchasePayment;

        $this->activityRepo->create(
            $payment,
            ACTIVITY_TYPE_DELETE_PURCHASE_PAYMENT,
            $payment->isFailedOrVoided() ? 0 : $payment->getCompletedAmount(),
            $payment->isFailedOrVoided() ? 0 : $payment->getCompletedAmount() * -1
        );
    }

    public function refundedPurchasePayment(PurchasePaymentWasRefunded $event)
    {
        $payment = $event->purchasePayment;

        $this->activityRepo->create(
            $payment,
            ACTIVITY_TYPE_REFUNDED_PURCHASE_PAYMENT,
            $event->refundAmount,
            $event->refundAmount * -1
        );
    }

    public function voidedPurchasePayment(PurchasePaymentWasVoided $event)
    {
        $payment = $event->purchasePayment;

        $this->activityRepo->create(
            $payment,
            ACTIVITY_TYPE_VOIDED_PURCHASE_PAYMENT,
            $payment->is_deleted ? 0 : $payment->getCompletedAmount(),
            $payment->is_deleted ? 0 : $payment->getCompletedAmount() * -1
        );
    }

    public function failedPurchasePayment(PurchasePaymentFailed $event)
    {
        $payment = $event->purchasePayment;

        $this->activityRepo->create(
            $payment,
            ACTIVITY_TYPE_FAILED_PURCHASE_PAYMENT,
            $payment->is_deleted ? 0 : $payment->getCompletedAmount(),
            $payment->is_deleted ? 0 : $payment->getCompletedAmount() * -1
        );
    }

    public function archivedPurchasePayment(PurchasePaymentWasArchived $event)
    {
        if ($event->purchasePayment->is_deleted) {
            return;
        }

        $this->activityRepo->create(
            $event->purchasePayment,
            ACTIVITY_TYPE_ARCHIVE_PURCHASE_PAYMENT
        );
    }

    public function restoredPurchasePayment(PurchasePaymentWasRestored $event)
    {
        $payment = $event->purchasePayment;

        $this->activityRepo->create(
            $payment,
            ACTIVITY_TYPE_RESTORE_PURCHASE_INVOICE,
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
