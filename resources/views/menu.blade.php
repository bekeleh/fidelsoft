@if($entityType == ENTITY_USER)
    @if (Auth::user()->can('create', [ENTITY_PERMISSION_GROUP]))
        {!! DropdownButton::normal(trans('texts.maintenance'))
        ->withAttributes(['class'=>'maintenanceDropdown'])
        ->withContents([
        ['label' => trans('texts.new_permission_group'), 'url' => url('/permission_groups')],
        ['label' => trans('texts.new_branch'), 'url' => url('/branches')],
        ['label' => trans('texts.new_location'), 'url' => url('/locations')],
        ])->split() !!}
    @endif
@elseif($entityType == ENTITY_PERMISSION_GROUP)
    @if (Auth::user()->can('create', [ENTITY_PERMISSION_GROUP]))
        {!! DropdownButton::normal(trans('texts.maintenance'))
        ->withAttributes(['class'=>'maintenanceDropdown'])
        ->withContents([
        ['label' => trans('texts.new_user'), 'url' => url('/users')],
        ['label' => trans('texts.new_branch'), 'url' => url('/branches')],
        ['label' => trans('texts.new_location'), 'url' => url('/locations')],
        ])->split() !!}
    @endif
@elseif (in_array($entityType, [ENTITY_PROPOSAL_SNIPPET,ENTITY_PROPOSAL_CATEGORY]))
    @if (Auth::user()->can('create', [ENTITY_PROPOSAL_CATEGORY]))
        {!! DropdownButton::normal(trans('texts.maintenance'))
        ->withAttributes(['class'=>'maintenanceDropdown'])
        ->withContents([
        ['label' => trans('texts.new_proposal_category'), 'url' => url('/proposals/categories')],
        ])->split() !!}
    @endif
@elseif (in_array($entityType, [ENTITY_SCHEDULE]))
    @if (Auth::user()->can('create', [ENTITY_SCHEDULE]))
        {!! DropdownButton::normal(trans('texts.maintenance'))
        ->withAttributes(['class'=>'maintenanceDropdown'])
        ->withContents([
        ['label' => trans('texts.list_scheduled_reports'), 'url' => url('/scheduled_reports')],
        ['label' => trans('texts.new_schedule_category'), 'url' => url('/schedule_categories')],
        ])->split() !!}
    @endif
@endif
@if (in_array($entityType, [ENTITY_EXPENSE,ENTITY_RECURRING_EXPENSE, ENTITY_RECURRING_INVOICE,ENTITY_VENDOR]))
    @if (Auth::user()->can('create', [ENTITY_EXPENSE_CATEGORY,ENTITY_RECURRING_EXPENSE, ENTITY_RECURRING_INVOICE,ENTITY_VENDOR]))
        {!! DropdownButton::normal(trans('texts.maintenance'))
        ->withAttributes(['class'=>'maintenanceDropdown'])
        ->withContents([
        ['label' => trans('texts.new_recurring_expense'), 'url' => url('/recurring_expenses')],
        ['label' => trans('texts.new_expense_category'), 'url' => url('/expense_categories')],
        ])->split() !!}
    @endif
@elseif (in_array($entityType, [ENTITY_EXPENSE_CATEGORY,ENTITY_RECURRING_EXPENSE, ENTITY_RECURRING_INVOICE,ENTITY_VENDOR]))
    @if (Auth::user()->can('create', [ENTITY_EXPENSE_CATEGORY,ENTITY_RECURRING_EXPENSE, ENTITY_RECURRING_INVOICE,ENTITY_VENDOR]))
        {!! DropdownButton::normal(trans('texts.maintenance'))
        ->withAttributes(['class'=>'maintenanceDropdown'])
        ->withContents([
        ['label' => trans('texts.new_expense'), 'url' => url('/expenses')],
        ['label' => trans('texts.new_recurring_expense'), 'url' => url('/recurring_expenses')],
        ])->split() !!}
    @endif
@elseif (in_array($entityType, [ENTITY_PROPOSAL,ENTITY_PROPOSAL_TEMPLATE,ENTITY_PROPOSAL_SNIPPET]))
    @if (Auth::user()->can('create', [ENTITY_PROPOSAL_TEMPLATE,ENTITY_PROPOSAL_SNIPPET]))
        {!! DropdownButton::normal(trans('texts.maintenance'))
        ->withAttributes(['class'=>'maintenanceDropdown'])
        ->withContents([
        ['label' => trans('texts.new_proposal_template'), 'url' => url('/proposals/templates')],
        ['label' => trans('texts.new_proposal_snippet'), 'url' => url('/proposals/snippets')],
        ])->split() !!}
    @endif
    <!-- task menu -->
@elseif ($entityType == ENTITY_TASK)
    {!! Button::normal(trans('texts.kanban'))->asLinkTo(url('/tasks/kanban' . (! empty($clientId) ? ('/' . $clientId . (! empty($projectId) ? '/' . $projectId : '')) : '')))->appendIcon(Icon::create('th')) !!}
    {!! Button::normal(trans('texts.time_tracker'))->asLinkTo('javascript:openTimeTracker()')->appendIcon(Icon::create('time')) !!}
@endif
<!-- client invoice menu -->
@if (in_array($entityType, [ENTITY_INVOICE]))
    @if (Auth::user()->can('create', [ENTITY_INVOICE]))
        {!! DropdownButton::normal(trans('texts.maintenance'))
        ->withAttributes(['class'=>'maintenanceDropdown'])
        ->withContents([
        ['label' => trans('texts.new_client'), 'url' => url('/clients')],
        ['label' => trans('texts.new_quote'), 'url' => url('/quotes')],
        ['label' => trans('texts.new_credit'), 'url' => url('/credits')],
        ['label' => trans('texts.new_expense'), 'url' => url('/expenses')],
        ])->split() !!}
    @endif
    <!-- vendor bill menu -->
@elseif (in_array($entityType, [ENTITY_BILL]))
    @if (Auth::user()->can('create', [ENTITY_BILL]))
        {!! DropdownButton::normal(trans('texts.maintenance'))
        ->withAttributes(['class'=>'maintenanceDropdown'])
        ->withContents([
        ['label' => trans('texts.new_vendor'), 'url' => url('/vendors')],
        ['label' => trans('texts.new_quote'), 'url' => url('/bill_quotes')],
        ['label' => trans('texts.new_credit'), 'url' => url('/vendor_credits')],
        ['label' => trans('texts.new_item_store'), 'url' => url('/item_stores')],
        ['label' => trans('texts.new_expense'), 'url' => url('/bill_expenses')],
        ])->split() !!}
    @endif
@elseif (in_array($entityType, [ENTITY_QUOTE]))
    @if (Auth::user()->can('create', [ENTITY_QUOTE]))
        {!! DropdownButton::normal(trans('texts.maintenance'))
        ->withAttributes(['class'=>'maintenanceDropdown'])
        ->withContents([
        ['label' => trans('texts.new_invoice'), 'url' => url('/invoices')],
        ['label' => trans('texts.new_client'), 'url' => url('/clients')],
        ['label' => trans('texts.new_credit'), 'url' => url('/credits')],
        ['label' => trans('texts.new_expense'), 'url' => url('/expenses')],
        ])->split() !!}
    @endif
@elseif (in_array($entityType, [ENTITY_CLIENT]))
    @if (Auth::user()->can('create', [ENTITY_CLIENT]))
        {!! DropdownButton::normal(trans('texts.maintenance'))
        ->withAttributes(['class'=>'maintenanceDropdown'])
        ->withContents([
        ['label' => trans('texts.new_invoice'), 'url' => url('/invoices')],
        ['label' => trans('texts.new_quote'), 'url' => url('/quotes')],
        ['label' => trans('texts.new_credit'), 'url' => url('/credits')],
        ['label' => trans('texts.new_expense'), 'url' => url('/expenses')],
        ])->split() !!}
    @endif
@elseif (in_array($entityType, [ENTITY_CLIENT_TYPE]))
    @if (Auth::user()->can('create', [ENTITY_CLIENT_TYPE]))
        {!! DropdownButton::normal(trans('texts.maintenance'))
        ->withAttributes(['class'=>'maintenanceDropdown'])
        ->withContents([
        ['label' => trans('texts.new_invoice'), 'url' => url('/invoices')],
        ['label' => trans('texts.new_quote'), 'url' => url('/quotes')],
        ['label' => trans('texts.new_credit'), 'url' => url('/credits')],
        ['label' => trans('texts.new_expense'), 'url' => url('/expenses')],
        ['label' => trans('texts.new_client'), 'url' => url('/clients')],
        ])->split() !!}
    @endif
@elseif (in_array($entityType, [ENTITY_PAYMENT]))
    @if (Auth::user()->can('create', [ENTITY_PAYMENT]))
        {!! DropdownButton::normal(trans('texts.maintenance'))
        ->withAttributes(['class'=>'maintenanceDropdown'])
        ->withContents([
        ['label' => trans('texts.new_invoice'), 'url' => url('/invoices')],
        ['label' => trans('texts.new_quote'), 'url' => url('/quotes')],
        ['label' => trans('texts.new_client'), 'url' => url('/clients')],
        ])->split() !!}
    @endif
@endif
<!-- item menu -->
@if (in_array($entityType, [ENTITY_PRODUCT]))
    @if (Auth::user()->can('create', [ENTITY_PRODUCT]))
        {!! DropdownButton::normal(trans('texts.maintenance'))
        ->withAttributes(['class'=>'maintenanceDropdown'])
        ->withContents([
        ['label' => trans('texts.new_item_brand'), 'url' => url('/item_brands')],
        ['label' => trans('texts.new_item_category'), 'url' => url('/item_categories')],
        ['label' => trans('texts.new_item_price'), 'url' => url('/item_prices')],
        ['label' => trans('texts.new_item_store'), 'url' => url('/item_stores')],
        ['label' => trans('texts.new_item_transfer'), 'url' => url('/item_transfers')],
        ['label' => trans('texts.list_item_movements'), 'url' => url('/item_movements')],
        ['label' => trans('texts.new_warehouse'), 'url' => url('/warehouses')],
        ])->split() !!}
    @endif
@elseif (in_array($entityType, [ENTITY_ITEM_BRAND]))
    @if (Auth::user()->can('create', [ENTITY_ITEM_BRAND]))
        {!! DropdownButton::normal(trans('texts.maintenance'))
        ->withAttributes(['class'=>'maintenanceDropdown'])
        ->withContents([
        ['label' => trans('texts.new_item'), 'url' => url('/products')],
        ['label' => trans('texts.new_item_category'), 'url' => url('/item_categories')],
        ['label' => trans('texts.new_item_price'), 'url' => url('/item_prices')],
        ['label' => trans('texts.new_item_store'), 'url' => url('/item_stores')],
        ['label' => trans('texts.new_item_transfer'), 'url' => url('/item_transfers')],
        ['label' => trans('texts.list_item_movements'), 'url' => url('/item_movements')],
        ['label' => trans('texts.new_warehouse'), 'url' => url('/warehouses')],
        ])->split() !!}
    @endif
@elseif (in_array($entityType, [ENTITY_ITEM_CATEGORY]))
    @if (Auth::user()->can('create', [ENTITY_ITEM_CATEGORY]))
        {!! DropdownButton::normal(trans('texts.maintenance'))
        ->withAttributes(['class'=>'maintenanceDropdown'])
        ->withContents([
        ['label' => trans('texts.new_item'), 'url' => url('/products')],
        ['label' => trans('texts.new_item_brand'), 'url' => url('/item_brands')],
        ['label' => trans('texts.new_item_price'), 'url' => url('/item_prices')],
        ['label' => trans('texts.new_item_store'), 'url' => url('/item_stores')],
        ['label' => trans('texts.new_item_transfer'), 'url' => url('/item_transfers')],
        ['label' => trans('texts.list_item_movements'), 'url' => url('/item_movements')],
        ['label' => trans('texts.new_warehouse'), 'url' => url('/warehouses')],
        ])->split() !!}
    @endif
@elseif($entityType == ENTITY_ITEM_TRANSFER)
    @if (Auth::user()->can('create', [ENTITY_ITEM_TRANSFER]))
        {!! DropdownButton::normal(trans('texts.maintenance'))
        ->withAttributes(['class'=>'maintenanceDropdown'])
        ->withContents([
        ['label' => trans('texts.new_item_store'), 'url' => url('/item_stores')],
        ['label' => trans('texts.new_warehouse'), 'url' => url('/warehouses')],
        ['label' => trans('texts.list_item_movements'), 'url' => url('/item_movements')],
                ['label' => trans('texts.new_item_request'), 'url' => url('/item_requests')],
        ])->split() !!}
    @endif
@elseif($entityType == ENTITY_ITEM_REQUEST)
    @if (Auth::user()->can('create', [ENTITY_ITEM_REQUEST]))
        {!! DropdownButton::normal(trans('texts.maintenance'))
        ->withAttributes(['class'=>'maintenanceDropdown'])
        ->withContents([
        ['label' => trans('texts.new_item_store'), 'url' => url('/item_stores')],
        ['label' => trans('texts.new_warehouse'), 'url' => url('/warehouses')],
        ['label' => trans('texts.new_item_transfer'), 'url' => url('/item_transfers')],
        ['label' => trans('texts.list_item_movements'), 'url' => url('/item_movements')],
        ])->split() !!}
    @endif
@elseif($entityType == ENTITY_ITEM_MOVEMENT)
    @if (Auth::user()->can('create', [ENTITY_ITEM_MOVEMENT]))
        {!! DropdownButton::normal(trans('texts.maintenance'))
        ->withAttributes(['class'=>'maintenanceDropdown'])
        ->withContents([
        ['label' => trans('texts.new_item_store'), 'url' => url('/item_stores')],
        ['label' => trans('texts.new_warehouse'), 'url' => url('/warehouses')],
        ['label' => trans('texts.new_item_transfer'), 'url' => url('/item_transfers')],
                ['label' => trans('texts.new_item_request'), 'url' => url('/item_requests')],
        ])->split() !!}
    @endif
@elseif($entityType == ENTITY_ITEM_STORE)
    @if (Auth::user()->can('create', [ENTITY_ITEM_STORE]))
        {!! DropdownButton::normal(trans('texts.maintenance'))
        ->withAttributes(['class'=>'maintenanceDropdown'])
        ->withContents([
        ['label' => trans('texts.new_product'), 'url' => url('/products')],
        ['label' => trans('texts.new_warehouse'), 'url' => url('/warehouses')],
        ['label' => trans('texts.new_branch'), 'url' => url('/branches')],
        ['label' => trans('texts.new_item_transfer'), 'url' => url('/item_transfers')],
        ['label' => trans('texts.new_item_request'), 'url' => url('/item_requests')],
        ['label' => trans('texts.list_item_movements'), 'url' => url('/item_movements')],
        ['label' => trans('texts.new_item_price'), 'url' => url('/item_prices')],
        ['label' => trans('texts.new_invoice'), 'url' => url('/invoices')],
        ['label' => trans('texts.new_bill'), 'url' => url('/bills')],
        ])->split() !!}
    @endif
@elseif($entityType == ENTITY_ITEM_PRICE)
    @if (Auth::user()->can('create', [ENTITY_ITEM_PRICE]))
        {!! DropdownButton::normal(trans('texts.maintenance'))
        ->withAttributes(['class'=>'maintenanceDropdown'])
        ->withContents([
        ['label' => trans('texts.new_product'), 'url' => url('/products')],
        ['label' => trans('texts.new_warehouse'), 'url' => url('/warehouses')],
        ['label' => trans('texts.new_branch'), 'url' => url('/branches')],
        ['label' => trans('texts.new_item_store'), 'url' => url('/item_stores')],
        ['label' => trans('texts.new_item_transfer'), 'url' => url('/item_transfers')],
        ['label' => trans('texts.list_item_movements'), 'url' => url('/item_movements')],
        ])->split() !!}
    @endif
@elseif($entityType == ENTITY_WAREHOUSE)
    @if (Auth::user()->can('create', [ENTITY_WAREHOUSE]))
        {!! DropdownButton::normal(trans('texts.maintenance'))
        ->withAttributes(['class'=>'maintenanceDropdown'])
        ->withContents([
        ['label' => trans('texts.new_product'), 'url' => url('/products')],
        ['label' => trans('texts.new_item_store'), 'url' => url('/item_stores')],
        ['label' => trans('texts.new_item_transfer'), 'url' => url('/item_transfers')],
        ['label' => trans('texts.list_item_movements'), 'url' => url('/item_movements')],
                ['label' => trans('texts.new_item_transfer'), 'url' => url('/item_transfers')],
        ])->split() !!}
    @endif
@elseif($entityType == ENTITY_BRANCH)
    @if (Auth::user()->can('create', [ENTITY_BRANCH]))
        {!! DropdownButton::normal(trans('texts.maintenance'))
        ->withAttributes(['class'=>'maintenanceDropdown'])
        ->withContents([
        ['label' => trans('texts.new_user'), 'url' => url('/users')],
        ['label' => trans('texts.new_product'), 'url' => url('/products')],
        ['label' => trans('texts.new_item_store'), 'url' => url('/item_stores')],
        ['label' => trans('texts.new_item_transfer'), 'url' => url('/item_transfers')],
        ['label' => trans('texts.new_item_request'), 'url' => url('/item_requests')],
        ['label' => trans('texts.list_item_movements'), 'url' => url('/item_movements')],
        ['label' => trans('texts.new_warehouse'), 'url' => url('/warehouses')],
        ])->split() !!}
    @endif
@endif