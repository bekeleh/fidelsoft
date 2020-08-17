<?php

namespace App\Listeners\Report;

use App;
use App\Events\ClientWasArchivedEvent;
use App\Events\ClientWasCreatedEvent;
use App\Events\ClientWasDeletedEvent;
use App\Events\ClientWasRestoredEvent;
use App\Events\CreditWasArchivedEvent;
use App\Events\CreditWasCreatedEvent;
use App\Events\CreditWasDeletedEvent;
use App\Events\CreditWasRestoredEvent;
use App\Events\ExpenseWasArchivedEvent;
use App\Events\ExpenseWasCreatedEvent;
use App\Events\ExpenseWasDeletedEvent;
use App\Events\ExpenseWasRestoredEvent;
use App\Events\ExpenseWasUpdatedEvent;
use App\Events\InvoiceInvitationWasEmailedEvent;
use App\Events\InvoiceInvitationWasViewedEvent;
use App\Events\InvoiceWasArchivedEvent;
use App\Events\InvoiceWasCreatedEvent;
use App\Events\InvoiceWasDeletedEvent;
use App\Events\InvoiceWasRestoredEvent;
use App\Events\InvoiceWasUpdatedEvent;
use App\Events\PaymentFailedEvent;
use App\Events\PaymentWasArchivedEvent;
use App\Events\PaymentWasCreatedEvent;
use App\Events\PaymentWasDeletedEvent;
use App\Events\PaymentWasRefundedEvent;
use App\Events\PaymentWasRestoredEvent;
use App\Events\PaymentWasVoidedEvent;
use App\Events\BillCreditWasArchivedEvent;
use App\Events\BillCreditWasCreatedEvent;
use App\Events\BillCreditWasDeletedEvent;
use App\Events\BillCreditWasRestoredEvent;
use App\Events\BillInvitationWasEmailedEvent;
use App\Events\BillInvitationWasViewedEvent;
use App\Events\BillWasArchivedEvent;
use App\Events\BillWasCreatedEvent;
use App\Events\BillWasDeletedEvent;
use App\Events\BillWasRestoredEvent;
use App\Events\BillWasUpdatedEvent;
use App\Events\BillPaymentFailedEvent;
use App\Events\BillPaymentWasArchivedEvent;
use App\Events\BillPaymentWasCreatedEvent;
use App\Events\BillPaymentWasDeletedEvent;
use App\Events\BillPaymentWasRefundedEvent;
use App\Events\BillPaymentWasRestoredEvent;
use App\Events\BillPaymentWasVoidedEvent;
use App\Events\BillQuoteInvitationWasEmailedEvent;
use App\Events\BillQuoteInvitationWasViewedEvent;
use App\Events\BillQuoteWasArchivedEvent;
use App\Events\BillQuoteWasCreatedEvent;
use App\Events\BillQuoteWasDeletedEvent;
use App\Events\BillQuoteWasRestoredEvent;
use App\Events\BillQuoteWasUpdatedEvent;
use App\Events\QuoteInvitationWasApprovedEvent;
use App\Events\QuoteInvitationWasEmailedEvent;
use App\Events\QuoteInvitationWasViewedEvent;
use App\Events\BillQuoteInvitationWasApprovedEvent;
use App\Events\QuoteWasArchivedEvent;
use App\Events\QuoteWasCreatedEvent;
use App\Events\QuoteWasDeletedEvent;
use App\Events\QuoteWasRestoredEvent;
use App\Events\QuoteWasUpdatedEvent;
use App\Events\TaskWasArchivedEvent;
use App\Events\TaskWasCreatedEvent;
use App\Events\TaskWasDeletedEvent;
use App\Events\TaskWasRestoredEvent;
use App\Events\TaskWasUpdatedEvent;
use App\Events\VendorWasArchivedEvent;
use App\Events\VendorWasCreatedEvent;
use App\Events\VendorWasDeletedEvent;
use App\Events\VendorWasRestoredEvent;
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
    public function createdClient(ClientWasCreatedEvent $event)
    {
        $this->activityRepo->create(
            $event->client,
            ACTIVITY_TYPE_CREATE_CLIENT
        );
    }

    public function deletedClient(ClientWasDeletedEvent $event)
    {
        $this->activityRepo->create(
            $event->client,
            ACTIVITY_TYPE_DELETE_CLIENT
        );
    }

    public function archivedClient(ClientWasArchivedEvent $event)
    {
        if ($event->client->is_deleted) {
            return;
        }

        $this->activityRepo->create(
            $event->client,
            ACTIVITY_TYPE_ARCHIVE_CLIENT
        );
    }

    public function restoredClient(ClientWasRestoredEvent $event)
    {
        $this->activityRepo->create(
            $event->client,
            ACTIVITY_TYPE_RESTORE_CLIENT
        );
    }

//    vendor activities
    public function createdVendor(VendorWasCreatedEvent $event)
    {
        $this->activityRepo->create(
            $event->vendor,
            ACTIVITY_TYPE_CREATE_VENDOR
        );
    }

    public function deletedVendor(VendorWasDeletedEvent $event)
    {
        $this->activityRepo->create(
            $event->vendor,
            ACTIVITY_TYPE_DELETE_VENDOR
        );
    }

    public function archivedVendor(VendorWasArchivedEvent $event)
    {
        if ($event->vendor->is_deleted) {
            return;
        }

        $this->activityRepo->create(
            $event->vendor,
            ACTIVITY_TYPE_ARCHIVE_VENDOR
        );
    }

    public function restoredVendor(VendorWasRestoredEvent $event)
    {
        $this->activityRepo->create(
            $event->vendor,
            ACTIVITY_TYPE_RESTORE_VENDOR
        );
    }

//  invoice activities
    public function createdInvoice(InvoiceWasCreatedEvent $event)
    {
        $this->activityRepo->create(
            $event->invoice, ACTIVITY_TYPE_CREATE_INVOICE, $event->invoice->getAdjustment()
        );
    }

    public function updatedInvoice(InvoiceWasUpdatedEvent $event)
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

    public function deletedInvoice(InvoiceWasDeletedEvent $event)
    {
        $invoice = $event->invoice;

        $this->activityRepo->create(
            $invoice,
            ACTIVITY_TYPE_DELETE_INVOICE,
            $invoice->affectsBalance() ? $invoice->balance * -1 : 0,
            $invoice->affectsBalance() ? $invoice->getAmountPaid() * -1 : 0
        );
    }

    public function archivedInvoice(InvoiceWasArchivedEvent $event)
    {
        if ($event->invoice->is_deleted) {
            return;
        }

        $this->activityRepo->create(
            $event->invoice,
            ACTIVITY_TYPE_ARCHIVE_INVOICE
        );
    }

    public function restoredInvoice(InvoiceWasRestoredEvent $event)
    {
        $invoice = $event->invoice;

        $this->activityRepo->create(
            $invoice,
            ACTIVITY_TYPE_RESTORE_INVOICE,
            $invoice->affectsBalance() && $event->fromDeleted ? $invoice->balance : 0,
            $invoice->affectsBalance() && $event->fromDeleted ? $invoice->getAmountPaid() : 0
        );
    }

    public function emailedInvoice(InvoiceInvitationWasEmailedEvent $event)
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

    public function viewedInvoice(InvoiceInvitationWasViewedEvent $event)
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
    public function createdQuote(QuoteWasCreatedEvent $event)
    {
        $this->activityRepo->create(
            $event->quote,
            ACTIVITY_TYPE_CREATE_QUOTE
        );
    }

    public function updatedQuote(QuoteWasUpdatedEvent $event)
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

    public function deletedQuote(QuoteWasDeletedEvent $event)
    {
        $this->activityRepo->create(
            $event->quote,
            ACTIVITY_TYPE_DELETE_QUOTE
        );
    }

    public function archivedQuote(QuoteWasArchivedEvent $event)
    {
        if ($event->quote->is_deleted) {
            return;
        }

        $this->activityRepo->create(
            $event->quote,
            ACTIVITY_TYPE_ARCHIVE_QUOTE
        );
    }

    public function restoredQuote(QuoteWasRestoredEvent $event)
    {
        $this->activityRepo->create(
            $event->quote,
            ACTIVITY_TYPE_RESTORE_QUOTE
        );
    }

    public function emailedQuote(QuoteInvitationWasEmailedEvent $event)
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

    public function viewedQuote(QuoteInvitationWasViewedEvent $event)
    {
        $this->activityRepo->create(
            $event->quote,
            ACTIVITY_TYPE_VIEW_QUOTE,
            false,
            false,
            $event->invitation
        );
    }

    public function approvedQuote(QuoteInvitationWasApprovedEvent $event)
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
    public function createdCredit(CreditWasCreatedEvent $event)
    {
        $this->activityRepo->create(
            $event->credit,
            ACTIVITY_TYPE_CREATE_CREDIT
        );
    }

    public function deletedCredit(CreditWasDeletedEvent $event)
    {
        $this->activityRepo->create(
            $event->credit,
            ACTIVITY_TYPE_DELETE_CREDIT
        );
    }

    public function archivedCredit(CreditWasArchivedEvent $event)
    {
        if ($event->credit->is_deleted) {
            return;
        }

        $this->activityRepo->create(
            $event->credit,
            ACTIVITY_TYPE_ARCHIVE_CREDIT
        );
    }

    public function restoredCredit(CreditWasRestoredEvent $event)
    {
        $this->activityRepo->create(
            $event->credit,
            ACTIVITY_TYPE_RESTORE_CREDIT
        );
    }

//   invoice payment activities
    public function createdPayment(PaymentWasCreatedEvent $event)
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

    public function deletedPayment(PaymentWasDeletedEvent $event)
    {
        $payment = $event->payment;

        $this->activityRepo->create(
            $payment,
            ACTIVITY_TYPE_DELETE_PAYMENT,
            $payment->isFailedOrVoided() ? 0 : $payment->getCompletedAmount(),
            $payment->isFailedOrVoided() ? 0 : $payment->getCompletedAmount() * -1
        );
    }

    public function refundedPayment(PaymentWasRefundedEvent $event)
    {
        $payment = $event->payment;

        $this->activityRepo->create(
            $payment,
            ACTIVITY_TYPE_REFUNDED_PAYMENT,
            $event->refundAmount,
            $event->refundAmount * -1
        );
    }

    public function voidedPayment(PaymentWasVoidedEvent $event)
    {
        $payment = $event->payment;

        $this->activityRepo->create(
            $payment,
            ACTIVITY_TYPE_VOIDED_PAYMENT,
            $payment->is_deleted ? 0 : $payment->getCompletedAmount(),
            $payment->is_deleted ? 0 : $payment->getCompletedAmount() * -1
        );
    }

    public function failedPayment(PaymentFailedEvent $event)
    {
        $payment = $event->payment;

        $this->activityRepo->create(
            $payment,
            ACTIVITY_TYPE_FAILED_PAYMENT,
            $payment->is_deleted ? 0 : $payment->getCompletedAmount(),
            $payment->is_deleted ? 0 : $payment->getCompletedAmount() * -1
        );
    }

    public function archivedPayment(PaymentWasArchivedEvent $event)
    {
        if ($event->payment->is_deleted) {
            return;
        }

        $this->activityRepo->create(
            $event->payment,
            ACTIVITY_TYPE_ARCHIVE_PAYMENT
        );
    }

    public function restoredPayment(PaymentWasRestoredEvent $event)
    {
        $payment = $event->payment;

        $this->activityRepo->create(
            $payment,
            ACTIVITY_TYPE_RESTORE_PAYMENT,
            $event->fromDeleted && !$payment->isFailedOrVoided() ? $payment->getCompletedAmount() * -1 : 0,
            $event->fromDeleted && !$payment->isFailedOrVoided() ? $payment->getCompletedAmount() : 0
        );
    }

//  Bill invoice activities
    public function createdBill(BillWasCreatedEvent $event)
    {
        $this->activityRepo->createBill(
            $event->Bill, ACTIVITY_TYPE_CREATE_BILL,
            $event->Bill->getAdjustment()
        );
    }

    public function updatedBill(BillWasUpdatedEvent $event)
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

    public function deletedBill(BillWasDeletedEvent $event)
    {
        $Bill = $event->Bill;

        $this->activityRepo->createBill(
            $Bill,
            ACTIVITY_TYPE_DELETE_BILL,
            $Bill->affectsBalance() ? $Bill->balance * -1 : 0,
            $Bill->affectsBalance() ? $Bill->getAmountPaid() * -1 : 0
        );
    }

    public function archivedBill(BillWasArchivedEvent $event)
    {
        if ($event->Bill->is_deleted) {
            return;
        }

        $this->activityRepo->createBill(
            $event->Bill,
            ACTIVITY_TYPE_ARCHIVE_BILL
        );
    }

    public function restoredBill(BillWasRestoredEvent $event)
    {
        $Bill = $event->Bill;

        $this->activityRepo->createBill(
            $Bill,
            ACTIVITY_TYPE_RESTORE_INVOICE,
            $Bill->affectsBalance() && $event->fromDeleted ? $Bill->balance : 0,
            $Bill->affectsBalance() && $event->fromDeleted ? $Bill->getAmountPaid() : 0
        );
    }

    public function emailedBill(BillInvitationWasEmailedEvent $event)
    {
        $this->activityRepo->createBill(
            $event->BillInvitation->BILL,
            ACTIVITY_TYPE_EMAIL_BILL,
            false,
            false,
            $event->BillInvitation,
            $event->notes
        );
    }

    public function viewedBill(BillInvitationWasViewedEvent $event)
    {
        $this->activityRepo->createBill(
            $event->Bill,
            ACTIVITY_TYPE_VIEW_BILL,
            false,
            false,
            $event->BillInvitation
        );
    }

//  invoice quote activities
    public function createdBillQuote(BillQuoteWasCreatedEvent $event)
    {
        $this->activityRepo->createBill(
            $event->quote,
            ACTIVITY_TYPE_CREATE_bill_quote
        );
    }

    public function updatedBillQuote(BillQuoteWasUpdatedEvent $event)
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

    public function deletedBillQuote(BillQuoteWasDeletedEvent $event)
    {
        $this->activityRepo->createBill(
            $event->quote,
            ACTIVITY_TYPE_DELETE_bill_quote
        );
    }

    public function archivedBillQuote(BillQuoteWasArchivedEvent $event)
    {
        if ($event->quote->is_deleted) {
            return;
        }

        $this->activityRepo->createBill(
            $event->quote,
            ACTIVITY_TYPE_ARCHIVE_bill_quote
        );
    }

    public function restoredBillQuote(BillQuoteWasRestoredEvent $event)
    {
        $this->activityRepo->createBill(
            $event->quote,
            ACTIVITY_TYPE_RESTORE_bill_quote
        );
    }

    public function emailedBillQuote(BillQuoteInvitationWasEmailedEvent $event)
    {
        $this->activityRepo->createBill(
            $event->BillInvitation->BILL,
            ACTIVITY_TYPE_EMAIL_BILL_QUOTE,
            false,
            false,
            $event->BillInvitation,
            $event->notes
        );
    }

    public function viewedBillQuote(BillQuoteInvitationWasViewedEvent $event)
    {
        $this->activityRepo->createBill(
            $event->quote,
            ACTIVITY_TYPE_VIEW_bill_quote,
            false,
            false,
            $event->invitation
        );
    }

    public function approvedBillQuote(BillQuoteInvitationWasApprovedEvent $event)
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
    public function createdBillCredit(BillCreditWasCreatedEvent $event)
    {
        $this->activityRepo->createBill(
            $event->BillCredit,
            ACTIVITY_TYPE_CREATE_BILL_CREDIT
        );
    }

    public function deletedBillCredit(BillCreditWasDeletedEvent $event)
    {
        $this->activityRepo->createBill(
            $event->BillCredit,
            ACTIVITY_TYPE_DELETE_BILL_CREDIT
        );
    }

    public function archivedBillCredit(BillCreditWasArchivedEvent $event)
    {
        if ($event->BillCredit->is_deleted) {
            return;
        }

        $this->activityRepo->createBill(
            $event->BillCredit,
            ACTIVITY_TYPE_ARCHIVE_BILL_CREDIT
        );
    }

    public function restoredBillCredit(BillCreditWasRestoredEvent $event)
    {
        $this->activityRepo->createBill(
            $event->BillCredit,
            ACTIVITY_TYPE_RESTORE_BILL_CREDIT
        );
    }

//   invoice payment activities
    public function createdBillPayment(BillPaymentWasCreatedEvent $event)
    {
        $this->activityRepo->createBill(
            $event->BillPayment,
            ACTIVITY_TYPE_CREATE_BILL_PAYMENT,
            $event->BillPayment->amount * -1,
            $event->BillPayment->amount,
            false,
            App::runningInConsole() ? 'auto_billed' : ''
        );
    }

    public function deletedBillPayment(BillPaymentWasDeletedEvent $event)
    {
        $payment = $event->BillPayment;

        $this->activityRepo->createBill(
            $payment,
            ACTIVITY_TYPE_DELETE_BILL_PAYMENT,
            $payment->isFailedOrVoided() ? 0 : $payment->getCompletedAmount(),
            $payment->isFailedOrVoided() ? 0 : $payment->getCompletedAmount() * -1
        );
    }

    public function refundedBillPayment(BillPaymentWasRefundedEvent $event)
    {
        $payment = $event->BillPayment;

        $this->activityRepo->createBill(
            $payment,
            ACTIVITY_TYPE_REFUNDED_BILL_PAYMENT,
            $event->refundAmount,
            $event->refundAmount * -1
        );
    }

    public function voidedBillPayment(BillPaymentWasVoidedEvent $event)
    {
        $payment = $event->BillPayment;

        $this->activityRepo->createBill(
            $payment,
            ACTIVITY_TYPE_VOIDED_BILL_PAYMENT,
            $payment->is_deleted ? 0 : $payment->getCompletedAmount(),
            $payment->is_deleted ? 0 : $payment->getCompletedAmount() * -1
        );
    }

    public function failedBillPayment(BillPaymentFailedEvent $event)
    {
        $payment = $event->BillPayment;

        $this->activityRepo->createBill(
            $payment,
            ACTIVITY_TYPE_FAILED_BILL_PAYMENT,
            $payment->is_deleted ? 0 : $payment->getCompletedAmount(),
            $payment->is_deleted ? 0 : $payment->getCompletedAmount() * -1
        );
    }

    public function archivedBillPayment(BillPaymentWasArchivedEvent $event)
    {
        if ($event->BillPayment->is_deleted) {
            return;
        }

        $this->activityRepo->createBill(
            $event->BillPayment,
            ACTIVITY_TYPE_ARCHIVE_BILL_PAYMENT
        );
    }

    public function restoredBillPayment(BillPaymentWasRestoredEvent $event)
    {
        $payment = $event->BillPayment;

        $this->activityRepo->createBill(
            $payment,
            ACTIVITY_TYPE_RESTORE_BILL,
            $event->fromDeleted && !$payment->isFailedOrVoided() ? $payment->getCompletedAmount() * -1 : 0,
            $event->fromDeleted && !$payment->isFailedOrVoided() ? $payment->getCompletedAmount() : 0
        );
    }

//  task activities
    public function createdTask(TaskWasCreatedEvent $event)
    {
        $this->activityRepo->create(
            $event->task,
            ACTIVITY_TYPE_CREATE_TASK
        );
    }

    public function updatedTask(TaskWasUpdatedEvent $event)
    {
        if (!$event->task->isChanged()) {
            return;
        }

        $this->activityRepo->create(
            $event->task,
            ACTIVITY_TYPE_UPDATE_TASK
        );
    }

    public function archivedTask(TaskWasArchivedEvent $event)
    {
        if ($event->task->is_deleted) {
            return;
        }

        $this->activityRepo->create(
            $event->task,
            ACTIVITY_TYPE_ARCHIVE_TASK
        );
    }

    public function deletedTask(TaskWasDeletedEvent $event)
    {
        $this->activityRepo->create(
            $event->task,
            ACTIVITY_TYPE_DELETE_TASK
        );
    }

    public function restoredTask(TaskWasRestoredEvent $event)
    {
        $this->activityRepo->create(
            $event->task,
            ACTIVITY_TYPE_RESTORE_TASK
        );
    }

//  expense activities
    public function createdExpense(ExpenseWasCreatedEvent $event)
    {
        $this->activityRepo->create(
            $event->expense,
            ACTIVITY_TYPE_CREATE_EXPENSE
        );
    }

    public function updatedExpense(ExpenseWasUpdatedEvent $event)
    {
        if (!$event->expense->isChanged()) {
            return;
        }

        $this->activityRepo->create(
            $event->expense,
            ACTIVITY_TYPE_UPDATE_EXPENSE
        );
    }

    public function archivedExpense(ExpenseWasArchivedEvent $event)
    {
        if ($event->expense->is_deleted) {
            return;
        }

        $this->activityRepo->create(
            $event->expense,
            ACTIVITY_TYPE_ARCHIVE_EXPENSE
        );
    }

    public function deletedExpense(ExpenseWasDeletedEvent $event)
    {
        $this->activityRepo->create(
            $event->expense,
            ACTIVITY_TYPE_DELETE_EXPENSE
        );
    }

    public function restoredExpense(ExpenseWasRestoredEvent $event)
    {
        $this->activityRepo->create(
            $event->expense,
            ACTIVITY_TYPE_RESTORE_EXPENSE
        );
    }
}
