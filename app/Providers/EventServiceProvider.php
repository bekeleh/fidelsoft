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
            'App\Listeners\LogSuccessfulLogin',
        ],

        'Illuminate\Auth\Events\Failed' => [
            'App\Listeners\LogFailedLogin',
        ],
//      Clients
        'App\Events\ClientWasCreated' => [
            'App\Listeners\ActivityListener@createdClient',
            'App\Listeners\ClientListener@createdClient',
        ],
        'App\Events\ClientWasArchived' => [
            'App\Listeners\ActivityListener@archivedClient',
        ],
        'App\Events\ClientWasUpdated' => [
            'App\Listeners\ClientListener@updatedClient',
        ],
        'App\Events\ClientWasDeleted' => [
            'App\Listeners\ActivityListener@deletedClient',
            'App\Listeners\ClientListener@deletedClient',
            'App\Listeners\HistoryListener@deletedClient',
        ],
        'App\Events\ClientWasRestored' => [
            'App\Listeners\ActivityListener@restoredClient',
        ],

//     Vendor events
        'App\Events\vendorWasCreated' => [
            'App\Listeners\ActivityListener@createdVendor',
            'App\Listeners\VendorListener@createdVendor',
        ],
        'App\Events\vendorWasArchived' => [
            'App\Listeners\ActivityListener@archivedVendor',
        ],
        'App\Events\vendorWasUpdated' => [
            'App\Listeners\VendorListener@updatedVendor',
        ],
        'App\Events\vendorWasDeleted' => [
            'App\Listeners\ActivityListener@deletedVendor',
            'App\Listeners\VendorListener@deletedVendor',
            'App\Listeners\HistoryListener@deletedVendor',
        ],
        'App\Events\vendorWasRestored' => [
            'App\Listeners\ActivityListener@restoredVendor',
        ],

//       Invoices
        'App\Events\InvoiceWasCreated' => [
            'App\Listeners\ActivityListener@createdInvoice',
            'App\Listeners\InvoiceListener@createdInvoice',
        ],
        'App\Events\InvoiceWasUpdated' => [
            'App\Listeners\ActivityListener@updatedInvoice',
            'App\Listeners\InvoiceListener@updatedInvoice',
        ],
        'App\Events\InvoiceItemsWereCreated' => [
            'App\Listeners\InvoiceItemListener@createdInvoice',
        ],
        'App\Events\InvoiceItemsWereUpdated' => [
            'App\Listeners\InvoiceItemListener@updatedInvoice',
        ],
        'App\Events\InvoiceWasArchived' => [
            'App\Listeners\ActivityListener@archivedInvoice',
        ],
        'App\Events\InvoiceWasDeleted' => [
            'App\Listeners\ActivityListener@deletedInvoice',
            'App\Listeners\TaskListener@deletedInvoice',
            'App\Listeners\ExpenseListener@deletedInvoice',
            'App\Listeners\HistoryListener@deletedInvoice',
            'App\Listeners\InvoiceItemListener@deletedInvoice',
        ],
        'App\Events\InvoiceWasRestored' => [
            'App\Listeners\ActivityListener@restoredInvoice',
        ],
        'App\Events\InvoiceWasEmailed' => [
            'App\Listeners\InvoiceListener@emailedInvoice',
            'App\Listeners\NotificationListener@emailedInvoice',
        ],
        'App\Events\InvoiceInvitationWasEmailed' => [
            'App\Listeners\ActivityListener@emailedInvoice',
        ],
        'App\Events\InvoiceInvitationWasViewed' => [
            'App\Listeners\ActivityListener@viewedInvoice',
            'App\Listeners\NotificationListener@viewedInvoice',
            'App\Listeners\InvoiceListener@viewedInvoice',
        ],

//      Quotes
        'App\Events\QuoteWasCreated' => [
            'App\Listeners\ActivityListener@createdQuote',
        ],
        'App\Events\QuoteWasUpdated' => [
            'App\Listeners\ActivityListener@updatedQuote',
        ],
        'App\Events\QuoteItemsWereCreated' => [
            'App\Listeners\InvoiceItemListener@createdQuote',
        ],
        'App\Events\QuoteItemsWereUpdated' => [
            'App\Listeners\InvoiceItemListener@updatedQuote',
        ],
        'App\Events\QuoteWasArchived' => [
            'App\Listeners\ActivityListener@archivedQuote',
        ],
        'App\Events\QuoteWasDeleted' => [
            'App\Listeners\ActivityListener@deletedQuote',
            'App\Listeners\HistoryListener@deletedQuote',
            'App\Listeners\InvoiceItemListener@deletedQuote',
        ],
        'App\Events\QuoteWasRestored' => [
            'App\Listeners\ActivityListener@restoredQuote',
        ],
        'App\Events\QuoteWasEmailed' => [
            'App\Listeners\QuoteListener@emailedQuote',
            'App\Listeners\NotificationListener@emailedQuote',
        ],
        'App\Events\QuoteInvitationWasEmailed' => [
            'App\Listeners\ActivityListener@emailedQuote',
        ],
        'App\Events\QuoteInvitationWasViewed' => [
            'App\Listeners\ActivityListener@viewedQuote',
            'App\Listeners\NotificationListener@viewedQuote',
            'App\Listeners\QuoteListener@viewedQuote',
        ],
        'App\Events\QuoteInvitationWasApproved' => [
            'App\Listeners\ActivityListener@approvedQuote',
            'App\Listeners\NotificationListener@approvedQuote',
            'App\Listeners\QuoteListener@approvedQuote',
        ],

//      Payments
        'App\Events\PaymentWasCreated' => [
            'App\Listeners\ActivityListener@createdPayment',
            'App\Listeners\PaymentListener@createdPayment',
            'App\Listeners\InvoiceListener@createdPayment',
            'App\Listeners\NotificationListener@createdPayment',
            'App\Listeners\AnalyticsListener@trackRevenue',
        ],
        'App\Events\PaymentWasArchived' => [
            'App\Listeners\ActivityListener@archivedPayment',
        ],
        'App\Events\PaymentWasDeleted' => [
            'App\Listeners\ActivityListener@deletedPayment',
            'App\Listeners\InvoiceListener@deletedPayment',
            'App\Listeners\CreditListener@deletedPayment',
            'App\Listeners\PaymentListener@deletedPayment',
        ],
        'App\Events\PaymentWasRefunded' => [
            'App\Listeners\ActivityListener@refundedPayment',
            'App\Listeners\InvoiceListener@refundedPayment',
        ],
        'App\Events\PaymentWasVoided' => [
            'App\Listeners\ActivityListener@voidedPayment',
            'App\Listeners\InvoiceListener@voidedPayment',
        ],
        'App\Events\PaymentFailed' => [
            'App\Listeners\ActivityListener@failedPayment',
            'App\Listeners\InvoiceListener@failedPayment',
        ],
        'App\Events\PaymentWasRestored' => [
            'App\Listeners\ActivityListener@restoredPayment',
            'App\Listeners\InvoiceListener@restoredPayment',
        ],

//      Credits
        'App\Events\CreditWasCreated' => [
            'App\Listeners\ActivityListener@createdCredit',
        ],
        'App\Events\CreditWasArchived' => [
            'App\Listeners\ActivityListener@archivedCredit',
        ],
        'App\Events\CreditWasDeleted' => [
            'App\Listeners\ActivityListener@deletedCredit',
        ],
        'App\Events\CreditWasRestored' => [
            'App\Listeners\ActivityListener@restoredCredit',
        ],

//       User events
        'App\Events\UserSignedUp' => [
            'App\Listeners\HandleUserSignedUp',
        ],
        'App\Events\UserLoggedIn' => [
            'App\Listeners\HandleUserLoggedIn',
        ],
        'App\Events\UserSettingsChanged' => [
            'App\Listeners\HandleUserSettingsChanged',
        ],

//      Task events
        'App\Events\TaskWasCreated' => [
            'App\Listeners\ActivityListener@createdTask',
            'App\Listeners\TaskListener@createdTask',
        ],
        'App\Events\TaskWasUpdated' => [
            'App\Listeners\ActivityListener@updatedTask',
            'App\Listeners\TaskListener@updatedTask',
        ],
        'App\Events\TaskWasRestored' => [
            'App\Listeners\ActivityListener@restoredTask',
        ],
        'App\Events\TaskWasArchived' => [
            'App\Listeners\ActivityListener@archivedTask',
        ],
        'App\Events\TaskWasDeleted' => [
            'App\Listeners\ActivityListener@deletedTask',
            'App\Listeners\TaskListener@deletedTask',
            'App\Listeners\HistoryListener@deletedTask',
        ],

//      Expense events
        'App\Events\ExpenseWasCreated' => [
            'App\Listeners\ActivityListener@createdExpense',
            'App\Listeners\ExpenseListener@createdExpense',
        ],
        'App\Events\ExpenseWasUpdated' => [
            'App\Listeners\ActivityListener@updatedExpense',
            'App\Listeners\ExpenseListener@updatedExpense',
        ],
        'App\Events\ExpenseWasRestored' => [
            'App\Listeners\ActivityListener@restoredExpense',
        ],
        'App\Events\ExpenseWasArchived' => [
            'App\Listeners\ActivityListener@archivedExpense',
        ],
        'App\Events\ExpenseWasDeleted' => [
            'App\Listeners\ActivityListener@deletedExpense',
            'App\Listeners\ExpenseListener@deletedExpense',
            'App\Listeners\HistoryListener@deletedExpense',
        ],

//       Project events
        'App\Events\ProjectWasDeleted' => [
            'App\Listeners\HistoryListener@deletedProject',
        ],

//      Proposal events
        'App\Events\ProposalWasDeleted' => [
            'App\Listeners\HistoryListener@deletedProposal',
        ],

        'Illuminate\Queue\Events\JobExceptionOccurred' => [
            'App\Listeners\InvoiceListener@jobFailed'
        ],

//      DNS Add A record to Cloudflare
        'App\Events\SubdomainWasUpdated' => [
            'App\Listeners\DNSListener@addDNSRecord'
        ],

//     DNS Remove A record from Cloudflare
        'App\Events\SubdomainWasRemoved' => [
            'App\Listeners\DNSListener@removeDNSRecord'
        ],

//       Product events
        'App\Events\ProductWasCreated' => [
            'App\Listeners\ProductListener@createdProduct',
        ],
        'App\Events\ProductWasUpdated' => [
            'App\Listeners\ProductListener@updatedProduct',
        ],
        'App\Events\ProductWasDeleted' => [
            'App\Listeners\ProductListener@deletedProduct',
        ],
//       User events
        'App\Events\UserWasCreated' => [
            'App\Listeners\UserListener@createdUser',
        ],
        'App\Events\UserWasUpdated' => [
            'App\Listeners\UserListener@updatedUser',
        ],
        'App\Events\UserWasDeleted' => [
            'App\Listeners\UserListener@deletedUser',
        ],
//      Bill Invoices
        'App\Events\BillWasCreated' => [
            'App\Listeners\ActivityListener@createdBill',
            'App\Listeners\BillListener@createdInvoice',
        ],
        'App\Events\BillWasUpdated' => [
            'App\Listeners\ActivityListener@updatedBill',
            'App\Listeners\BillListener@updatedInvoice',
        ],
        'App\Events\BillItemsWereCreated' => [
            'App\Listeners\BillItemListener@createdInvoice',
        ],
        'App\Events\BillItemsWereUpdated' => [
            'App\Listeners\BillItemListener@updatedInvoice',
        ],
        'App\Events\BillWasArchived' => [
            'App\Listeners\ActivityListener@archivedBill',
        ],
        'App\Events\BillWasDeleted' => [
            'App\Listeners\ActivityListener@deletedBill',
//            'App\Listeners\HistoryListener@deletedInvoice',
            'App\Listeners\BillItemListener@deletedInvoice',
        ],
        'App\Events\BillWasRestored' => [
            'App\Listeners\ActivityListener@restoredBill',
        ],
        'App\Events\BillWasEmailed' => [
            'App\Listeners\BillListener@emailedInvoice',
            'App\Listeners\BillNotificationListener@emailedBill',
        ],
        'App\Events\BillInvitationWasEmailed' => [
            'App\Listeners\ActivityListener@emailedBill',
        ],
        'App\Events\BillInvitationWasViewed' => [
            'App\Listeners\ActivityListener@viewedBill',
            'App\Listeners\BillNotificationListener@viewedInvoice',
            'App\Listeners\BillListener@viewedInvoice',
        ],
//     Bill quote
        'App\Events\BillQuoteWasCreated' => [
            'App\Listeners\ActivityListener@createdBillQuote',
        ],
        'App\Events\BillQuoteWasUpdated' => [
            'App\Listeners\ActivityListener@updatedBillQuote',
        ],
        'App\Events\BillQuoteItemsWereCreated' => [
            'App\Listeners\BillItemListener@createdQuote',
        ],
        'App\Events\BillQuoteItemsWereUpdated' => [
            'App\Listeners\BillItemListener@updatedQuote',
        ],
        'App\Events\BillQuoteWasArchived' => [
            'App\Listeners\ActivityListener@archivedBillQuote',
        ],
        'App\Events\BillQuoteWasDeleted' => [
            'App\Listeners\ActivityListener@deletedBillQuote',
//            'App\Listeners\HistoryListener@deletedQuote',
            'App\Listeners\BillItemListener@deletedQuote',
        ],
        'App\Events\BillQuoteWasRestored' => [
            'App\Listeners\ActivityListener@restoredBillQuote',
        ],
        'App\Events\BillQuoteWasEmailed' => [
            'App\Listeners\BillQuoteListener@emailedQuote',
            'App\Listeners\BillNotificationListener@emailedQuote',
        ],
        'App\Events\BillQuoteInvitationWasEmailed' => [
            'App\Listeners\ActivityListener@emailedBillQuote',
        ],
        'App\Events\BillQuoteInvitationWasViewed' => [
            'App\Listeners\ActivityListener@viewedBillQuote',
            'App\Listeners\BillNotificationListener@viewedQuote',
            'App\Listeners\BillQuoteListener@viewedQuote',
        ],
        'App\Events\billQuoteInvitationWasApproved' => [
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
