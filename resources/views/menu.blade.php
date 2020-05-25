@if($entityType == ENTITY_USER)
    @if (Auth::user()->can('create', [ENTITY_PERMISSION_GROUP]))
        {!! DropdownButton::normal(trans('texts.maintenance'))
        ->withAttributes(['class'=>'maintenanceDropdown'])
        ->withContents([
        ['label' => trans('texts.new_permission_group'), 'url' => url('/permission_groups/create')],
        ])->split() !!}
    @endif
@elseif($entityType == ENTITY_PERMISSION_GROUP)
    @if (Auth::user()->can('create', [ENTITY_PERMISSION_GROUP]))
        {!! DropdownButton::normal(trans('texts.maintenance'))
        ->withAttributes(['class'=>'maintenanceDropdown'])
        ->withContents([
        ['label' => trans('texts.new_user'), 'url' => url('/users/create')],
        ])->split() !!}
    @endif
@elseif (in_array($entityType, [ENTITY_PROPOSAL_SNIPPET,ENTITY_PROPOSAL_CATEGORY]))
    @if (Auth::user()->can('create', [ENTITY_PROPOSAL_CATEGORY]))
        {!! DropdownButton::normal(trans('texts.maintenance'))
        ->withAttributes(['class'=>'maintenanceDropdown'])
        ->withContents([
        ['label' => trans('texts.new_proposal_category'), 'url' => url('/proposals/categories/create')],
        ])->split() !!}
    @endif
@elseif (in_array($entityType, [ENTITY_SCHEDULE]))
    @if (Auth::user()->can('create', [ENTITY_SCHEDULE]))
        {!! DropdownButton::normal(trans('texts.maintenance'))
        ->withAttributes(['class'=>'maintenanceDropdown'])
        ->withContents([
            ['label' => trans('texts.list_scheduled_reports'), 'url' => url('/scheduled_reports')],
        ['label' => trans('texts.new_schedule_category'), 'url' => url('/schedule_categories/create')],
        ])->split() !!}
    @endif
@endif
@if (in_array($entityType, [ENTITY_EXPENSE,ENTITY_EXPENSE_CATEGORY,ENTITY_RECURRING_EXPENSE, ENTITY_RECURRING_INVOICE,ENTITY_VENDOR]))
    @if (Auth::user()->can('create', [ENTITY_EXPENSE_CATEGORY,ENTITY_RECURRING_EXPENSE, ENTITY_RECURRING_INVOICE,ENTITY_VENDOR]))
        {!! DropdownButton::normal(trans('texts.maintenance'))
        ->withAttributes(['class'=>'maintenanceDropdown'])
        ->withContents([
        ['label' => trans('texts.new_recurring_expense'), 'url' => url('/recurring_expenses')],
        ['label' => trans('texts.new_expense_category'), 'url' => url('/expense_categories')],
        ])->split() !!}
    @endif
@elseif (in_array($entityType, [ENTITY_PROPOSAL,ENTITY_PROPOSAL_TEMPLATE,ENTITY_PROPOSAL_SNIPPET]))
    @if (Auth::user()->can('create', [ENTITY_PROPOSAL_TEMPLATE,ENTITY_PROPOSAL_SNIPPET]))
        {!! DropdownButton::normal(trans('texts.maintenance'))
        ->withAttributes(['class'=>'maintenanceDropdown'])
        ->withContents([
        ['label' => trans('texts.new_proposal_template'), 'url' => url('/proposals/templates/create')],
        ['label' => trans('texts.new_proposal_snippet'), 'url' => url('/proposals/snippets/create')],
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
@elseif (in_array($entityType, [ENTITY_CLIENT]))
    @if (Auth::user()->can('create', [ENTITY_CLIENT]))
        {!! DropdownButton::normal(trans('texts.maintenance'))
        ->withAttributes(['class'=>'maintenanceDropdown'])
        ->withContents([
        ['label' => trans('texts.new_invoice'), 'url' => url('/invoices')],
        ['label' => trans('texts.new_quote'), 'url' => url('/quotes')],
        ['label' => trans('texts.new_credit'), 'url' => url('/credits')],
        ['label' => trans('texts.new_expense'), 'url' => url('/expenses')],
        ['label' => trans('texts.new_sale_type'), 'url' => url('/sale_types')],
        ['label' => trans('texts.new_hold_reason'), 'url' => url('/hold_reasons')],
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
<!-- inventory menu -->
@if (in_array($entityType, [ENTITY_PRODUCT]))
    @if (Auth::user()->can('create', [ENTITY_PRODUCT]))
        {!! DropdownButton::normal(trans('texts.maintenance'))
        ->withAttributes(['class'=>'maintenanceDropdown'])
        ->withContents([
        ['label' => trans('texts.new_item_brand'), 'url' => url('/item_brands/create')],
        ['label' => trans('texts.new_item_category'), 'url' => url('/item_categories')],
        ['label' => trans('texts.new_item_price'), 'url' => url('/item_prices')],
        ['label' => trans('texts.new_item_store'), 'url' => url('/item_stores')],
        ['label' => trans('texts.new_item_transfer'), 'url' => url('/item_transfers')],
        ['label' => trans('texts.list_item_movements'), 'url' => url('/item_movements')],
        ['label' => trans('texts.new_store'), 'url' => url('/stores')],
        ['label' => trans('texts.new_unit'), 'url' => url('/units')],
        ])->split() !!}
    @endif
@elseif($entityType == ENTITY_ITEM_TRANSFER)
    @if (Auth::user()->can('create', [ENTITY_ITEM_TRANSFER]))
        {!! DropdownButton::normal(trans('texts.maintenance'))
        ->withAttributes(['class'=>'maintenanceDropdown'])
        ->withContents([
        ['label' => trans('texts.new_item_store'), 'url' => url('/item_stores')],
        ['label' => trans('texts.new_store'), 'url' => url('/stores')],
        ['label' => trans('texts.list_item_movements'), 'url' => url('/item_movements')],
        ])->split() !!}
    @endif
@elseif($entityType == ENTITY_ITEM_MOVEMENT)
    @if (Auth::user()->can('create', [ENTITY_ITEM_MOVEMENT]))
        {!! DropdownButton::normal(trans('texts.maintenance'))
        ->withAttributes(['class'=>'maintenanceDropdown'])
        ->withContents([
        ['label' => trans('texts.new_item_store'), 'url' => url('/item_stores')],
        ['label' => trans('texts.new_store'), 'url' => url('/stores')],
        ['label' => trans('texts.new_item_transfer'), 'url' => url('/item_transfers')],
        ])->split() !!}
    @endif
@elseif($entityType == ENTITY_ITEM_STORE)
    @if (Auth::user()->can('create', [ENTITY_ITEM_STORE]))
        {!! DropdownButton::normal(trans('texts.maintenance'))
        ->withAttributes(['class'=>'maintenanceDropdown'])
        ->withContents([
        ['label' => trans('texts.new_product'), 'url' => url('/items')],
        ['label' => trans('texts.new_store'), 'url' => url('/stores')],
        ['label' => trans('texts.new_item_transfer'), 'url' => url('/item_transfers')],
        ['label' => trans('texts.new_item_movement'), 'url' => url('/item_movements')],
        ['label' => trans('texts.new_item_price'), 'url' => url('/item_prices')],
        ])->split() !!}
    @endif
@elseif($entityType == ENTITY_ITEM_PRICE)
    @if (Auth::user()->can('create', [ENTITY_ITEM_PRICE]))
        {!! DropdownButton::normal(trans('texts.maintenance'))
        ->withAttributes(['class'=>'maintenanceDropdown'])
        ->withContents([
        ['label' => trans('texts.new_product'), 'url' => url('/items')],
        ['label' => trans('texts.new_store'), 'url' => url('/stores')],
        ['label' => trans('texts.new_item_transfer'), 'url' => url('/item_transfers')],
        ['label' => trans('texts.new_item_movement'), 'url' => url('/item_movements')],
        ['label' => trans('texts.new_sale_type'), 'url' => url('/sale_types')],
        ])->split() !!}
    @endif
@elseif($entityType == ENTITY_STORE)
    @if (Auth::user()->can('create', [ENTITY_STORE]))
        {!! DropdownButton::normal(trans('texts.maintenance'))
        ->withAttributes(['class'=>'maintenanceDropdown'])
        ->withContents([
        ['label' => trans('texts.new_product'), 'url' => url('/items')],
        ['label' => trans('texts.new_item_store'), 'url' => url('/item_stores')],
        ['label' => trans('texts.new_item_transfer'), 'url' => url('/item_transfers')],
        ['label' => trans('texts.new_item_movement'), 'url' => url('/item_movements')],
        ])->split() !!}
    @endif
@elseif($entityType == ENTITY_SALE_TYPE)
    @if (Auth::user()->can('create', [ENTITY_SALE_TYPE]))
        {!! DropdownButton::normal(trans('texts.maintenance'))
        ->withAttributes(['class'=>'maintenanceDropdown'])
        ->withContents([
        ['label' => trans('texts.new_product'), 'url' => url('/items')],
        ['label' => trans('texts.new_item_price'), 'url' => url('/item_prices')],
        ])->split() !!}
    @endif
@endif