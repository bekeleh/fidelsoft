<?php

namespace App\Providers;

use App\Models\AccountGateway;
use App\Models\AccountGatewayToken;
use App\Models\AccountToken;
use App\Models\BankAccount;
use App\Models\Branch;
use App\Models\Client;
use App\Models\ClientType;
use App\Models\Contact;
use App\Models\Credit;
use App\Models\Department;
use App\Models\Document;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Invoice;
use App\Models\ItemBrand;
use App\Models\ItemCategory;
use App\Models\ItemMovement;
use App\Models\ItemRequest;
use App\Models\ItemStore;
use App\Models\ItemTransfer;
use App\Models\Location;
use App\Models\Payment;
use App\Models\PaymentTerm;
use App\Models\PermissionGroup;
use App\Models\Product;
use App\Models\Project;
use App\Models\Proposal;
use App\Models\ProposalCategory;
use App\Models\ProposalSnippet;
use App\Models\ProposalTemplate;
use App\Models\Quote;
use App\Models\RecurringExpense;
use App\Models\Schedule;
use App\Models\ScheduleCategory;
use App\Models\ScheduledReport;
use App\Models\Status;
use App\Models\Store;
use App\Models\Subscription;
use App\Models\Task;
use App\Models\TaxRate;
use App\Models\User;
use App\Models\Vendor;
use App\Policies\AccountGatewayPolicy;
use App\Policies\BankAccountPolicy;
use App\Policies\BranchPolicy;
use App\Policies\ClientPolicy;
use App\Policies\ClientTypePolicy;
use App\Policies\ContactPolicy;
use App\Policies\CreditPolicy;
use App\Policies\CustomerPolicy;
use App\Policies\DepartmentPolicy;
use App\Policies\DocumentPolicy;
use App\Policies\ExpenseCategoryPolicy;
use App\Policies\ExpensePolicy;
use App\Policies\GenericEntityPolicy;
use App\Policies\InvoicePolicy;
use App\Policies\ItemBrandPolicy;
use App\Policies\ItemCategoryPolicy;
use App\Policies\ItemMovementPolicy;
use App\Policies\ItemRequestPolicy;
use App\Policies\ItemStorePolicy;
use App\Policies\ItemTransferPolicy;
use App\Policies\LocationPolicy;
use App\Policies\PaymentPolicy;
use App\Policies\PaymentTermPolicy;
use App\Policies\PermissionGroupPolicy;
use App\Policies\ProductPolicy;
use App\Policies\ProjectPolicy;
use App\Policies\ProposalCategoryPolicy;
use App\Policies\ProposalPolicy;
use App\Policies\ProposalSnippetPolicy;
use App\Policies\ProposalTemplatePolicy;
use App\Policies\QuotePolicy;
use App\Policies\RecurringExpensePolicy;
use App\Policies\ScheduleCategoryPolicy;
use App\Policies\ScheduledReportPolicy;
use App\Policies\SchedulePolicy;
use App\Policies\StatusPolicy;
use App\Policies\StorePolicy;
use App\Policies\SubscriptionPolicy;
use App\Policies\TaskPolicy;
use App\Policies\TaxRatePolicy;
use App\Policies\TokenPolicy;
use App\Policies\UserPolicy;
use App\Policies\VendorPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        Status::class => StatusPolicy::class,
        User::class => UserPolicy::class,
        PermissionGroup::class => PermissionGroupPolicy::class,
        Client::class => ClientPolicy::class,
        ClientType::class => ClientTypePolicy::class,
        Contact::class => ContactPolicy::class,
        Vendor::class => VendorPolicy::class,
        Credit::class => CreditPolicy::class,
        Document::class => DocumentPolicy::class,
        Expense::class => ExpensePolicy::class,
        RecurringExpense::class => RecurringExpensePolicy::class,
        ExpenseCategory::class => ExpenseCategoryPolicy::class,
        Invoice::class => InvoicePolicy::class,
        Quote::class => QuotePolicy::class,
        Payment::class => PaymentPolicy::class,
        Task::class => TaskPolicy::class,
        Product::class => ProductPolicy::class,
        ItemCategory::class => ItemCategoryPolicy::class,
        ItemBrand::class => ItemBrandPolicy::class,
        Location::class => LocationPolicy::class,
        Department::class => DepartmentPolicy::class,
        Branch::class => BranchPolicy::class,
        Store::class => StorePolicy::class,
        ItemStore::class => ItemStorePolicy::class,
        ItemTransfer::class => ItemTransferPolicy::class,
        ItemRequest::class => ItemRequestPolicy::class,
        ItemMovement::class => ItemMovementPolicy::class,
        TaxRate::class => TaxRatePolicy::class,
        AccountGateway::class => AccountGatewayPolicy::class,
        AccountToken::class => TokenPolicy::class,
        Subscription::class => SubscriptionPolicy::class,
        BankAccount::class => BankAccountPolicy::class,
        PaymentTerm::class => PaymentTermPolicy::class,
        Project::class => ProjectPolicy::class,
        AccountGatewayToken::class => CustomerPolicy::class,
        Proposal::class => ProposalPolicy::class,
        ProposalSnippet::class => ProposalSnippetPolicy::class,
        ProposalTemplate::class => ProposalTemplatePolicy::class,
        ProposalCategory::class => ProposalCategoryPolicy::class,
        ScheduleCategory::class => ScheduleCategoryPolicy::class,
        Schedule::class => SchedulePolicy::class,
        ScheduledReport::class => ScheduledReportPolicy::class,

    ];

    /**
     * Register any application authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        foreach (get_class_methods(new GenericEntityPolicy()) as $method) {
            Gate::define($method, "App\Policies\GenericEntityPolicy@{$method}");
        }

        $this->registerPolicies();

    }
}
