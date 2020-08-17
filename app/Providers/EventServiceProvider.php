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

//      Clients
        'App\Events\ClientWasCreatedEvent' => [
            'App\Listeners\Report\ActivityListener@createdClient',
            'App\Listeners\Report\ClientListener@createdClient',
        ],
        'App\Events\ClientWasArchivedEvent' => [
            'App\Listeners\Report\ActivityListener@archivedClient',
        ],
        'App\Events\ClientWasUpdatedEvent' => [
            'App\Listeners\Report\ClientListener@updatedClient',
        ],
        'App\Events\ClientWasDeletedEvent' => [
            'App\Listeners\Report\ActivityListener@deletedClient',
            'App\Listeners\Report\ClientListener@deletedClient',
            'App\Listeners\HistoryListener@deletedClient',
        ],
        'App\Events\ClientWasRestoredEvent' => [
            'App\Listeners\Report\ActivityListener@restoredClient',
        ],

//     Vendor events
        'App\Events\VendorWasCreatedEvent' => [
            'App\Listeners\Report\ActivityListener@createdVendor',
            'App\Listeners\Report\VendorListener@createdVendor',
        ],
        'App\Events\VendorWasArchivedEvent' => [
            'App\Listeners\Report\ActivityListener@archivedVendor',
        ],
        'App\Events\VendorWasUpdatedEvent' => [
            'App\Listeners\Report\VendorListener@updatedVendor',
        ],
        'App\Events\VendorWasDeletedEvent' => [
            'App\Listeners\Report\ActivityListener@deletedVendor',
            'App\Listeners\Report\VendorListener@deletedVendor',
            'App\Listeners\HistoryListener@deletedVendor',
        ],
        'App\Events\VendorWasRestoredEvent' => [
            'App\Listeners\Report\ActivityListener@restoredVendor',
        ],

//       Invoices
        'App\Events\InvoiceWasCreatedEvent' => [
            'App\Listeners\Report\ActivityListener@createdInvoice',
            'App\Listeners\Sale\InvoiceListener@createdInvoice',
        ],
        'App\Events\InvoiceWasUpdatedEvent' => [
            'App\Listeners\Report\ActivityListener@updatedInvoice',
            'App\Listeners\Sale\InvoiceListener@updatedInvoice',
        ],
        'App\Events\InvoiceItemsWereCreatedEvent' => [
            'App\Listeners\InvoiceItemListener@createdInvoice',
        ],
        'App\Events\InvoiceItemsWereUpdatedEvent' => [
            'App\Listeners\InvoiceItemListener@updatedInvoice',
        ],
        'App\Events\InvoiceWasArchivedEvent' => [
            'App\Listeners\Report\ActivityListener@archivedInvoice',
        ],
        'App\Events\InvoiceWasDeletedEvent' => [
            'App\Listeners\Report\ActivityListener@deletedInvoice',
            'App\Listeners\Sale\InvoiceListener@deletedInvoice',
            'App\Listeners\TaskListener@deletedInvoice',
            'App\Listeners\ExpenseListener@deletedInvoice',
            'App\Listeners\HistoryListener@deletedInvoice',
            'App\Listeners\InvoiceItemListener@deletedInvoice',
        ],
        'App\Events\InvoiceWasRestoredEvent' => [
            'App\Listeners\Report\ActivityListener@restoredInvoice',
        ],
        'App\Events\InvoiceWasEmailedEvent' => [
            'App\Listeners\Sale\InvoiceListener@emailedInvoice',
            'App\Listeners\NotificationListener@emailedInvoice',
        ],
        'App\Events\InvoiceInvitationWasEmailedEvent' => [
            'App\Listeners\Report\ActivityListener@emailedInvoice',
        ],
        'App\Events\InvoiceInvitationWasViewedEvent' => [
            'App\Listeners\Report\ActivityListener@viewedInvoice',
            'App\Listeners\NotificationListener@viewedInvoice',
            'App\Listeners\Sale\InvoiceListener@viewedInvoice',
        ],

//      Quotes
        'App\Events\QuoteWasCreatedEvent' => [
            'App\Listeners\Report\ActivityListener@createdQuote',
        ],
        'App\Events\QuoteWasUpdatedEvent' => [
            'App\Listeners\Report\ActivityListener@updatedQuote',
        ],
        'App\Events\QuoteItemsWereCreatedEvent' => [
            'App\Listeners\InvoiceItemListener@createdQuote',
        ],
        'App\Events\QuoteItemsWereUpdatedEvent' => [
            'App\Listeners\InvoiceItemListener@updatedQuote',
        ],
        'App\Events\QuoteWasArchivedEvent' => [
            'App\Listeners\Report\ActivityListener@archivedQuote',
        ],
        'App\Events\QuoteWasDeletedEvent' => [
            'App\Listeners\Report\ActivityListener@deletedQuote',
            'App\Listeners\HistoryListener@deletedQuote',
            'App\Listeners\InvoiceItemListener@deletedQuote',
        ],
        'App\Events\QuoteWasRestoredEvent' => [
            'App\Listeners\Report\ActivityListener@restoredQuote',
        ],
        'App\Events\QuoteWasEmailedEvent' => [
            'App\Listeners\QuoteListener@emailedQuote',
            'App\Listeners\NotificationListener@emailedQuote',
        ],
        'App\Events\QuoteInvitationWasEmailedEvent' => [
            'App\Listeners\Report\ActivityListener@emailedQuote',
        ],
        'App\Events\QuoteInvitationWasViewedEvent' => [
            'App\Listeners\Report\ActivityListener@viewedQuote',
            'App\Listeners\NotificationListener@viewedQuote',
            'App\Listeners\QuoteListener@viewedQuote',
        ],
        'App\Events\QuoteInvitationWasApprovedEvent' => [
            'App\Listeners\Report\ActivityListener@approvedQuote',
            'App\Listeners\NotificationListener@approvedQuote',
            'App\Listeners\QuoteListener@approvedQuote',
        ],

//      Payments
        'App\Events\PaymentWasCreatedEvent' => [
            'App\Listeners\Report\ActivityListener@createdPayment',
            'App\Listeners\PaymentListener@createdPayment',
            'App\Listeners\Sale\InvoiceListener@createdPayment',
            'App\Listeners\NotificationListener@createdPayment',
            'App\Listeners\AnalyticsListener@trackRevenue',
        ],
        'App\Events\PaymentWasArchivedEvent' => [
            'App\Listeners\Report\ActivityListener@archivedPayment',
        ],
        'App\Events\PaymentWasDeletedEvent' => [
            'App\Listeners\Report\ActivityListener@deletedPayment',
            'App\Listeners\Sale\InvoiceListener@deletedPayment',
            'App\Listeners\CreditListener@deletedPayment',
            'App\Listeners\PaymentListener@deletedPayment',
        ],
        'App\Events\PaymentWasRefundedEvent' => [
            'App\Listeners\Report\ActivityListener@refundedPayment',
            'App\Listeners\Sale\InvoiceListener@refundedPayment',
        ],
        'App\Events\PaymentWasVoidedEvent' => [
            'App\Listeners\Report\ActivityListener@voidedPayment',
            'App\Listeners\Sale\InvoiceListener@voidedPayment',
        ],
        'App\Events\PaymentFailedEvent' => [
            'App\Listeners\Report\ActivityListener@failedPayment',
            'App\Listeners\Sale\InvoiceListener@failedPayment',
        ],
        'App\Events\PaymentWasRestoredEvent' => [
            'App\Listeners\Report\ActivityListener@restoredPayment',
            'App\Listeners\Sale\InvoiceListener@restoredPayment',
        ],

//      Credits
        'App\Events\CreditWasCreatedEvent' => [
            'App\Listeners\Report\ActivityListener@createdCredit',
        ],
        'App\Events\CreditWasArchivedEvent' => [
            'App\Listeners\Report\ActivityListener@archivedCredit',
        ],
        'App\Events\CreditWasDeletedEvent' => [
            'App\Listeners\Report\ActivityListener@deletedCredit',
        ],
        'App\Events\CreditWasRestoredEvent' => [
            'App\Listeners\Report\ActivityListener@restoredCredit',
        ],

//       User events
        'App\Events\UserSignedUpEvent' => [
            'App\Listeners\Auth\UserSignedUpListener',
        ],
        'App\Events\UserLoggedInEvent' => [
            'App\Listeners\Auth\UserLoggedListener',
        ],
        'App\Events\UserSettingsChangedEvent' => [
            'App\Listeners\Auth\UserSettingsChangedListener',
        ],

//      Task events
        'App\Events\TaskWasCreatedEvent' => [
            'App\Listeners\Report\ActivityListener@createdTask',
            'App\Listeners\TaskListener@createdTask',
        ],
        'App\Events\TaskWasUpdatedEvent' => [
            'App\Listeners\Report\ActivityListener@updatedTask',
            'App\Listeners\TaskListener@updatedTask',
        ],
        'App\Events\TaskWasRestoredEvent' => [
            'App\Listeners\Report\ActivityListener@restoredTask',
        ],
        'App\Events\TaskWasArchivedEvent' => [
            'App\Listeners\Report\ActivityListener@archivedTask',
        ],
        'App\Events\TaskWasDeletedEvent' => [
            'App\Listeners\Report\ActivityListener@deletedTask',
            'App\Listeners\TaskListener@deletedTask',
            'App\Listeners\HistoryListener@deletedTask',
        ],

//      Expense events
        'App\Events\ExpenseWasCreatedEvent' => [
            'App\Listeners\Report\ActivityListener@createdExpense',
            'App\Listeners\ExpenseListener@createdExpense',
        ],
        'App\Events\ExpenseWasUpdatedEvent' => [
            'App\Listeners\Report\ActivityListener@updatedExpense',
            'App\Listeners\ExpenseListener@updatedExpense',
        ],
        'App\Events\ExpenseWasRestoredEvent' => [
            'App\Listeners\Report\ActivityListener@restoredExpense',
        ],
        'App\Events\ExpenseWasArchivedEvent' => [
            'App\Listeners\Report\ActivityListener@archivedExpense',
        ],
        'App\Events\ExpenseWasDeletedEvent' => [
            'App\Listeners\Report\ActivityListener@deletedExpense',
            'App\Listeners\ExpenseListener@deletedExpense',
            'App\Listeners\HistoryListener@deletedExpense',
        ],

//       Project events
        'App\Events\ProjectWasDeletedEvent' => [
            'App\Listeners\HistoryListener@deletedProject',
        ],

//      Proposal events
        'App\Events\ProposalWasDeletedEvent' => [
            'App\Listeners\HistoryListener@deletedProposal',
        ],

        'Illuminate\Queue\Events\JobExceptionOccurred' => [
            'App\Listeners\Sale\InvoiceListener@jobFailed'
        ],

//      DNS Add A record to Cloudflare
        'App\Events\SubdomainWasUpdatedEvent' => [
            'App\Listeners\DNSListener@addDNSRecord'
        ],

//     DNS Remove A record from Cloudflare
        'App\Events\SubdomainWasRemovedEvent' => [
            'App\Listeners\DNSListener@removeDNSRecord'
        ],

//       Product events
        'App\Events\ProductWasCreatedEvent' => [
            'App\Listeners\ProductListener@createdProduct',
        ],
        'App\Events\ProductWasUpdatedEvent' => [
            'App\Listeners\ProductListener@updatedProduct',
        ],
        'App\Events\ProductWasDeletedEvent' => [
            'App\Listeners\ProductListener@deletedProduct',
        ],
//       User events
        'App\Events\UserWasCreatedEvent' => [
            'App\Listeners\Report\AddUsers@createdUser',
        ],
        'App\Events\UserWasUpdatedEvent' => [
            'App\Listeners\Report\AddUsers@updatedUser',
        ],
        'App\Events\UserWasDeletedEvent' => [
            'App\Listeners\Report\AddUsers@deletedUser',
        ],
//      Bill Invoices
        'App\Events\BillWasCreatedEvent' => [
            'App\Listeners\Report\ActivityListener@createdBill',
            'App\Listeners\BillListener@createdInvoice',
        ],
        'App\Events\BillWasUpdatedEvent' => [
            'App\Listeners\Report\ActivityListener@updatedBill',
            'App\Listeners\BillListener@updatedInvoice',
        ],
        'App\Events\BillItemsWereCreatedEvent' => [
            'App\Listeners\BillItemListener@createdInvoice',
        ],
        'App\Events\BillItemsWereUpdatedEvent' => [
            'App\Listeners\BillItemListener@updatedInvoice',
        ],
        'App\Events\BillWasArchivedEvent' => [
            'App\Listeners\Report\ActivityListener@archivedBill',
        ],
        'App\Events\BillWasDeletedEvent' => [
            'App\Listeners\Report\ActivityListener@deletedBill',
//            'App\Listeners\HistoryListener@deletedInvoice',
            'App\Listeners\BillItemListener@deletedInvoice',
        ],
        'App\Events\BillWasRestoredEvent' => [
            'App\Listeners\Report\ActivityListener@restoredBill',
        ],
        'App\Events\BillWasEmailedEvent' => [
            'App\Listeners\BillListener@emailedInvoice',
            'App\Listeners\BillNotificationListener@emailedBill',
        ],
        'App\Events\BillInvitationWasEmailedEvent' => [
            'App\Listeners\Report\ActivityListener@emailedBill',
        ],
        'App\Events\BillInvitationWasViewedEvent' => [
            'App\Listeners\Report\ActivityListener@viewedBill',
            'App\Listeners\BillNotificationListener@viewedInvoice',
            'App\Listeners\BillListener@viewedInvoice',
        ],
//     Bill quote
        'App\Events\BillQuoteWasCreatedEvent' => [
            'App\Listeners\Report\ActivityListener@createdBillQuote',
        ],
        'App\Events\BillQuoteWasUpdatedEvent' => [
            'App\Listeners\Report\ActivityListener@updatedBillQuote',
        ],
        'App\Events\BillQuoteItemsWereCreatedEvent' => [
            'App\Listeners\BillItemListener@createdQuote',
        ],
        'App\Events\BillQuoteItemsWereUpdatedEvent' => [
            'App\Listeners\BillItemListener@updatedQuote',
        ],
        'App\Events\BillQuoteWasArchivedEvent' => [
            'App\Listeners\Report\ActivityListener@archivedBillQuote',
        ],
        'App\Events\BillQuoteWasDeletedEvent' => [
            'App\Listeners\Report\ActivityListener@deletedBillQuote',
//            'App\Listeners\HistoryListener@deletedQuote',
            'App\Listeners\BillItemListener@deletedQuote',
        ],
        'App\Events\BillQuoteWasRestoredEvent' => [
            'App\Listeners\Report\ActivityListener@restoredBillQuote',
        ],
        'App\Events\BillQuoteWasEmailedEvent' => [
            'App\Listeners\BillQuoteListener@emailedQuote',
            'App\Listeners\BillNotificationListener@emailedQuote',
        ],
        'App\Events\BillQuoteInvitationWasEmailedEvent' => [
            'App\Listeners\Report\ActivityListener@emailedBillQuote',
        ],
        'App\Events\BillQuoteInvitationWasViewedEvent' => [
            'App\Listeners\Report\ActivityListener@viewedBillQuote',
            'App\Listeners\BillNotificationListener@viewedQuote',
            'App\Listeners\BillQuoteListener@viewedQuote',
        ],
        'App\Events\BillQuoteInvitationWasApprovedEvent' => [
            'App\Listeners\Report\ActivityListener@approvedBillQuote',
            'App\Listeners\BillNotificationListener@approvedQuote',
            'App\Listeners\BillQuoteListener@approvedQuote',
        ],
//     Bill invoice payment and credit and others

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
