<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event handler mappings for the application.
     *
     * @var array
     */
    protected $listen = [
//      user login
        'Illuminate\Auth\Events\Login' => [
            'App\Listeners\Auth\UserSuccessfulLoginListener',
        ],

        'Illuminate\Auth\Events\Failed' => [
            'App\Listeners\Auth\FailedLoginListener',
        ],

//       User events
        'App\Events\Auth\UserSignedUpEvent' => [
            'App\Listeners\Auth\UserSignedUpListener',
        ],
        'App\Events\Auth\UserLoggedInEvent' => [
            'App\Listeners\Auth\UserLoggedListener',
        ],
        'App\Events\Auth\UserSettingsChangedEvent' => [
            'App\Listeners\Auth\UserSettingsChangedListener',
        ],

//       User events
        'App\Events\User\UserWasCreatedEvent' => [
            'App\Listeners\User\UserListener@createdUser',
        ],
        'App\Events\User\UserWasUpdatedEvent' => [
            'App\Listeners\User\UserListener@updatedUser',
        ],
        'App\Events\User\UserWasDeletedEvent' => [
            'App\Listeners\User\UserListener@deletedUser',
        ],

//      Clients
        'App\Events\Client\ClientWasCreatedEvent' => [
            'App\Listeners\Report\ActivityListener@createdClient',
            'App\Listeners\Client\ClientListener@createdClient',
        ],
        'App\Events\Client\ClientWasArchivedEvent' => [
            'App\Listeners\Report\ActivityListener@archivedClient',
        ],
        'App\Events\Client\ClientWasUpdatedEvent' => [
            'App\Listeners\Client\ClientListener@updatedClient',
        ],
        'App\Events\Client\ClientWasDeletedEvent' => [
            'App\Listeners\Report\ActivityListener@deletedClient',
            'App\Listeners\Client\ClientListener@deletedClient',
            'App\Listeners\Report\HistoryListener@deletedClient',
        ],
        'App\Events\Client\ClientWasRestoredEvent' => [
            'App\Listeners\Report\ActivityListener@restoredClient',
        ],

//     Vendor events
        'App\Events\Vendor\VendorWasCreatedEvent' => [
            'App\Listeners\Report\ActivityListener@createdVendor',
            'App\Listeners\Vendor\VendorListener@createdVendor',
        ],
        'App\Events\Vendor\VendorWasUpdatedEvent' => [
            'App\Listeners\Vendor\VendorListener@updatedVendor',
        ],
        'App\Events\Vendor\VendorWasArchivedEvent' => [
            'App\Listeners\Report\ActivityListener@archivedVendor',
        ],
        'App\Events\Vendor\VendorWasDeletedEvent' => [
            'App\Listeners\Report\ActivityListener@deletedVendor',
            'App\Listeners\Vendor\VendorListener@deletedVendor',
            'App\Listeners\Report\HistoryListener@deletedVendor',
        ],
        'App\Events\Vendor\VendorWasRestoredEvent' => [
            'App\Listeners\Report\ActivityListener@restoredVendor',
        ],

//       Invoices
        'App\Events\Sale\InvoiceWasCreatedEvent' => [
            'App\Listeners\Report\ActivityListener@createdInvoice',
            'App\Listeners\Sale\InvoiceListener@createdInvoice',
        ],
        'App\Events\Sale\InvoiceWasUpdatedEvent' => [
            'App\Listeners\Report\ActivityListener@updatedInvoice',
            'App\Listeners\Sale\InvoiceListener@updatedInvoice',
        ],
        'App\Events\Sale\InvoiceItemsWereUpdatedEvent' => [
            'App\Listeners\Sale\InvoiceItemListener@updatedInvoice',
        ],
        'App\Events\Sale\InvoiceItemsWereCreatedEvent' => [
            'App\Listeners\Sale\InvoiceItemListener@createdInvoice',
        ],
        'App\Events\Sale\InvoiceWasArchivedEvent' => [
            'App\Listeners\Report\ActivityListener@archivedInvoice',
        ],
        'App\Events\Sale\InvoiceWasDeletedEvent' => [
            'App\Listeners\Report\ActivityListener@deletedInvoice',
            'App\Listeners\Sale\InvoiceListener@deletedInvoice',
            'App\Listeners\Sale\InvoiceItemListener@deletedInvoice',
            'App\Listeners\Setting\TaskListener@deletedInvoice',
            'App\Listeners\Expense\Expense\ExpenseListener@deletedInvoice',
            'App\Listeners\Report\HistoryListener@deletedInvoice',
        ],
        'App\Events\Sale\InvoiceWasRestoredEvent' => [
            'App\Listeners\Report\ActivityListener@restoredInvoice',
        ],
        'App\Events\Sale\InvoiceWasEmailedEvent' => [
            'App\Listeners\Sale\InvoiceListener@emailedInvoice',
            'App\Listeners\Sale\SendInvoiceNotification@emailedInvoice',
        ],
        'App\Events\Sale\InvoiceInvitationWasEmailedEvent' => [
            'App\Listeners\Report\ActivityListener@emailedInvoice',
        ],
        'App\Events\Sale\InvoiceInvitationWasViewedEvent' => [
            'App\Listeners\Report\ActivityListener@viewedInvoice',
            'App\Listeners\Sale\SendInvoiceNotification@viewedInvoice',
            'App\Listeners\Sale\InvoiceListener@viewedInvoice',
        ],

//      Quotes
        'App\Events\Sale\QuoteWasCreatedEvent' => [
            'App\Listeners\Report\ActivityListener@createdQuote',
        ],
        'App\Events\Sale\QuoteWasUpdatedEvent' => [
            'App\Listeners\Report\ActivityListener@updatedQuote',
        ],
        'App\Events\Sale\QuoteItemsWereCreatedEvent' => [
            'App\Listeners\Sale\InvoiceItemListener@createdQuote',
        ],
        'App\Events\Sale\QuoteItemsWereUpdatedEvent' => [
            'App\Listeners\Sale\InvoiceItemListener@updatedQuote',
        ],
        'App\Events\Sale\QuoteWasArchivedEvent' => [
            'App\Listeners\Report\ActivityListener@archivedQuote',
        ],
        'App\Events\Sale\QuoteWasDeletedEvent' => [
            'App\Listeners\Report\ActivityListener@deletedQuote',
            'App\Listeners\Report\HistoryListener@deletedQuote',
            'App\Listeners\Sale\InvoiceItemListener@deletedQuote',
        ],
        'App\Events\Sale\QuoteWasRestoredEvent' => [
            'App\Listeners\Report\ActivityListener@restoredQuote',
        ],
        'App\Events\Sale\QuoteWasEmailedEvent' => [
            'App\Listeners\Sale\QuoteListener@emailedQuote',
            'App\Listeners\Sale\SendInvoiceNotification@emailedQuote',
        ],
        'App\Events\Sale\QuoteInvitationWasEmailedEvent' => [
            'App\Listeners\Report\ActivityListener@emailedQuote',
        ],
//      sale quote viewed
        'App\Events\Sale\QuoteInvitationWasViewedEvent' => [
            'App\Listeners\Report\ActivityListener@viewedQuote',
            'App\Listeners\Sale\SendInvoiceNotification@viewedQuote',
            'App\Listeners\Sale\QuoteListener@viewedQuote',
        ],
        'App\Events\Sale\QuoteInvitationWasApprovedEvent' => [
            'App\Listeners\Report\ActivityListener@approvedQuote',
            'App\Listeners\NotificationListener@approvedQuote',
            'App\Listeners\QuoteListener@approvedQuote',
        ],

//      sales Payment
        'App\Events\Sale\PaymentWasCreatedEvent' => [
            'App\Listeners\Report\ActivityListener@createdPayment',
            'App\Listeners\Sale\PaymentListener@createdPayment',
            'App\Listeners\Sale\InvoiceListener@createdPayment',
            'App\Listeners\Sale\SendInvoicePaymentNotification@createdPayment',
            'App\Listeners\Report\AnalyticsListener@trackRevenue',
        ],
        'App\Events\Sale\PaymentWasArchivedEvent' => [
            'App\Listeners\Report\ActivityListener@archivedPayment',
        ],
        'App\Events\Sale\PaymentWasDeletedEvent' => [
            'App\Listeners\Report\ActivityListener@deletedPayment',
            'App\Listeners\Sale\InvoiceListener@deletedPayment',
            'App\Listeners\Client\CreditListener@deletedPayment',
            'App\Listeners\Client\PaymentListener@deletedPayment',
        ],
        'App\Events\Sale\PaymentWasRefundedEvent' => [
            'App\Listeners\Report\ActivityListener@refundedPayment',
            'App\Listeners\Sale\InvoiceListener@refundedPayment',
        ],
        'App\Events\Sale\PaymentWasVoidedEvent' => [
            'App\Listeners\Report\ActivityListener@voidedPayment',
            'App\Listeners\Sale\InvoiceListener@voidedPayment',
        ],
        'App\Events\Sale\PaymentFailedEvent' => [
            'App\Listeners\Report\ActivityListener@failedPayment',
            'App\Listeners\Sale\InvoiceListener@failedPayment',
        ],
        'App\Events\Sale\PaymentWasRestoredEvent' => [
            'App\Listeners\Report\ActivityListener@restoredPayment',
            'App\Listeners\Sale\InvoiceListener@restoredPayment',
        ],

//      Credits
        'App\Events\Client\CreditWasCreatedEvent' => [
            'App\Listeners\Report\ActivityListener@createdCredit',
        ],
        'App\Events\Client\CreditWasArchivedEvent' => [
            'App\Listeners\Report\ActivityListener@archivedCredit',
        ],
        'App\Events\Client\CreditWasDeletedEvent' => [
            'App\Listeners\Report\ActivityListener@deletedCredit',
        ],
        'App\Events\Client\CreditWasRestoredEvent' => [
            'App\Listeners\Report\ActivityListener@restoredCredit',
        ],

//      Task events
        'App\Events\Setting\TaskWasCreatedEvent' => [
            'App\Listeners\Report\ActivityListener@createdTask',
            'App\Listeners\Setting\TaskListener@createdTask',
        ],
        'App\Events\Setting\TaskWasUpdatedEvent' => [
            'App\Listeners\Report\ActivityListener@updatedTask',
            'App\Listeners\Setting\TaskListener@updatedTask',
        ],
        'App\Events\Setting\TaskWasRestoredEvent' => [
            'App\Listeners\Report\ActivityListener@restoredTask',
        ],
        'App\Events\Setting\TaskWasArchivedEvent' => [
            'App\Listeners\Report\ActivityListener@archivedTask',
        ],
        'App\Events\Setting\TaskWasDeletedEvent' => [
            'App\Listeners\Report\ActivityListener@deletedTask',
            'App\Listeners\Setting\TaskListener@deletedTask',
            'App\Listeners\Report\HistoryListener@deletedTask',
        ],

//      Expense events
        'App\Events\Expense\ExpenseWasCreatedEvent' => [
            'App\Listeners\Report\ActivityListener@createdExpense',
            'App\Listeners\Expense\ExpenseListener@createdExpense',
        ],
        'App\Events\Expense\ExpenseWasUpdatedEvent' => [
            'App\Listeners\Report\ActivityListener@updatedExpense',
            'App\Listeners\Expense\ExpenseListener@updatedExpense',
        ],
        'App\Events\Expense\ExpenseWasRestoredEvent' => [
            'App\Listeners\Report\ActivityListener@restoredExpense',
        ],
        'App\Events\Expense\ExpenseWasArchivedEvent' => [
            'App\Listeners\Report\ActivityListener@archivedExpense',
        ],
        'App\Events\Expense\ExpenseWasDeletedEvent' => [
            'App\Listeners\Report\ActivityListener@deletedExpense',
            'App\Listeners\Expense\Expense\ExpenseListener@deletedExpense',
            'App\Listeners\Report\HistoryListener@deletedExpense',
        ],

//       Project events
        'App\Events\ProjectWasDeletedEvent' => [
            'App\Listeners\Report\HistoryListener@deletedProject',
        ],

//      Proposal events
        'App\Events\ProposalWasDeletedEvent' => [
            'App\Listeners\Report\HistoryListener@deletedProposal',
        ],

        'Illuminate\Queue\Events\JobExceptionOccurred' => [
            'App\Listeners\Sale\InvoiceListener@jobFailed'
        ],

//      DNS Add A record to Cloudflare
        'App\Events\Setting\SubdomainWasUpdatedEvent' => [
            'App\Listeners\Setting\DNSListener@addDNSRecord'
        ],

//     DNS Remove A record from Cloudflare
        'App\Events\Setting\SubdomainWasRemovedEvent' => [
            'App\Listeners\Setting\DNSListener@removeDNSRecord'
        ],

//       Product events
        'App\Events\Setting\ProductWasCreatedEvent' => [
            'App\Listeners\Setting\ProductListener@createdProduct',
        ],
        'App\Events\Setting\ProductWasUpdatedEvent' => [
            'App\Listeners\Setting\ProductListener@updatedProduct',
        ],
        'App\Events\Setting\ProductWasDeletedEvent' => [
            'App\Listeners\Setting\ProductListener@deletedProduct',
        ],

//      Bills
        'App\Events\Purchase\BillWasCreatedEvent' => [
            'App\Listeners\Report\ActivityListener@createdBill',
            'App\Listeners\Purchase\BillListener@createdBill',
        ],
        'App\Events\Purchase\BillWasUpdatedEvent' => [
            'App\Listeners\Report\ActivityListener@updatedBill',
            'App\Listeners\Purchase\BillListener@updatedBill',
        ],
        'App\Events\Purchase\BillItemsWereCreatedEvent' => [
            'App\Listeners\Purchase\BillItemListener@createdBill',
        ],
        'App\Events\Purchase\BillItemsWereUpdatedEvent' => [
            'App\Listeners\Purchase\BillItemListener@updatedBill',
        ],
        'App\Events\Purchase\BillWasArchivedEvent' => [
            'App\Listeners\Report\ActivityListener@archivedBill',
        ],
        'App\Events\Purchase\BillWasDeletedEvent' => [
            'App\Listeners\Report\ActivityListener@deletedBill',
//            'App\Listeners\Report\HistoryListener@deletedBill',
            'App\Listeners\Purchase\BillItemListener@deletedBill',
        ],
        'App\Events\Purchase\BillWasRestoredEvent' => [
            'App\Listeners\Report\ActivityListener@restoredBill',
        ],
        'App\Events\Purchase\BillWasEmailedEvent' => [
            'App\Listeners\Purchase\BillListener@emailedBill',
            'App\Listeners\Purchase\BillNotificationListener@emailedBill',
        ],
        'App\Events\Purchase\BillInvitationWasEmailedEvent' => [
            'App\Listeners\Report\ActivityListener@emailedBill',
        ],
        'App\Events\Purchase\BillInvitationWasViewedEvent' => [
            'App\Listeners\Report\ActivityListener@viewedBill',
            'App\Listeners\Purchase\SendBillNotification@viewedBill',
            'App\Listeners\Purchase\BillListener@viewedBill',
        ],
//     Bill quote
        'App\Events\Purchase\BillQuoteWasCreatedEvent' => [
            'App\Listeners\Report\ActivityListener@createdBillQuote',
        ],
        'App\Events\Purchase\BillQuoteWasUpdatedEvent' => [
            'App\Listeners\Report\ActivityListener@updatedBillQuote',
        ],
        'App\Events\Purchase\BillQuoteItemsWereCreatedEvent' => [
            'App\Listeners\Purchase\BillItemListener@createdQuote',
        ],
        'App\Events\Purchase\BillQuoteItemsWereUpdatedEvent' => [
            'App\Listeners\Purchase\BillItemListener@updatedQuote',
        ],
        'App\Events\Purchase\BillQuoteWasArchivedEvent' => [
            'App\Listeners\Report\ActivityListener@archivedBillQuote',
        ],
        'App\Events\Purchase\BillQuoteWasDeletedEvent' => [
            'App\Listeners\Report\ActivityListener@deletedBillQuote',
//            'App\Listeners\Report\HistoryListener@deletedQuote',
            'App\Listeners\Purchase\BillItemListener@deletedQuote',
        ],
        'App\Events\Purchase\BillQuoteWasRestoredEvent' => [
            'App\Listeners\Report\ActivityListener@restoredBillQuote',
        ],
        'App\Events\Purchase\BillQuoteWasEmailedEvent' => [
            'App\Listeners\Purchase\BillQuoteListener@emailedQuote',
            'App\Listeners\Purchase\SendBillNotification@emailedQuote',
        ],
        'App\Events\Purchase\BillQuoteInvitationWasEmailedEvent' => [
            'App\Listeners\Report\ActivityListener@emailedBillQuote',
        ],
        'App\Events\Purchase\BillQuoteInvitationWasViewedEvent' => [
            'App\Listeners\Report\ActivityListener@viewedBillQuote',
            'App\Listeners\Purchase\SendBillNotification@viewedQuote',
            'App\Listeners\Purchase\BillQuoteListener@viewedQuote',
        ],
        'App\Events\Purchase\BillQuoteInvitationWasApprovedEvent' => [
            'App\Listeners\Report\ActivityListener@approvedBillQuote',
            'App\Listeners\Purchase\SendBillNotification@approvedQuote',
            'App\Listeners\Purchase\BillQuoteListener@approvedQuote',
        ],
//      bill Payment
        'App\Events\Purchase\BillPaymentWasCreatedEvent' => [
            'App\Listeners\Report\ActivityListener@createdBillPayment',
            'App\Listeners\Purchase\BillPaymentListener@createdBillPayment',
            'App\Listeners\Purchase\BillListener@createdBillPayment',
            'App\Listeners\Purchase\SendBillPaymentNotification@createdBillPayment',
//            'App\Listeners\Report\AnalyticsListener@trackExpense',
        ],
        'App\Events\Purchase\BillPaymentWasArchivedEvent' => [
            'App\Listeners\Report\ActivityListener@archivedBillPayment',
        ],
        'App\Events\Purchase\BillPaymentWasDeletedEvent' => [
            'App\Listeners\Report\ActivityListener@deletedBillPayment',
            'App\Listeners\Purchase\BillListener@deletedBillPayment',
            'App\Listeners\Vendor\VendorCreditListener@deletedBillPayment',
            'App\Listeners\Vendor\BillPaymentListener@deletedBillPayment',
        ],
        'App\Events\Purchase\BillPaymentWasRefundedEvent' => [
            'App\Listeners\Report\ActivityListener@refundedBillPayment',
            'App\Listeners\Purchase\BillListener@refundedBillPayment',
        ],
        'App\Events\Purchase\BillPaymentWasVoidedEvent' => [
            'App\Listeners\Report\ActivityListener@voidedBillPayment',
            'App\Listeners\Purchase\BillListener@voidedBillPayment',
        ],
        'App\Events\Purchase\BillPaymentFailedEvent' => [
            'App\Listeners\Report\ActivityListener@failedBillPayment',
            'App\Listeners\Purchase\BillListener@failedBillPayment',
        ],
        'App\Events\Purchase\BillPaymentWasRestoredEvent' => [
            'App\Listeners\Report\ActivityListener@restoredBillPayment',
            'App\Listeners\Purchase\BillListener@restoredBillPayment',
        ],

//    Vendor  Credits
        'App\Events\Vendor\VendorCreditWasCreatedEvent' => [
            'App\Listeners\Report\ActivityListener@createdVendorCredit',
        ],
        'App\Events\Vendor\VendorCreditWasArchivedEvent' => [
            'App\Listeners\Report\ActivityListener@archivedVendorCredit',
        ],
        'App\Events\Vendor\VendorCreditWasDeletedEvent' => [
            'App\Listeners\Report\ActivityListener@deletedVendorCredit',
        ],
        'App\Events\Vendor\VendorCreditWasRestoredEvent' => [
            'App\Listeners\Report\ActivityListener@restoredVendorCredit',
        ],

    ];

    /**
     * Register any other events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }
}
