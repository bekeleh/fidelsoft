<?php

namespace App\Listeners\Report;

use App;
use App\Events\Client\ClientWasArchivedEvent;
use App\Events\Client\ClientWasCreatedEvent;
use App\Events\Client\ClientWasDeletedEvent;
use App\Events\Client\ClientWasRestoredEvent;
use App\Events\Client\CreditWasArchivedEvent;
use App\Events\Client\CreditWasCreatedEvent;
use App\Events\Client\CreditWasDeletedEvent;
use App\Events\Client\CreditWasRestoredEvent;
use App\Events\Expense\ExpenseWasArchivedEvent;
use App\Events\Expense\ExpenseWasCreatedEvent;
use App\Events\Expense\ExpenseWasDeletedEvent;
use App\Events\Expense\ExpenseWasRestoredEvent;
use App\Events\Expense\ExpenseWasUpdatedEvent;
use App\Events\Purchase\BillCreditWasArchivedEvent;
use App\Events\Purchase\BillCreditWasCreatedEvent;
use App\Events\Purchase\BillCreditWasDeletedEvent;
use App\Events\Purchase\BillCreditWasRestoredEvent;
use App\Events\Purchase\BillInvitationWasEmailedEvent;
use App\Events\Purchase\BillInvitationWasViewedEvent;
use App\Events\Purchase\BillPaymentFailedEvent;
use App\Events\Purchase\BillPaymentWasArchivedEvent;
use App\Events\Purchase\BillPaymentWasCreatedEvent;
use App\Events\Purchase\BillPaymentWasDeletedEvent;
use App\Events\Purchase\BillPaymentWasRefundedEvent;
use App\Events\Purchase\BillPaymentWasRestoredEvent;
use App\Events\Purchase\BillPaymentWasVoidedEvent;
use App\Events\Purchase\BillQuoteInvitationWasApprovedEvent;
use App\Events\Purchase\BillQuoteInvitationWasEmailedEvent;
use App\Events\Purchase\BillQuoteInvitationWasViewedEvent;
use App\Events\Purchase\BillQuoteWasArchivedEvent;
use App\Events\Purchase\BillQuoteWasCreatedEvent;
use App\Events\Purchase\BillQuoteWasDeletedEvent;
use App\Events\Purchase\BillQuoteWasRestoredEvent;
use App\Events\Purchase\BillQuoteWasUpdatedEvent;
use App\Events\Purchase\BillWasArchivedEvent;
use App\Events\Purchase\BillWasCreatedEvent;
use App\Events\Purchase\BillWasDeletedEvent;
use App\Events\Purchase\BillWasRestoredEvent;
use App\Events\Purchase\BillWasUpdatedEvent;
use App\Events\Sale\InvoiceInvitationWasEmailedEvent;
use App\Events\Sale\InvoiceInvitationWasViewedEvent;
use App\Events\Sale\InvoiceWasArchivedEvent;
use App\Events\Sale\InvoiceWasCreatedEvent;
use App\Events\Sale\InvoiceWasDeletedEvent;
use App\Events\Sale\InvoiceWasRestoredEvent;
use App\Events\Sale\InvoiceWasUpdatedEvent;
use App\Events\Sale\PaymentFailedEvent;
use App\Events\Sale\PaymentWasArchivedEvent;
use App\Events\Sale\PaymentWasCreatedEvent;
use App\Events\Sale\PaymentWasDeletedEvent;
use App\Events\Sale\PaymentWasRefundedEvent;
use App\Events\Sale\PaymentWasRestoredEvent;
use App\Events\Sale\PaymentWasVoidedEvent;
use App\Events\Sale\QuoteInvitationWasApprovedEvent;
use App\Events\Sale\QuoteInvitationWasEmailedEvent;
use App\Events\Sale\QuoteInvitationWasViewedEvent;
use App\Events\Sale\QuoteWasArchivedEvent;
use App\Events\Sale\QuoteWasCreatedEvent;
use App\Events\Sale\QuoteWasDeletedEvent;
use App\Events\Sale\QuoteWasRestoredEvent;
use App\Events\Sale\QuoteWasUpdatedEvent;
use App\Events\Setting\TaskWasArchivedEvent;
use App\Events\Setting\TaskWasCreatedEvent;
use App\Events\Setting\TaskWasDeletedEvent;
use App\Events\Setting\TaskWasRestoredEvent;
use App\Events\Setting\TaskWasUpdatedEvent;
use App\Events\Vendor\VendorWasArchivedEvent;
use App\Events\Vendor\VendorWasCreatedEvent;
use App\Events\Vendor\VendorWasDeletedEvent;
use App\Events\Vendor\VendorWasRestoredEvent;
use App\Models\Bill;
use App\Models\Invoice;
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
            $event->bill, ACTIVITY_TYPE_CREATE_BILL,
            $event->bill->getAdjustment()
        );
    }

    public function updatedBill(BillWasUpdatedEvent $event)
    {
        if (!$event->bill->isChanged()) {
            return;
        }

        $backupInvoice = Bill::with('invoice_items', 'vendor.account', 'vendor.contacts')
            ->withTrashed()
            ->find($event->bill->id);

        $activity = $this->activityRepo->createBill(
            $event->bill,
            ACTIVITY_TYPE_UPDATE_BILL,
            $event->bill->getAdjustment()
        );

        $activity->json_backup = $backupInvoice->hidePrivateFields()->toJSON();

        $activity->save();
    }

    public function deletedBill(BillWasDeletedEvent $event)
    {
        $bill = $event->bill;

        $this->activityRepo->createBill(
            $bill,
            ACTIVITY_TYPE_DELETE_BILL,
            $bill->affectsBalance() ? $bill->balance * -1 : 0,
            $bill->affectsBalance() ? $bill->getAmountPaid() * -1 : 0
        );
    }

    public function archivedBill(BillWasArchivedEvent $event)
    {
        if ($event->bill->is_deleted) {
            return;
        }

        $this->activityRepo->createBill(
            $event->bill,
            ACTIVITY_TYPE_ARCHIVE_BILL
        );
    }

    public function restoredBill(BillWasRestoredEvent $event)
    {
        $bill = $event->bill;

        $this->activityRepo->createBill(
            $bill,
            ACTIVITY_TYPE_RESTORE_INVOICE,
            $bill->affectsBalance() && $event->fromDeleted ? $bill->balance : 0,
            $bill->affectsBalance() && $event->fromDeleted ? $bill->getAmountPaid() : 0
        );
    }

    public function emailedBill(BillInvitationWasEmailedEvent $event)
    {
        $this->activityRepo->createBill(
            $event->billInvitation->BILL,
            ACTIVITY_TYPE_EMAIL_BILL,
            false,
            false,
            $event->billInvitation,
            $event->notes
        );
    }

    public function viewedBill(BillInvitationWasViewedEvent $event)
    {
        $this->activityRepo->createBill(
            $event->bill,
            ACTIVITY_TYPE_VIEW_BILL,
            false,
            false,
            $event->billInvitation
        );
    }

//  invoice quote activities
    public function createdBillQuote(BillQuoteWasCreatedEvent $event)
    {
        $this->activityRepo->createBill(
            $event->billQuote,
            ACTIVITY_TYPE_CREATE_BILL_QUOTE
        );
    }

    public function updatedBillQuote(BillQuoteWasUpdatedEvent $event)
    {
        if (!$event->billQuote->isChanged()) {
            return;
        }

        $backupQuote = Bill::with('invoice_items', 'vendor.account', 'vendor.contacts')
            ->withTrashed()
            ->find($event->billQuote->id);

        $activity = $this->activityRepo->createBill(
            $event->billQuote,
            ACTIVITY_TYPE_UPDATE_BILL_QUOTE
        );

        $activity->json_backup = $backupQuote->hidePrivateFields()->toJSON();
        $activity->save();
    }

    public function deletedBillQuote(BillQuoteWasDeletedEvent $event)
    {
        $this->activityRepo->createBill(
            $event->billQuote,
            ACTIVITY_TYPE_DELETE_BILL_QUOTE
        );
    }

    public function archivedBillQuote(BillQuoteWasArchivedEvent $event)
    {
        if ($event->billQuote->is_deleted) {
            return;
        }

        $this->activityRepo->createBill(
            $event->billQuote,
            ACTIVITY_TYPE_ARCHIVE_BILL_QUOTE
        );
    }

    public function restoredBillQuote(BillQuoteWasRestoredEvent $event)
    {
        $this->activityRepo->createBill(
            $event->billQuote,
            ACTIVITY_TYPE_RESTORE_BILL_QUOTE
        );
    }

    public function emailedBillQuote(BillQuoteInvitationWasEmailedEvent $event)
    {
        $this->activityRepo->createBill(
            $event->billInvitation->BILL,
            ACTIVITY_TYPE_EMAIL_BILL_QUOTE,
            false,
            false,
            $event->billInvitation,
            $event->notes
        );
    }

    public function viewedBillQuote(BillQuoteInvitationWasViewedEvent $event)
    {
        $this->activityRepo->createBill(
            $event->quote,
            ACTIVITY_TYPE_VIEW_BILL_QUOTE,
            false,
            false,
            $event->invitation
        );
    }

    public function approvedBillQuote(BillQuoteInvitationWasApprovedEvent $event)
    {
        $this->activityRepo->createBill(
            $event->quote,
            ACTIVITY_TYPE_APPROVE_BILL_QUOTE,
            false,
            false,
            $event->billInvitation
        );
    }

//  invoice credit activities
    public function createdBillCredit(BillCreditWasCreatedEvent $event)
    {
        $this->activityRepo->createBill(
            $event->billCredit,
            ACTIVITY_TYPE_CREATE_VENDOR_CREDIT
        );
    }

    public function deletedBillCredit(BillCreditWasDeletedEvent $event)
    {
        $this->activityRepo->createBill(
            $event->billCredit,
            ACTIVITY_TYPE_DELETE_VENDOR_CREDIT
        );
    }

    public function archivedBillCredit(BillCreditWasArchivedEvent $event)
    {
        if ($event->billCredit->is_deleted) {
            return;
        }

        $this->activityRepo->createBill(
            $event->billCredit,
            ACTIVITY_TYPE_ARCHIVE_VENDOR_CREDIT
        );
    }

    public function restoredBillCredit(BillCreditWasRestoredEvent $event)
    {
        $this->activityRepo->createBill(
            $event->billCredit,
            ACTIVITY_TYPE_RESTORE_VENDOR_CREDIT
        );
    }

//   bill payment activities
    public function createdBillPayment(BillPaymentWasCreatedEvent $event)
    {
        $this->activityRepo->createBill(
            $event->billPayment,
            ACTIVITY_TYPE_CREATE_BILL_PAYMENT,
            $event->billPayment->amount * -1,
            $event->billPayment->amount,
            false,
            App::runningInConsole() ? 'auto_billed' : ''
        );
    }

    public function deletedBillPayment(BillPaymentWasDeletedEvent $event)
    {
        $payment = $event->billPayment;

        $this->activityRepo->createBill(
            $payment,
            ACTIVITY_TYPE_DELETE_BILL_PAYMENT,
            $payment->isFailedOrVoided() ? 0 : $payment->getCompletedAmount(),
            $payment->isFailedOrVoided() ? 0 : $payment->getCompletedAmount() * -1
        );
    }

    public function refundedBillPayment(BillPaymentWasRefundedEvent $event)
    {
        $payment = $event->billPayment;

        $this->activityRepo->createBill(
            $payment,
            ACTIVITY_TYPE_REFUNDED_BILL_PAYMENT,
            $event->refundAmount,
            $event->refundAmount * -1
        );
    }

    public function voidedBillPayment(BillPaymentWasVoidedEvent $event)
    {
        $payment = $event->billPayment;

        $this->activityRepo->createBill(
            $payment,
            ACTIVITY_TYPE_VOIDED_BILL_PAYMENT,
            $payment->is_deleted ? 0 : $payment->getCompletedAmount(),
            $payment->is_deleted ? 0 : $payment->getCompletedAmount() * -1
        );
    }

    public function failedBillPayment(BillPaymentFailedEvent $event)
    {
        $payment = $event->billPayment;

        $this->activityRepo->createBill(
            $payment,
            ACTIVITY_TYPE_FAILED_BILL_PAYMENT,
            $payment->is_deleted ? 0 : $payment->getCompletedAmount(),
            $payment->is_deleted ? 0 : $payment->getCompletedAmount() * -1
        );
    }

    public function archivedBillPayment(BillPaymentWasArchivedEvent $event)
    {
        if ($event->billPayment->is_deleted) {
            return;
        }

        $this->activityRepo->createBill(
            $event->billPayment,
            ACTIVITY_TYPE_ARCHIVE_BILL_PAYMENT
        );
    }

    public function restoredBillPayment(BillPaymentWasRestoredEvent $event)
    {
        $payment = $event->billPayment;

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
