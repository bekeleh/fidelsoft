<?php

// Application setup
Route::get('/setup', 'AppController@showSetup');
Route::post('/setup', 'AppController@doSetup');
Route::get('/install', 'AppController@install');
Route::get('/update', 'AppController@update');

// Public pages
Route::get('/', 'HomeController@showIndex');
Route::get('/log_error', 'HomeController@logError');
Route::get('/invoice_now', 'HomeController@invoiceNow');
Route::get('/keep_alive', 'HomeController@keepAlive');
Route::post('/get_started', 'AccountController@getStarted');

// Client auth
Route::get('/client/login', ['as' => 'login', 'uses' => 'ClientAuth\LoginController@showLoginForm']);
Route::get('/client/logout', ['as' => 'logout', 'uses' => 'ClientAuth\LoginController@getLogoutWrapper']);
Route::get('/client/session_expired', ['as' => 'logout', 'uses' => 'ClientAuth\LoginController@getSessionExpired']);
Route::get('/client/recover_password', ['as' => 'forgot', 'uses' => 'ClientAuth\ForgotPasswordController@showLinkRequestForm']);
Route::get('/client/password/reset/{token}', ['as' => 'forgot', 'uses' => 'ClientAuth\ResetPasswordController@showResetForm']);

Route::group(['middleware' => ['lookup:contact']], function () {
    Route::post('/client/login', ['as' => 'login', 'uses' => 'ClientAuth\LoginController@login']);
    Route::post('/client/recover_password', ['as' => 'forgot', 'uses' => 'ClientAuth\ForgotPasswordController@sendResetLinkEmail']);
    Route::post('/client/password/reset', ['as' => 'forgot', 'uses' => 'ClientAuth\ResetPasswordController@reset']);
    Route::get('/proposal/image/{account_key}/{document_key}/{filename?}', 'ClientPortalProposalController@getProposalImage');
});

// Client visible pages
Route::group(['middleware' => ['lookup:contact', 'auth:client','banned:client']], function () {
    Route::get('view/{invitation_key}', 'ClientPortalController@viewInvoice');
    Route::get('proposal/{proposal_invitation_key}/download', 'ClientPortalProposalController@downloadProposal');
    Route::get('proposal/{proposal_invitation_key}', 'ClientPortalProposalController@viewProposal');
    Route::get('download/{invitation_key}', 'ClientPortalController@download');
    Route::put('authorize/{invitation_key}', 'ClientPortalController@authorizeInvoice');
    Route::get('view', 'HomeController@viewLogo');
    Route::get('approve/{invitation_key}', 'QuoteController@approve');
    Route::get('payment/{invitation_key}/{gateway_type?}/{source_id?}', 'OnlinePaymentController@showPayment');
    Route::post('payment/{invitation_key}/{gateway_type?}', 'OnlinePaymentController@doPayment');
    Route::get('complete_source/{invitation_key}/{gateway_type}', 'OnlinePaymentController@completeSource');
    Route::match(['GET', 'POST'], 'complete/{invitation_key?}/{gateway_type?}', 'OnlinePaymentController@offsitePayment');
    Route::get('bank/{routing_number}', 'OnlinePaymentController@getBankInfo');
    Route::get('client/payment_methods', 'ClientPortalController@paymentMethods');
    Route::get('client/statement/{client_id?}', 'ClientPortalController@statement');
    Route::post('client/payment_methods/verify', 'ClientPortalController@verifyPaymentMethod');
    Route::post('client/payment_methods/default', 'ClientPortalController@setDefaultPaymentMethod');
    Route::post('client/payment_methods/{source_id}/remove', 'ClientPortalController@removePaymentMethod');
    Route::get('client/details', 'ClientPortalController@showDetails');
    Route::post('client/details', 'ClientPortalController@updateDetails');
    Route::get('client/quotes', 'ClientPortalController@quoteIndex');
    Route::get('client/credits', 'ClientPortalController@creditIndex');
    Route::get('client/invoices', 'ClientPortalController@invoiceIndex');
    Route::get('client/invoices/recurring', 'ClientPortalController@recurringInvoiceIndex');
    Route::post('client/invoices/auto_bill', 'ClientPortalController@setAutoBill');
    Route::get('client/documents', 'ClientPortalController@documentIndex');
    Route::get('client/payments', 'ClientPortalController@paymentIndex');
    Route::get('client/tasks', 'ClientPortalController@taskIndex');
    Route::get('client/dashboard/{contact_key?}', 'ClientPortalController@dashboard');
    Route::get('client/documents/js/{documents}/{filename}', 'ClientPortalController@getDocumentVFSJS');
    Route::get('client/documents/{invitation_key}/{documents}/{filename?}', 'ClientPortalController@getDocument');
    Route::get('client/documents/{invitation_key}/{filename?}', 'ClientPortalController@getInvoiceDocumentsZip');
    Route::get('client/{contact_key?}', 'ClientPortalController@dashboard');

    Route::get('api/client.quotes', ['as' => 'api.client.quotes', 'uses' => 'ClientPortalController@quoteDatatable']);
    Route::get('api/client.credits', ['as' => 'api.client.credits', 'uses' => 'ClientPortalController@creditDatatable']);
    Route::get('api/client.invoices', ['as' => 'api.client.invoices', 'uses' => 'ClientPortalController@invoiceDatatable']);
    Route::get('api/client.recurring_invoices', ['as' => 'api.client.recurring_invoices', 'uses' => 'ClientPortalController@recurringInvoiceDatatable']);
    Route::get('api/client.documents', ['as' => 'api.client.documents', 'uses' => 'ClientPortalController@documentDatatable']);
    Route::get('api/client.payments', ['as' => 'api.client.payments', 'uses' => 'ClientPortalController@paymentDatatable']);
    Route::get('api/client.tasks', ['as' => 'api.client.tasks', 'uses' => 'ClientPortalController@taskDatatable']);
    Route::get('api/client.activity', ['as' => 'api.client.activity', 'uses' => 'ClientPortalController@activityDatatable']);
});

Route::group(['middleware' => 'lookup:license'], function () {
    Route::get('license', 'NinjaController@show_license_payment');
    Route::post('license', 'NinjaController@do_license_payment');
    Route::get('claim_license', 'NinjaController@claim_license');
    if (Utils::isNinja()) {
        Route::post('/signup/register', 'AccountController@doRegister');
        Route::get('/news_feed/{user_type}/{version}/', 'HomeController@newsFeed');
    }
});

Route::group(['middleware' => 'lookup:postmark'], function () {
    Route::post('/hook/email_bounced', 'AppController@emailBounced');
    Route::post('/hook/email_opened', 'AppController@emailOpened');
});

Route::group(['middleware' => 'lookup:account'], function () {
    Route::post('/payment_hook/{account_key}/{gateway_id}', 'OnlinePaymentController@handlePaymentWebhook');
    Route::match(['GET', 'POST', 'OPTIONS'], '/buy_now/{gateway_type?}', 'OnlinePaymentController@handleBuyNow');
    Route::get('validate_two_factor/{account_key}', 'Auth\LoginController@getValidateToken');
    Route::post('validate_two_factor/{account_key}', ['middleware' => 'throttle:5', 'uses' => 'Auth\LoginController@postValidateToken']);
    Route::get('.well-known/apple-developer-merchantid-domain-association', 'OnlinePaymentController@showAppleMerchantId');
});

//Route::post('/hook/bot/{platform?}', 'BotController@handleMessage');

// Laravel auth routes
Route::get('/logout', ['as' => 'logout', 'uses' => 'Auth\LoginController@getLogoutWrapper']);


Route::group(['middleware' => 'guest'], function () {
    Route::get('/', 'HomeController@home')->name('home');
//    Route::get('/', 'DashboardController@index')->name('dashboard');
//    Route::get('password/reset', 'UserController@showLinkRequestForm')->name('password.request');
//    Route::post('password/email', 'UserController@sendResetLinkEmail')->name('password.email');
//    Route::get('password/reset/{token}', 'UserController@showResetForm')->name('password.reset');
//    Route::post('password/reset/{token}', 'UserController@reset')->name('password.reset');
    
    Route::get('/login', ['as' => 'login', 'uses' => 'Auth\LoginController@getLoginWrapper'])->name('login');
    Route::get('/recover_password', ['as' => 'forgot', 'uses' => 'Auth\ForgotPasswordController@showLinkRequestForm'])->name('password.email');
    Route::get('/password/reset/{token}', ['as' => 'forgot', 'uses' => 'Auth\ResetPasswordController@showResetForm'])->name('password.reset');
    Route::get('/auth/{provider}', 'Auth\AuthController@oauthLogin');
});

Route::group(['middleware' => ['lookup:user']], function () {
    Route::get('/user/confirm/{confirmation_code}', 'UserController@confirm');
    Route::post('/login', ['as' => 'login', 'uses' => 'Auth\LoginController@postLoginWrapper']);
    Route::post('/recover_password', ['as' => 'forgot', 'uses' => 'Auth\ForgotPasswordController@sendResetLinkEmail']);
    Route::post('/password/reset', ['as' => 'forgot', 'uses' => 'Auth\ResetPasswordController@reset']);
});

if (Utils::isSelfHost()) {
    Route::get('/run_command', 'AppController@runCommand');
}

if (Utils::isReseller()) {
    Route::post('/reseller_stats', 'AppController@stats');
}

if (Utils::isTravis()) {
    Route::get('/check_data', 'AppController@checkData');
}

Route::group(['middleware' => ['lookup:user', 'auth:user','banned:user']], function () {
    Route::get('force_reset_password/{public_key}', 'Auth\ForceResetPasswordController@showUserForPasswordReset');
    Route::post('force_reset_password/force_reset_password', 'Auth\ForceResetPasswordController@changePassword');
    Route::get('logged_in', 'HomeController@loggedIn');
    Route::get('dashboard', 'DashboardController@index')->name('dashboard');
    Route::get('dashboard_chart_data/{group_by}/{start_date}/{end_date}/{currency_id}/{include_expenses}', 'DashboardController@chartData');
    Route::get('set_entity_filter/{entity_type}/{filter?}', 'AccountController@setEntityFilter');
    Route::get('hide_message', 'HomeController@hideMessage');
    Route::get('force_inline_pdf', 'UserController@forcePDFJS');
    Route::get('account/get_search_data', ['as' => 'get_search_data', 'uses' => 'AccountController@getSearchData']);
    Route::get('check_invoice_number/{invoice_id?}', 'InvoiceController@checkInvoiceNumber');
    Route::post('save_sidebar_state', 'UserController@saveSidebarState');
    Route::post('contact_us', 'HomeController@contactUs');
    Route::post('handle_command', 'BotController@handleCommand');
    Route::post('accept_terms', 'UserController@acceptTerms');

    Route::post('signup/validate', 'AccountController@checkEmail');
    Route::post('signup/submit', 'AccountController@submitSignup');
    Route::get('auth_unlink', 'Auth\AuthController@oauthUnlink');

    Route::get('settings/user_details', 'AccountController@showUserDetails');
    Route::post('settings/user_details', 'AccountController@saveUserDetails');
    Route::post('settings/payment_gateway_limits', 'AccountGatewayController@savePaymentGatewayLimits');
    Route::post('users/change_password', 'UserController@changePassword');
    Route::post('users/change_permission', 'UserController@changePermission');
    Route::get('settings/enable_two_factor', 'TwoFactorController@setupTwoFactor');
    Route::post('settings/enable_two_factor', 'TwoFactorController@enableTwoFactor');

    Route::resource('clients', 'ClientController');
    Route::get('api/clients', 'ClientController@getDatatable');
    Route::get('api/activities/{client_id?}', 'ActivityController@getDatatable');
    Route::post('clients/bulk', 'ClientController@bulk');
    Route::get('clients/statement/{client_id}', 'ClientController@statement');
    Route::post('email_history', 'ClientController@getEmailHistory');
    Route::post('reactivate_email/{bounce_id}', 'ClientController@reactivateEmail');

    Route::get('time_tracker', 'TimeTrackerController@index');
    Route::get('tasks/kanban/{client_id?}/{project_id?}', 'TaskKanbanController@index');
    Route::post('task_statuses', 'TaskKanbanController@storeStatus');
    Route::put('task_statuses/{task_status_id}', 'TaskKanbanController@updateStatus');
    Route::delete('task_statuses/{task_status_id}', 'TaskKanbanController@deleteStatus');
    Route::put('task_status_order/{task_id}', 'TaskKanbanController@updateTask');
    Route::resource('tasks', 'TaskController');
    Route::get('api/tasks/{client_id?}/{project_id?}', 'TaskController@getDatatable');
    Route::get('tasks/create/{client_id?}/{project_id?}', 'TaskController@create');
    Route::post('tasks/bulk', 'TaskController@bulk');
    Route::get('projects', 'ProjectController@index');
    Route::get('api/projects', 'ProjectController@getDatatable');
    Route::get('projects/create/{client_id?}', 'ProjectController@create');
    Route::post('projects', 'ProjectController@store');
    Route::put('projects/{projects}', 'ProjectController@update');
    Route::get('projects/{projects}/edit', 'ProjectController@edit');
    Route::get('projects/{projects}', 'ProjectController@show');
    Route::post('projects/bulk', 'ProjectController@bulk');
//     invoice
    Route::get('api/recurring_invoices/{client_id?}', 'InvoiceController@getRecurringDatatable');

    Route::get('invoices/delivery_note/{invoice_id}', 'InvoiceController@deliveryNote');
    Route::get('invoices/invoice_history/{invoice_id}', 'InvoiceController@invoiceHistory');
    Route::get('quotes/quote_history/{invoice_id}', 'InvoiceController@invoiceHistory');

    Route::resource('invoices', 'InvoiceController');
    Route::get('api/invoices/{client_id?}', 'InvoiceController@getDatatable');
    Route::get('invoices/create/{client_id?}', 'InvoiceController@create');
    Route::get('recurring_invoices/create/{client_id?}', 'InvoiceController@createRecurring');
    Route::get('recurring_invoices', 'RecurringInvoiceController@index');
    Route::get('recurring_invoices/{invoices}/edit', 'InvoiceController@edit');
    Route::get('recurring_invoices/{invoices}', 'InvoiceController@edit');
    Route::get('invoices/{invoices}/clone', 'InvoiceController@cloneInvoice');
    Route::post('invoices/bulk', 'InvoiceController@bulk');
    Route::post('recurring_invoices/bulk', 'InvoiceController@bulk');
//   invoice item
    Route::get('invoice_items/{invoice_items}/clone', 'invoiceItemController@cloneinvoiceItem');
    Route::get('api/invoice_items', 'invoiceItemController@getDatatable');
    Route::resource('invoice_items', 'invoiceItemController');
    Route::post('invoice_items/bulk', 'invoiceItemController@bulk');
//     expense
    Route::get('recurring_expenses', 'RecurringExpenseController@index');
    Route::get('api/recurring_expenses', 'RecurringExpenseController@getDatatable');
    Route::get('recurring_expenses/create/{vendor_id?}/{client_id?}/{category_id?}', 'RecurringExpenseController@create');
    Route::post('recurring_expenses', 'RecurringExpenseController@store');
    Route::put('recurring_expenses/{recurring_expenses}', 'RecurringExpenseController@update');
    Route::get('recurring_expenses/{recurring_expenses}/edit', 'RecurringExpenseController@edit');
    Route::get('recurring_expenses/{recurring_expenses}', 'RecurringExpenseController@edit');
    Route::post('recurring_expenses/bulk', 'RecurringExpenseController@bulk');

    Route::get('documents/{documents}/{filename?}', 'DocumentController@get');
    Route::get('documents/js/{documents}/{filename}', 'DocumentController@getVFSJS');
    Route::get('documents/preview/{documents}/{filename?}', 'DocumentController@getPreview');
    Route::post('documents', 'DocumentController@postUpload');
    Route::delete('documents/{documents}', 'DocumentController@delete');

    Route::get('quotes/create/{client_id?}', 'QuoteController@create');
    Route::get('quotes/{invoices}/clone', 'InvoiceController@cloneQuote');
    Route::get('quotes/{invoices}/edit', 'InvoiceController@edit');
    Route::put('quotes/{invoices}', 'InvoiceController@update');
    Route::get('quotes/{invoices}', 'InvoiceController@edit');
    Route::post('quotes', 'InvoiceController@store');
    Route::get('quotes', 'QuoteController@index');
    Route::get('api/quotes/{client_id?}', 'QuoteController@getDatatable');
    Route::post('quotes/bulk', 'QuoteController@bulk');
//       client type
    Route::get('client_types/{client_types}/clone', 'ClientTypeController@cloneClientType');
    Route::get('api/client_types', 'ClientTypeController@getDatatable');
    Route::resource('client_types', 'ClientTypeController');
    Route::post('client_types/bulk', 'ClientTypeController@bulk');

    Route::post('proposals/categories/bulk', 'ProposalCategoryController@bulk');
    Route::get('proposals/categories/{proposal_categories}/edit', 'ProposalCategoryController@edit');
    Route::get('proposals/categories/create', 'ProposalCategoryController@create');
    Route::resource('proposals/categories', 'ProposalCategoryController');
    Route::get('api/proposal_categories', 'ProposalCategoryController@getDatatable');

    Route::post('proposals/snippets/bulk', 'ProposalSnippetController@bulk');
    Route::get('proposals/snippets/{proposal_snippets}/edit', 'ProposalSnippetController@edit');
    Route::get('proposals/snippets/create', 'ProposalSnippetController@create');
    Route::resource('proposals/snippets', 'ProposalSnippetController');
    Route::get('api/proposal_snippets', 'ProposalSnippetController@getDatatable');

    Route::get('proposals/templates/{proposal_templates}/clone', 'ProposalTemplateController@cloneProposal');
    Route::post('proposals/templates/bulk', 'ProposalTemplateController@bulk');
    Route::get('proposals/templates/{proposal_templates}/edit', 'ProposalTemplateController@edit');
    Route::get('proposals/templates/create', 'ProposalTemplateController@create');
    Route::resource('proposals/templates', 'ProposalTemplateController');
    Route::get('api/proposal_templates', 'ProposalTemplateController@getDatatable');

    Route::post('proposals/bulk', 'ProposalController@bulk');
    Route::get('proposals/{proposals}/edit', 'ProposalController@edit');
    Route::get('proposals/{proposals}/download', 'ProposalController@download');
    Route::get('proposals/create/{invoice_id?}/{proposal_template_id?}', 'ProposalController@create');
    Route::resource('proposals', 'ProposalController');
    Route::get('api/proposals', 'ProposalController@getDatatable');

    Route::resource('payments', 'PaymentController');
    Route::get('payments/create/{client_id?}/{invoice_id?}', 'PaymentController@create');
    Route::get('api/payments/{client_id?}', 'PaymentController@getDatatable');
    Route::post('payments/bulk', 'PaymentController@bulk');
//  credit
    Route::resource('credits', 'CreditController');
    Route::get('credits/create/{client_id?}/{invoice_id?}', 'CreditController@create');
    Route::get('api/credits/{client_id?}', 'CreditController@getDatatable');
    Route::post('credits/bulk', 'CreditController@bulk');
//  approval status
    Route::get('statuses/{statuses}/clone', 'StatusController@cloneStatus');
    Route::get('api/statuses', 'StatusController@getDatatable');
    Route::resource('statuses', 'StatusController');
    Route::post('statuses/bulk', 'StatusController@bulk');
//  manufacturer
    Route::get('manufacturers/{manufacturers}/clone', 'ManufacturerController@cloneManufacturer');
    Route::get('api/manufacturers', 'ManufacturerController@getDatatable');
    Route::resource('manufacturers', 'ManufacturerController');
    Route::post('manufacturers/bulk', 'ManufacturerController@bulk');
//   Point of sale
    Route::get('api/pointofsale/productsByBarcode', 'PointOfSaleApiController@productsByBarcode')->name('productsByBarcode');
//  product
    Route::get('products/{products}/clone', 'ProductController@cloneProduct');
    Route::get('api/products', 'ProductController@getDatatable');
    Route::resource('products', 'ProductController');
    Route::post('products/bulk', 'ProductController@bulk');
    //company we can setup a common properties like prefix, namespace and middleware
    Route::group(['middleware'=> ['setup']], function(){
        Route::get('companies/{companies}/clone', 'CompanyController@cloneCompany');
        Route::get('api/companies', 'CompanyController@getDatatable');
        Route::resource('companies', 'CompanyController');
        Route::post('companies/bulk', 'CompanyController@bulk');
    });

//  item movement
    Route::get('item_movements/{item_movements}/clone', 'ItemMovementController@cloneItemMovement');
    Route::get('api/item_movements', 'ItemMovementController@getDatatable');
    Route::resource('item_movements', 'ItemMovementController');
    Route::post('item_movements/bulk', 'ItemMovementController@bulk');
//  item store
    Route::get('stores/{stores}/clone', 'StoreController@cloneStore');
    Route::get('api/stores', 'StoreController@getDatatable');
    Route::resource('stores', 'StoreController');
    Route::post('stores/bulk', 'StoreController@bulk');
    Route::post('item_stores/item_list', 'ItemStoreController@getItemList');
//    item brand
    Route::get('item_brands/{item_brands}/clone', 'ItemBrandController@cloneItemBrand');
    Route::get('api/item_brands', 'ItemBrandController@getDatatable');
    Route::resource('item_brands', 'ItemBrandController');
    Route::post('item_brands/bulk', 'ItemBrandController@bulk');
//      item category
    Route::get('item_categories/{item_categories}/clone', 'ItemCategoryController@cloneItemCategory');
    Route::get('api/item_categories', 'ItemCategoryController@getDatatable');
    Route::resource('item_categories', 'ItemCategoryController');
    Route::post('item_categories/bulk', 'ItemCategoryController@bulk');
//     item store
    Route::get('item_stores/{item_stores}/clone', 'ItemStoreController@cloneItemStore');
    Route::resource('item_stores', 'ItemStoreController');
    Route::get('api/item_stores', 'ItemStoreController@getDatatable');
    Route::post('item_stores/bulk', 'ItemStoreController@bulk');
//    item request
    Route::get('item_requests/{item_requests}/clone', 'ItemRequestController@cloneItemRequest');
    Route::resource('item_requests', 'ItemRequestController');
    Route::get('api/item_requests', 'ItemRequestController@getDatatable');
    Route::post('item_requests/bulk', 'ItemRequestController@bulk');
    Route::post('item_requests/approve', 'ItemRequestController@approve');
//         item transfer
    Route::get('item_transfers/{item_transfers}/clone', 'ItemTransferController@cloneItemTransfer');
    Route::resource('item_transfers', 'ItemTransferController');
    Route::get('api/item_transfers', 'ItemTransferController@getDatatable');
    Route::post('item_transfers/bulk', 'ItemTransferController@bulk');
//     item price
    Route::get('item_prices/{item_prices}/clone', 'ItemPriceController@cloneItemPrice');
    Route::resource('item_prices', 'ItemPriceController');
    Route::get('api/item_prices', 'ItemPriceController@getDatatable');
    Route::post('item_prices/bulk', 'ItemPriceController@bulk');
//       branch
    Route::get('branches/{branches}/clone', 'BranchController@cloneBranch');
    Route::get('api/branches', 'BranchController@getDatatable');
    Route::resource('branches', 'BranchController');
    Route::post('branches/bulk', 'BranchController@bulk');
//       department
    Route::get('departments/{departments}/clone', 'departmentController@cloneDepartment');
    Route::get('api/departments', 'departmentController@getDatatable');
    Route::resource('departments', 'departmentController');
    Route::post('departments/bulk', 'departmentController@bulk');
//   location
    Route::get('locations/{locations}/clone', 'LocationController@cloneLocation');
    Route::get('api/locations', 'LocationController@getDatatable');
    Route::resource('locations', 'LocationController');
    Route::post('locations/bulk', 'LocationController@bulk');
//   unit
    Route::get('units/{units}/clone', 'UnitController@cloneUnit');
    Route::get('api/units', 'UnitController@getDatatable');
    Route::resource('units', 'UnitController');
    Route::post('units/bulk', 'UnitController@bulk');
//       sales type
    Route::get('sale_types/{sale_types}/clone', 'SaleTypeController@cloneSaleType');
    Route::get('api/sale_types', 'SaleTypeController@getDatatable');
    Route::resource('sale_types', 'SaleTypeController');
    Route::post('sale_types/bulk', 'SaleTypeController@bulk');
//    hold reason
    Route::get('hold_reasons/{hold_reasons}/clone', 'HoldReasonController@cloneSaleType');
    Route::get('api/hold_reasons', 'HoldReasonController@getDatatable');
    Route::resource('hold_reasons', 'HoldReasonController');
    Route::post('hold_reasons/bulk', 'HoldReasonController@bulk');
//   schedule category
    Route::get('schedule_categories/{schedule_categories}/clone', 'ScheduleCategoryController@cloneScheduleCategory');
    Route::get('api/schedule_categories', 'ScheduleCategoryController@getDatatable');
    Route::resource('schedule_categories', 'ScheduleCategoryController');
    Route::post('schedule_categories/bulk', 'ScheduleCategoryController@bulk');
//   schedule
    Route::get('schedules/{schedules}/clone', 'ScheduleController@cloneSchedule');
    Route::get('api/schedules', 'ScheduleController@getDatatable');
    Route::resource('schedules', 'ScheduleController');
    Route::post('schedules/bulk', 'ScheduleController@bulk');
//   scheduled report
    Route::get('scheduled_reports/{scheduled_reports}/clone', 'ScheduledReportController@cloneScheduleReport');
    Route::get('api/scheduled_reports', 'ScheduledReportController@getDatatable');
    Route::resource('scheduled_reports', 'ScheduledReportController');
    Route::post('scheduled_reports/bulk', 'ScheduledReportController@bulk');
//   account
    Route::get('/resend_confirmation', 'AccountController@resendConfirmation');
    Route::post('/update_setup', 'AppController@updateSetup');

// vendor
    Route::get('vendors/{vendors}/clone', 'VendorController@cloneVendor');
    Route::resource('vendors', 'VendorController');
    Route::get('api/vendors', 'VendorController@getDatatable');
    Route::post('vendors/bulk', 'VendorController@bulk');

// Expense
    Route::get('expenses/{expenses}/clone', 'ExpenseController@cloneExpense');
    Route::resource('expenses', 'ExpenseController');
    Route::get('expenses/create/{client_id?}/{vendor_id?}/{category_id?}', 'ExpenseController@create');
    Route::get('expenses/{expenses}/clone', 'ExpenseController@cloneExpense');
    Route::get('api/expenses', 'ExpenseController@getDatatable');
    Route::get('api/vendor_expenses/{id}', 'ExpenseController@getDatatableVendor');
    Route::get('api/client_expenses/{id}', 'ExpenseController@getDatatableClient');
    Route::post('expenses/bulk', 'ExpenseController@bulk');
    Route::get('expense_categories', 'ExpenseCategoryController@index');
    Route::get('api/expense_categories', 'ExpenseCategoryController@getDatatable');
    Route::get('expense_categories/create', 'ExpenseCategoryController@create');
    Route::post('expense_categories', 'ExpenseCategoryController@store');
    Route::put('expense_categories/{expense_categories}', 'ExpenseCategoryController@update');
    Route::get('expense_categories/{expense_categories}/edit', 'ExpenseCategoryController@edit');
    Route::post('expense_categories/bulk', 'ExpenseCategoryController@bulk');

// BlueVine
    Route::post('bluevine/signup', 'BlueVineController@signup');
    Route::get('bluevine/hide_message', 'BlueVineController@hideMessage');
    Route::get('bluevine/completed', 'BlueVineController@handleCompleted');

    Route::get('white_label/hide_message', 'NinjaController@hideWhiteLabelMessage');
    Route::get('white_label/purchase', 'NinjaController@purchaseWhiteLabel');

    Route::get('reports', 'ReportController@showReports');
    Route::post('reports', 'ReportController@showReports');
    Route::get('reports/calendar', 'CalendarController@showCalendar');
    Route::get('reports/calendar_events', 'CalendarController@loadEvents');
    Route::get('reports/emails', 'ReportController@showEmailReport');
    Route::get('reports/emails_report/{start_date}/{end_date}', 'ReportController@loadEmailReport');
});

Route::group([
    'middleware' => ['lookup:user', 'auth:user', 'permissions.required'],
    // 'permissions' => 'admin',
], function () {
//    user
    Route::get('api/users', 'UserController@getDatatable');
    Route::get('users/{users}/clone', 'UserController@cloneUser');
    Route::resource('users', 'UserController');
    Route::post('users/bulk', 'UserController@bulk');
    Route::get('send_confirmation/{user_id}', 'UserController@sendConfirmation');
    Route::get('/switch_account/{user_id}', 'UserController@switchAccount');
    Route::get('/account/{account_key}', 'UserController@viewAccountByKey');
    Route::get('/unlink_account/{user_account_id}/{user_id}', 'UserController@unlinkAccount');
    Route::get('/manage_companies', 'UserController@manageCompanies');
    Route::get('/errors', 'AppController@errors');
    Route::get('/test_headless', 'AppController@testHeadless');
//   event subscription 
    Route::get('api/tokens', 'TokenController@getDatatable');
    Route::resource('tokens', 'TokenController');
    Route::post('tokens/bulk', 'TokenController@bulk');
    Route::get('api/subscriptions', 'SubscriptionController@getDatatable');
    Route::resource('subscriptions', 'SubscriptionController');
    Route::post('subscriptions/bulk', 'SubscriptionController@bulk');
//    user permission_groups
    Route::get('permission_groups/{permission_groups}/clone', 'PermissionGroupController@clonePermissionGroup');
    Route::get('api/permission_groups', 'PermissionGroupController@getDatatable');
    Route::resource('permission_groups', 'PermissionGroupController');
    Route::post('permission_groups/bulk', 'PermissionGroupController@bulk');
    Route::post('permission_groups/change_permission', 'PermissionGroupController@changePermission');
//    permissions
    Route::get('permissions/{permissions}/clone', 'permissionController@clonePermission');
    Route::get('api/permissions', 'permissionController@getDatatable');
    Route::resource('permissions', 'permissionController');
    Route::post('permissions/bulk', 'permissionController@bulk');
//  tax
    Route::get('tax_rates/{tax_rates}/clone', 'TaxRateController@cloneTaxRate');
    Route::get('api/tax_rates', 'TaxRateController@getDatatable');
    Route::resource('tax_rates', 'TaxRateController');
    Route::post('tax_rates/bulk', 'TaxRateController@bulk');

    Route::get('settings/email_preview', 'AccountController@previewEmail');
    Route::post('settings/client_portal', 'AccountController@saveClientPortalSettings');
    Route::post('settings/email_settings', 'AccountController@saveEmailSettings');
    Route::get('company/{section}/{subSection?}', 'AccountController@redirectLegacy');
    Route::get('settings/data_visualizations', 'ReportController@d3');

    Route::post('settings/change_plan', 'AccountController@changePlan');
    Route::post('settings/cancel_account', 'AccountController@cancelAccount');
    Route::post('settings/purge_data', 'AccountController@purgeData');
    Route::post('settings/company_details', 'AccountController@updateDetails');
    Route::post('settings/{section?}', 'AccountController@doSection');
    Route::post('remove_logo', 'AccountController@removeLogo');

    Route::post('/export', 'ExportController@doExport');
    Route::post('/import', 'ImportController@doImport');
    Route::get('/cancel_import', 'ImportController@cancelImport');
    Route::post('/import_csv', 'ImportController@doImportCSV');

    Route::get('gateways/create/{show_wepay?}', 'AccountGatewayController@create');
    Route::resource('gateways', 'AccountGatewayController');
    Route::get('gateways/{public_id}/resend_confirmation', 'AccountGatewayController@resendConfirmation');
    Route::get('api/gateways', 'AccountGatewayController@getDatatable');
    Route::post('account_gateways/bulk', 'AccountGatewayController@bulk');

    Route::get('payment_terms', 'PaymentTermController@index');
    Route::get('api/payment_terms', 'PaymentTermController@getDatatable');
    Route::get('payment_terms/create', 'PaymentTermController@create');
    Route::post('payment_terms', 'PaymentTermController@store');
    Route::put('payment_terms/{payment_terms}', 'PaymentTermController@update');
    Route::get('payment_terms/{payment_terms}/edit', 'PaymentTermController@edit');
    Route::post('payment_terms/bulk', 'PaymentTermController@bulk');

    Route::get('bank_accounts/import_ofx', 'BankAccountController@showImportOFX');
    Route::post('bank_accounts/import_ofx', 'BankAccountController@doImportOFX');
    Route::resource('bank_accounts', 'BankAccountController');
    Route::get('api/bank_accounts', 'BankAccountController@getDatatable');
    Route::post('bank_accounts/bulk', 'BankAccountController@bulk');
    Route::post('bank_accounts/validate', 'BankAccountController@validateAccount');
    Route::post('bank_accounts/import_expenses/{bank_id}', 'BankAccountController@importExpenses');

//Route::get('self-update', 'SelfUpdateController@index');
//Route::post('self-update', 'SelfUpdateController@update');
//Route::get('self-update/download', 'SelfUpdateController@download');
});
//    setting
Route::group(['middleware' => ['lookup:user', 'auth:user']], function () {
    Route::get('settings/{section?}', 'AccountController@showSection');
});

// Redirects for legacy links
Route::get('rocksteady', 'AppController@redirect');
Route::get('about', 'AppController@redirect');
Route::get('contact', 'AppController@redirect');
Route::get('plans', 'AppController@redirect');
Route::get('faq', 'AppController@redirect');
Route::get('features', 'AppController@redirect');
Route::get('testimonials', 'AppController@redirect');
Route::get('compare-online-invoicing{sites?}', 'AppController@redirect');
Route::get('feed', 'AppController@redirect');
Route::get('comments/feed', 'AppController@redirect');
Route::get('terms', 'AppController@redirect');

// Include static app constants
require_once app_path() . '/Constants.php';
