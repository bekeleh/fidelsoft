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
        'App\Events\UserWasCreatedEvent' => [
            'App\Listeners\User\UserListener@createdUser',
        ],
        'App\Events\UserWasUpdatedEvent' => [
            'App\Listeners\User\UserListener@updatedUser',
        ],
        'App\Events\UserWasDeletedEvent' => [
            'App\Listeners\User\UserListener@deletedUser',
        ],

//      Clients
        'App\Events\ClientWasCreatedEvent' => [
            'App\Listeners\Report\ActivityListener@createdClient',
            'App\Listeners\Client\ClientListener@createdClient',
        ],
        'App\Events\ClientWasArchivedEvent' => [
            'App\Listeners\Report\ActivityListener@archivedClient',
        ],
        'App\Events\ClientWasUpdatedEvent' => [
            'App\Listeners\Client\ClientListener@updatedClient',
        ],
        'App\Events\ClientWasDeletedEvent' => [
            'App\Listeners\Report\ActivityListener@deletedClient',
            'App\Listeners\Client\ClientListener@deletedClient',
            'App\Listeners\Report\HistoryListener@deletedClient',
        ],
        'App\Events\ClientWasRestoredEvent' => [
            'App\Listeners\Report\ActivityListener@restoredClient',
        ],

//     Vendor events
        'App\Events\VendorWasCreatedEvent' => [
            'App\Listeners\Report\ActivityListener@createdVendor',
            'App\Listeners\Vendor\VendorListener@createdVendor',
        ],
        'App\Events\VendorWasUpdatedEvent' => [
            'App\Listeners\Vendor\VendorListener@updatedVendor',
        ],
        'App\Events\VendorWasArchivedEvent' => [
            'App\Listeners\Report\ActivityListener@archivedVendor',
        ],
        'App\Events\VendorWasDeletedEvent' => [
            'App\Listeners\Report\ActivityListener@deletedVendor',
            'App\Listeners\Vendor\VendorListener@deletedVendor',
            'App\Listeners\Report\HistoryListener@deletedVendor',
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
        'App\Events\InvoiceItemsWereUpdatedEvent' => [
            'App\Listeners\Sale\InvoiceItemListener@updatedInvoice',
        ],
        'App\Events\InvoiceItemsWereCreatedEvent' => [
            'App\Listeners\Sale\InvoiceItemListener@createdInvoice',
        ],
        'App\Events\InvoiceWasArchivedEvent' => [
            'App\Listeners\Report\ActivityListener@archivedInvoice',
        ],
        'App\Events\InvoiceWasDeletedEvent' => [
            'App\Listeners\Report\ActivityListener@deletedInvoice',
            'App\Listeners\Sale\InvoiceListener@deletedInvoice',
            'App\Listeners\Sale\InvoiceItemListener@deletedInvoice',
            'App\Listeners\TaskListener@deletedInvoice',
            'App\Listeners\Expense\ExpenseListener@deletedInvoice',
            'App\Listeners\Report\HistoryListener@deletedInvoice',
        ],
        'App\Events\InvoiceWasRestoredEvent' => [
            'App\Listeners\Report\ActivityListener@restoredInvoice',
        ],
        'App\Events\InvoiceWasEmailedEvent' => [
            'App\Listeners\Sale\InvoiceListener@emailedInvoice',
            'App\Listeners\Sale\SendInvoiceNotification@emailedInvoice',
        ],
        'App\Events\InvoiceInvitationWasEmailedEvent' => [
            'App\Listeners\Report\ActivityListener@emailedInvoice',
        ],
        'App\Events\InvoiceInvitationWasViewedEvent' => [
            'App\Listeners\Report\ActivityListener@viewedInvoice',
            'App\Listeners\Sale\SendInvoiceNotification@viewedInvoice',
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
            'App\Listeners\Sale\InvoiceItemListener@createdQuote',
        ],
        'App\Events\QuoteItemsWereUpdatedEvent' => [
            'App\Listeners\Sale\InvoiceItemListener@updatedQuote',
        ],
        'App\Events\QuoteWasArchivedEvent' => [
            'App\Listeners\Report\ActivityListener@archivedQuote',
        ],
        'App\Events\QuoteWasDeletedEvent' => [
            'App\Listeners\Report\ActivityListener@deletedQuote',
            'App\Listeners\Report\HistoryListener@deletedQuote',
            'App\Listeners\Sale\InvoiceItemListener@deletedQuote',
        ],
        'App\Events\QuoteWasRestoredEvent' => [
            'App\Listeners\Report\ActivityListener@restoredQuote',
        ],
        'App\Events\QuoteWasEmailedEvent' => [
            'App\Listeners\Sale\QuoteListener@emailedQuote',
            'App\Listeners\Sale\SendInvoiceNotification@emailedQuote',
        ],
        'App\Events\QuoteInvitationWasEmailedEvent' => [
            'App\Listeners\Report\ActivityListener@emailedQuote',
        ],
//      quote viewed
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
        'App\Events\Auth\UserSettingsChangedEvent' => [
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
            'App\Listeners\Report\HistoryListener@deletedTask',
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

//      Bill Invoices
        'App\Events\BillWasCreatedEvent' => [
            'App\Listeners\Report\ActivityListener@createdBill',
            'App\Listeners\Purchase\BillListener@createdInvoice',
        ],
        'App\Events\BillWasUpdatedEvent' => [
            'App\Listeners\Report\ActivityListener@updatedBill',
            'App\Listeners\Purchase\BillListener@updatedInvoice',
        ],
        'App\Events\BillItemsWereCreatedEvent' => [
            'App\Listeners\Purchase\BillItemListener@createdInvoice',
        ],
        'App\Events\BillItemsWereUpdatedEvent' => [
            'App\Listeners\Purchase\BillItemListener@updatedInvoice',
        ],
        'App\Events\BillWasArchivedEvent' => [
            'App\Listeners\Report\ActivityListener@archivedBill',
        ],
        'App\Events\BillWasDeletedEvent' => [
            'App\Listeners\Report\ActivityListener@deletedBill',
//            'App\Listeners\Report\HistoryListener@deletedInvoice',
            'App\Listeners\Purchase\BillItemListener@deletedInvoice',
        ],
        'App\Events\BillWasRestoredEvent' => [
            'App\Listeners\Report\ActivityListener@restoredBill',
        ],
        'App\Events\BillWasEmailedEvent' => [
            'App\Listeners\Purchase\BillListener@emailedInvoice',
            'App\Listeners\Purchase\BillNotificationListener@emailedBill',
        ],
        'App\Events\BillInvitationWasEmailedEvent' => [
            'App\Listeners\Report\ActivityListener@emailedBill',
        ],
        'App\Events\BillInvitationWasViewedEvent' => [
            'App\Listeners\Report\ActivityListener@viewedBill',
            'App\Listeners\Purchase\BillNotificationListener@viewedInvoice',
            'App\Listeners\Purchase\BillListener@viewedInvoice',
        ],
//     Bill quote
        'App\Events\BillQuoteWasCreatedEvent' => [
            'App\Listeners\Report\ActivityListener@createdBillQuote',
        ],
        'App\Events\BillQuoteWasUpdatedEvent' => [
            'App\Listeners\Report\ActivityListener@updatedBillQuote',
        ],
        'App\Events\BillQuoteItemsWereCreatedEvent' => [
            'App\Listeners\Purchase\BillItemListener@createdQuote',
        ],
        'App\Events\BillQuoteItemsWereUpdatedEvent' => [
            'App\Listeners\Purchase\BillItemListener@updatedQuote',
        ],
        'App\Events\BillQuoteWasArchivedEvent' => [
            'App\Listeners\Report\ActivityListener@archivedBillQuote',
        ],
        'App\Events\BillQuoteWasDeletedEvent' => [
            'App\Listeners\Report\ActivityListener@deletedBillQuote',
//            'App\Listeners\Report\HistoryListener@deletedQuote',
            'App\Listeners\Purchase\BillItemListener@deletedQuote',
        ],
        'App\Events\BillQuoteWasRestoredEvent' => [
            'App\Listeners\Report\ActivityListener@restoredBillQuote',
        ],
        'App\Events\BillQuoteWasEmailedEvent' => [
            'App\Listeners\Purchase\BillQuoteListener@emailedQuote',
            'App\Listeners\Purchase\BillNotificationListener@emailedQuote',
        ],
        'App\Events\BillQuoteInvitationWasEmailedEvent' => [
            'App\Listeners\Report\ActivityListener@emailedBillQuote',
        ],
        'App\Events\BillQuoteInvitationWasViewedEvent' => [
            'App\Listeners\Report\ActivityListener@viewedBillQuote',
            'App\Listeners\Purchase\BillNotificationListener@viewedQuote',
            'App\Listeners\Purchase\BillQuoteListener@viewedQuote',
        ],
        'App\Events\BillQuoteInvitationWasApprovedEvent' => [
            'App\Listeners\Report\ActivityListener@approvedBillQuote',
            'App\Listeners\Purchase\BillNotificationListener@approvedQuote',
            'App\Listeners\Purchase\BillQuoteListener@approvedQuote',
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
