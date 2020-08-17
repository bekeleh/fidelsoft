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
            'App\Listeners\Auth\UserSuccessfulLogin',
        ],

        'Illuminate\Auth\Events\Failed' => [
            'App\Listeners\Auth\UserFailedLogin',
        ],
//      Clients
        'App\Events\ClientWasCreatedEvent' => [
            'App\Listeners\ActivityListener@createdClient',
            'App\Listeners\Report\AddClients@createdClient',
        ],
        'App\Events\ClientWasArchivedEvent' => [
            'App\Listeners\ActivityListener@archivedClient',
        ],
        'App\Events\ClientWasUpdatedEvent' => [
            'App\Listeners\Report\AddClients@updatedClient',
        ],
        'App\Events\ClientWasDeletedEvent' => [
            'App\Listeners\ActivityListener@deletedClient',
            'App\Listeners\Report\AddClients@deletedClient',
            'App\Listeners\HistoryListener@deletedClient',
        ],
        'App\Events\ClientWasRestoredEvent' => [
            'App\Listeners\ActivityListener@restoredClient',
        ],

//     Vendor events
        'App\Events\VendorWasCreatedEvent' => [
            'App\Listeners\ActivityListener@createdVendor',
            'App\Listeners\Report\AddVendors@createdVendor',
        ],
        'App\Events\VendorWasArchivedEvent' => [
            'App\Listeners\ActivityListener@archivedVendor',
        ],
        'App\Events\VendorWasUpdatedEvent' => [
            'App\Listeners\Report\AddVendors@updatedVendor',
        ],
        'App\Events\VendorWasDeletedEvent' => [
            'App\Listeners\ActivityListener@deletedVendor',
            'App\Listeners\Report\AddVendors@deletedVendor',
            'App\Listeners\HistoryListener@deletedVendor',
        ],
        'App\Events\VendorWasRestoredEvent' => [
            'App\Listeners\ActivityListener@restoredVendor',
        ],

//       Invoices
        'App\Events\InvoiceWasCreatedEvent' => [
            'App\Listeners\ActivityListener@createdInvoice',
            'App\Listeners\InvoiceListener@createdInvoice',
        ],
        'App\Events\InvoiceWasUpdatedEvent' => [
            'App\Listeners\ActivityListener@updatedInvoice',
            'App\Listeners\InvoiceListener@updatedInvoice',
        ],
        'App\Events\InvoiceItemsWereCreatedEvent' => [
            'App\Listeners\InvoiceItemListener@createdInvoice',
        ],
        'App\Events\InvoiceItemsWereUpdatedEvent' => [
            'App\Listeners\InvoiceItemListener@updatedInvoice',
        ],
        'App\Events\InvoiceWasArchivedEvent' => [
            'App\Listeners\ActivityListener@archivedInvoice',
        ],
        'App\Events\InvoiceWasDeletedEvent' => [
            'App\Listeners\ActivityListener@deletedInvoice',
            'App\Listeners\TaskListener@deletedInvoice',
            'App\Listeners\ExpenseListener@deletedInvoice',
            'App\Listeners\HistoryListener@deletedInvoice',
            'App\Listeners\InvoiceItemListener@deletedInvoice',
        ],
        'App\Events\InvoiceWasRestoredEvent' => [
            'App\Listeners\ActivityListener@restoredInvoice',
        ],
        'App\Events\InvoiceWasEmailedEvent' => [
            'App\Listeners\InvoiceListener@emailedInvoice',
            'App\Listeners\NotificationListener@emailedInvoice',
        ],
        'App\Events\InvoiceInvitationWasEmailedEvent' => [
            'App\Listeners\ActivityListener@emailedInvoice',
        ],
        'App\Events\InvoiceInvitationWasViewedEvent' => [
            'App\Listeners\ActivityListener@viewedInvoice',
            'App\Listeners\NotificationListener@viewedInvoice',
            'App\Listeners\InvoiceListener@viewedInvoice',
        ],

//      Quotes
        'App\Events\QuoteWasCreatedEvent' => [
            'App\Listeners\ActivityListener@createdQuote',
        ],
        'App\Events\QuoteWasUpdatedEvent' => [
            'App\Listeners\ActivityListener@updatedQuote',
        ],
        'App\Events\QuoteItemsWereCreatedEvent' => [
            'App\Listeners\InvoiceItemListener@createdQuote',
        ],
        'App\Events\QuoteItemsWereUpdatedEvent' => [
            'App\Listeners\InvoiceItemListener@updatedQuote',
        ],
        'App\Events\QuoteWasArchivedEvent' => [
            'App\Listeners\ActivityListener@archivedQuote',
        ],
        'App\Events\QuoteWasDeletedEvent' => [
            'App\Listeners\ActivityListener@deletedQuote',
            'App\Listeners\HistoryListener@deletedQuote',
            'App\Listeners\InvoiceItemListener@deletedQuote',
        ],
        'App\Events\QuoteWasRestoredEvent' => [
            'App\Listeners\ActivityListener@restoredQuote',
        ],
        'App\Events\QuoteWasEmailedEvent' => [
            'App\Listeners\QuoteListener@emailedQuote',
            'App\Listeners\NotificationListener@emailedQuote',
        ],
        'App\Events\QuoteInvitationWasEmailedEvent' => [
            'App\Listeners\ActivityListener@emailedQuote',
        ],
        'App\Events\QuoteInvitationWasViewedEvent' => [
            'App\Listeners\ActivityListener@viewedQuote',
            'App\Listeners\NotificationListener@viewedQuote',
            'App\Listeners\QuoteListener@viewedQuote',
        ],
        'App\Events\QuoteInvitationWasApprovedEvent' => [
            'App\Listeners\ActivityListener@approvedQuote',
            'App\Listeners\NotificationListener@approvedQuote',
            'App\Listeners\QuoteListener@approvedQuote',
        ],

//      Payments
        'App\Events\PaymentWasCreatedEvent' => [
            'App\Listeners\ActivityListener@createdPayment',
            'App\Listeners\PaymentListener@createdPayment',
            'App\Listeners\InvoiceListener@createdPayment',
            'App\Listeners\NotificationListener@createdPayment',
            'App\Listeners\AnalyticsListener@trackRevenue',
        ],
        'App\Events\PaymentWasArchivedEvent' => [
            'App\Listeners\ActivityListener@archivedPayment',
        ],
        'App\Events\PaymentWasDeletedEvent' => [
            'App\Listeners\ActivityListener@deletedPayment',
            'App\Listeners\InvoiceListener@deletedPayment',
            'App\Listeners\CreditListener@deletedPayment',
            'App\Listeners\PaymentListener@deletedPayment',
        ],
        'App\Events\PaymentWasRefundedEvent' => [
            'App\Listeners\ActivityListener@refundedPayment',
            'App\Listeners\InvoiceListener@refundedPayment',
        ],
        'App\Events\PaymentWasVoidedEvent' => [
            'App\Listeners\ActivityListener@voidedPayment',
            'App\Listeners\InvoiceListener@voidedPayment',
        ],
        'App\Events\PaymentFailedEvent' => [
            'App\Listeners\ActivityListener@failedPayment',
            'App\Listeners\InvoiceListener@failedPayment',
        ],
        'App\Events\PaymentWasRestoredEvent' => [
            'App\Listeners\ActivityListener@restoredPayment',
            'App\Listeners\InvoiceListener@restoredPayment',
        ],

//      Credits
        'App\Events\CreditWasCreatedEvent' => [
            'App\Listeners\ActivityListener@createdCredit',
        ],
        'App\Events\CreditWasArchivedEvent' => [
            'App\Listeners\ActivityListener@archivedCredit',
        ],
        'App\Events\CreditWasDeletedEvent' => [
            'App\Listeners\ActivityListener@deletedCredit',
        ],
        'App\Events\CreditWasRestoredEvent' => [
            'App\Listeners\ActivityListener@restoredCredit',
        ],

//       User events
        'App\Events\UserSignedUpEvent' => [
            'App\Listeners\Auth\UserSignedUp',
        ],
        'App\Events\UserLoggedInEvent' => [
            'App\Listeners\Auth\UserLogged',
        ],
        'App\Events\UserSettingsChangedEvent' => [
            'App\Listeners\Auth\UserSettingsChanged',
        ],

//      Task events
        'App\Events\TaskWasCreatedEvent' => [
            'App\Listeners\ActivityListener@createdTask',
            'App\Listeners\TaskListener@createdTask',
        ],
        'App\Events\TaskWasUpdatedEvent' => [
            'App\Listeners\ActivityListener@updatedTask',
            'App\Listeners\TaskListener@updatedTask',
        ],
        'App\Events\TaskWasRestoredEvent' => [
            'App\Listeners\ActivityListener@restoredTask',
        ],
        'App\Events\TaskWasArchivedEvent' => [
            'App\Listeners\ActivityListener@archivedTask',
        ],
        'App\Events\TaskWasDeletedEvent' => [
            'App\Listeners\ActivityListener@deletedTask',
            'App\Listeners\TaskListener@deletedTask',
            'App\Listeners\HistoryListener@deletedTask',
        ],

//      Expense events
        'App\Events\ExpenseWasCreatedEvent' => [
            'App\Listeners\ActivityListener@createdExpense',
            'App\Listeners\ExpenseListener@createdExpense',
        ],
        'App\Events\ExpenseWasUpdatedEvent' => [
            'App\Listeners\ActivityListener@updatedExpense',
            'App\Listeners\ExpenseListener@updatedExpense',
        ],
        'App\Events\ExpenseWasRestoredEvent' => [
            'App\Listeners\ActivityListener@restoredExpense',
        ],
        'App\Events\ExpenseWasArchivedEvent' => [
            'App\Listeners\ActivityListener@archivedExpense',
        ],
        'App\Events\ExpenseWasDeletedEvent' => [
            'App\Listeners\ActivityListener@deletedExpense',
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
            'App\Listeners\InvoiceListener@jobFailed'
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
            'App\Listeners\ActivityListener@createdBill',
            'App\Listeners\BillListener@createdInvoice',
        ],
        'App\Events\BillWasUpdatedEvent' => [
            'App\Listeners\ActivityListener@updatedBill',
            'App\Listeners\BillListener@updatedInvoice',
        ],
        'App\Events\BillItemsWereCreatedEvent' => [
            'App\Listeners\BillItemListener@createdInvoice',
        ],
        'App\Events\BillItemsWereUpdatedEvent' => [
            'App\Listeners\BillItemListener@updatedInvoice',
        ],
        'App\Events\BillWasArchivedEvent' => [
            'App\Listeners\ActivityListener@archivedBill',
        ],
        'App\Events\BillWasDeletedEvent' => [
            'App\Listeners\ActivityListener@deletedBill',
//            'App\Listeners\HandleHistory@deletedInvoice',
            'App\Listeners\BillItemListener@deletedInvoice',
        ],
        'App\Events\BillWasRestoredEvent' => [
            'App\Listeners\ActivityListener@restoredBill',
        ],
        'App\Events\BillWasEmailedEvent' => [
            'App\Listeners\BillListener@emailedInvoice',
            'App\Listeners\BillNotificationListener@emailedBill',
        ],
        'App\Events\BillInvitationWasEmailedEvent' => [
            'App\Listeners\ActivityListener@emailedBill',
        ],
        'App\Events\BillInvitationWasViewedEvent' => [
            'App\Listeners\ActivityListener@viewedBill',
            'App\Listeners\BillNotificationListener@viewedInvoice',
            'App\Listeners\BillListener@viewedInvoice',
        ],
//     Bill quote
        'App\Events\BillQuoteWasCreatedEvent' => [
            'App\Listeners\ActivityListener@createdBillQuote',
        ],
        'App\Events\BillQuoteWasUpdatedEvent' => [
            'App\Listeners\ActivityListener@updatedBillQuote',
        ],
        'App\Events\BillQuoteItemsWereCreatedEvent' => [
            'App\Listeners\BillItemListener@createdQuote',
        ],
        'App\Events\BillQuoteItemsWereUpdatedEvent' => [
            'App\Listeners\BillItemListener@updatedQuote',
        ],
        'App\Events\BillQuoteWasArchivedEvent' => [
            'App\Listeners\ActivityListener@archivedBillQuote',
        ],
        'App\Events\BillQuoteWasDeletedEvent' => [
            'App\Listeners\ActivityListener@deletedBillQuote',
//            'App\Listeners\HandleHistory@deletedQuote',
            'App\Listeners\BillItemListener@deletedQuote',
        ],
        'App\Events\BillQuoteWasRestoredEvent' => [
            'App\Listeners\ActivityListener@restoredBillQuote',
        ],
        'App\Events\BillQuoteWasEmailedEvent' => [
            'App\Listeners\BillQuoteListener@emailedQuote',
            'App\Listeners\BillNotificationListener@emailedQuote',
        ],
        'App\Events\BillQuoteInvitationWasEmailedEvent' => [
            'App\Listeners\ActivityListener@emailedBillQuote',
        ],
        'App\Events\BillQuoteInvitationWasViewedEvent' => [
            'App\Listeners\ActivityListener@viewedBillQuote',
            'App\Listeners\BillNotificationListener@viewedQuote',
            'App\Listeners\BillQuoteListener@viewedQuote',
        ],
        'App\Events\BillQuoteInvitationWasApprovedEvent' => [
            'App\Listeners\ActivityListener@approvedBillQuote',
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
