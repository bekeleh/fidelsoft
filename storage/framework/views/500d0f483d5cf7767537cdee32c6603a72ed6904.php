<?php if($entityType == ENTITY_USER): ?>
    <?php if(Auth::user()->can('create', [ENTITY_PERMISSION_GROUP])): ?>
        <?php echo DropdownButton::normal(trans('texts.maintenance'))
        ->withAttributes(['class'=>'maintenanceDropdown'])
        ->withContents([
        ['label' => trans('texts.new_permission_group'), 'url' => url('/permission_groups')],
        ['label' => trans('texts.new_branch'), 'url' => url('/branches')],
        ['label' => trans('texts.new_location'), 'url' => url('/locations')],
        ])->split(); ?>

    <?php endif; ?>
<?php elseif($entityType == ENTITY_PERMISSION_GROUP): ?>
    <?php if(Auth::user()->can('create', [ENTITY_PERMISSION_GROUP])): ?>
        <?php echo DropdownButton::normal(trans('texts.maintenance'))
        ->withAttributes(['class'=>'maintenanceDropdown'])
        ->withContents([
        ['label' => trans('texts.new_user'), 'url' => url('/users')],
        ['label' => trans('texts.new_branch'), 'url' => url('/branches')],
        ['label' => trans('texts.new_location'), 'url' => url('/locations')],
        ])->split(); ?>

    <?php endif; ?>
<?php elseif(in_array($entityType, [ENTITY_PROPOSAL_SNIPPET,ENTITY_PROPOSAL_CATEGORY])): ?>
    <?php if(Auth::user()->can('create', [ENTITY_PROPOSAL_CATEGORY])): ?>
        <?php echo DropdownButton::normal(trans('texts.maintenance'))
        ->withAttributes(['class'=>'maintenanceDropdown'])
        ->withContents([
        ['label' => trans('texts.new_proposal_category'), 'url' => url('/proposals/categories')],
        ])->split(); ?>

    <?php endif; ?>
<?php elseif(in_array($entityType, [ENTITY_SCHEDULE])): ?>
    <?php if(Auth::user()->can('create', [ENTITY_SCHEDULE])): ?>
        <?php echo DropdownButton::normal(trans('texts.maintenance'))
        ->withAttributes(['class'=>'maintenanceDropdown'])
        ->withContents([
        ['label' => trans('texts.list_scheduled_reports'), 'url' => url('/scheduled_reports')],
        ['label' => trans('texts.new_schedule_category'), 'url' => url('/schedule_categories')],
        ])->split(); ?>

    <?php endif; ?>
<?php endif; ?>
<?php if(in_array($entityType, [ENTITY_EXPENSE,ENTITY_RECURRING_EXPENSE, ENTITY_RECURRING_INVOICE,ENTITY_VENDOR])): ?>
    <?php if(Auth::user()->can('create', [ENTITY_EXPENSE_CATEGORY,ENTITY_RECURRING_EXPENSE, ENTITY_RECURRING_INVOICE,ENTITY_VENDOR])): ?>
        <?php echo DropdownButton::normal(trans('texts.maintenance'))
        ->withAttributes(['class'=>'maintenanceDropdown'])
        ->withContents([
        ['label' => trans('texts.new_recurring_expense'), 'url' => url('/recurring_expenses')],
        ['label' => trans('texts.new_expense_category'), 'url' => url('/expense_categories')],
        ])->split(); ?>

    <?php endif; ?>
<?php elseif(in_array($entityType, [ENTITY_EXPENSE_CATEGORY,ENTITY_RECURRING_EXPENSE, ENTITY_RECURRING_INVOICE,ENTITY_VENDOR])): ?>
    <?php if(Auth::user()->can('create', [ENTITY_EXPENSE_CATEGORY,ENTITY_RECURRING_EXPENSE, ENTITY_RECURRING_INVOICE,ENTITY_VENDOR])): ?>
        <?php echo DropdownButton::normal(trans('texts.maintenance'))
        ->withAttributes(['class'=>'maintenanceDropdown'])
        ->withContents([
        ['label' => trans('texts.new_expense'), 'url' => url('/expenses')],
        ['label' => trans('texts.new_recurring_expense'), 'url' => url('/recurring_expenses')],
        ])->split(); ?>

    <?php endif; ?>
<?php elseif(in_array($entityType, [ENTITY_PROPOSAL,ENTITY_PROPOSAL_TEMPLATE,ENTITY_PROPOSAL_SNIPPET])): ?>
    <?php if(Auth::user()->can('create', [ENTITY_PROPOSAL_TEMPLATE,ENTITY_PROPOSAL_SNIPPET])): ?>
        <?php echo DropdownButton::normal(trans('texts.maintenance'))
        ->withAttributes(['class'=>'maintenanceDropdown'])
        ->withContents([
        ['label' => trans('texts.new_proposal_template'), 'url' => url('/proposals/templates')],
        ['label' => trans('texts.new_proposal_snippet'), 'url' => url('/proposals/snippets')],
        ])->split(); ?>

    <?php endif; ?>
    <!-- task menu -->
<?php elseif($entityType == ENTITY_TASK): ?>
    <?php echo Button::normal(trans('texts.kanban'))->asLinkTo(url('/tasks/kanban' . (! empty($clientId) ? ('/' . $clientId . (! empty($projectId) ? '/' . $projectId : '')) : '')))->appendIcon(Icon::create('th')); ?>

    <?php echo Button::normal(trans('texts.time_tracker'))->asLinkTo('javascript:openTimeTracker()')->appendIcon(Icon::create('time')); ?>

<?php endif; ?>
<!-- client invoice menu -->
<?php if(in_array($entityType, [ENTITY_INVOICE])): ?>
    <?php if(Auth::user()->can('create', [ENTITY_INVOICE])): ?>
        <?php echo DropdownButton::normal(trans('texts.maintenance'))
        ->withAttributes(['class'=>'maintenanceDropdown'])
        ->withContents([
        ['label' => trans('texts.new_client'), 'url' => url('/clients')],
        ['label' => trans('texts.new_quote'), 'url' => url('/quotes')],
        ['label' => trans('texts.new_credit'), 'url' => url('/credits')],
        ['label' => trans('texts.new_expense'), 'url' => url('/expenses')],
        ])->split(); ?>

    <?php endif; ?>
<?php elseif(in_array($entityType, [ENTITY_QUOTE])): ?>
    <?php if(Auth::user()->can('create', [ENTITY_QUOTE])): ?>
        <?php echo DropdownButton::normal(trans('texts.maintenance'))
        ->withAttributes(['class'=>'maintenanceDropdown'])
        ->withContents([
        ['label' => trans('texts.new_invoice'), 'url' => url('/invoices')],
        ['label' => trans('texts.new_client'), 'url' => url('/clients')],
        ['label' => trans('texts.new_credit'), 'url' => url('/credits')],
        ['label' => trans('texts.new_expense'), 'url' => url('/expenses')],
        ])->split(); ?>

    <?php endif; ?>
<?php elseif(in_array($entityType, [ENTITY_CLIENT])): ?>
    <?php if(Auth::user()->can('create', [ENTITY_CLIENT])): ?>
        <?php echo DropdownButton::normal(trans('texts.maintenance'))
        ->withAttributes(['class'=>'maintenanceDropdown'])
        ->withContents([
        ['label' => trans('texts.new_invoice'), 'url' => url('/invoices')],
        ['label' => trans('texts.new_quote'), 'url' => url('/quotes')],
        ['label' => trans('texts.new_credit'), 'url' => url('/credits')],
        ['label' => trans('texts.new_expense'), 'url' => url('/expenses')],
        ])->split(); ?>

    <?php endif; ?>
<?php elseif(in_array($entityType, [ENTITY_CLIENT_TYPE])): ?>
    <?php if(Auth::user()->can('create', [ENTITY_CLIENT_TYPE])): ?>
        <?php echo DropdownButton::normal(trans('texts.maintenance'))
        ->withAttributes(['class'=>'maintenanceDropdown'])
        ->withContents([
        ['label' => trans('texts.new_invoice'), 'url' => url('/invoices')],
        ['label' => trans('texts.new_quote'), 'url' => url('/quotes')],
        ['label' => trans('texts.new_credit'), 'url' => url('/credits')],
        ['label' => trans('texts.new_expense'), 'url' => url('/expenses')],
        ['label' => trans('texts.new_client'), 'url' => url('/clients')],
        ])->split(); ?>

    <?php endif; ?>
<?php elseif(in_array($entityType, [ENTITY_PAYMENT])): ?>
    <?php if(Auth::user()->can('create', [ENTITY_PAYMENT])): ?>
        <?php echo DropdownButton::normal(trans('texts.maintenance'))
        ->withAttributes(['class'=>'maintenanceDropdown'])
        ->withContents([
        ['label' => trans('texts.new_invoice'), 'url' => url('/invoices')],
        ['label' => trans('texts.new_quote'), 'url' => url('/quotes')],
        ['label' => trans('texts.new_client'), 'url' => url('/clients')],
        ])->split(); ?>

    <?php endif; ?>
<?php endif; ?>
<!-- item menu -->
<?php if(in_array($entityType, [ENTITY_PRODUCT])): ?>
    <?php if(Auth::user()->can('create', [ENTITY_PRODUCT])): ?>
        <?php echo DropdownButton::normal(trans('texts.maintenance'))
        ->withAttributes(['class'=>'maintenanceDropdown'])
        ->withContents([
        ['label' => trans('texts.new_item_brand'), 'url' => url('/item_brands')],
        ['label' => trans('texts.new_item_category'), 'url' => url('/item_categories')],
        ['label' => trans('texts.new_item_price'), 'url' => url('/item_prices')],
        ['label' => trans('texts.new_item_store'), 'url' => url('/item_stores')],
        ['label' => trans('texts.new_item_transfer'), 'url' => url('/item_transfers')],
        ['label' => trans('texts.list_item_movementss'), 'url' => url('/item_movements')],
        ['label' => trans('texts.new_warehouse'), 'url' => url('/warehouses')],
        ])->split(); ?>

    <?php endif; ?>
<?php elseif(in_array($entityType, [ENTITY_ITEM_BRAND])): ?>
    <?php if(Auth::user()->can('create', [ENTITY_ITEM_BRAND])): ?>
        <?php echo DropdownButton::normal(trans('texts.maintenance'))
        ->withAttributes(['class'=>'maintenanceDropdown'])
        ->withContents([
        ['label' => trans('texts.new_item'), 'url' => url('/products')],
        ['label' => trans('texts.new_item_category'), 'url' => url('/item_categories')],
        ['label' => trans('texts.new_item_price'), 'url' => url('/item_prices')],
        ['label' => trans('texts.new_item_store'), 'url' => url('/item_stores')],
        ['label' => trans('texts.new_item_transfer'), 'url' => url('/item_transfers')],
        ['label' => trans('texts.list_item_movementss'), 'url' => url('/item_movements')],
        ['label' => trans('texts.new_warehouse'), 'url' => url('/warehouses')],
        ])->split(); ?>

    <?php endif; ?>
<?php elseif(in_array($entityType, [ENTITY_ITEM_CATEGORY])): ?>
    <?php if(Auth::user()->can('create', [ENTITY_ITEM_CATEGORY])): ?>
        <?php echo DropdownButton::normal(trans('texts.maintenance'))
        ->withAttributes(['class'=>'maintenanceDropdown'])
        ->withContents([
        ['label' => trans('texts.new_item'), 'url' => url('/products')],
        ['label' => trans('texts.new_item_brand'), 'url' => url('/item_brands')],
        ['label' => trans('texts.new_item_price'), 'url' => url('/item_prices')],
        ['label' => trans('texts.new_item_store'), 'url' => url('/item_stores')],
        ['label' => trans('texts.new_item_transfer'), 'url' => url('/item_transfers')],
        ['label' => trans('texts.list_item_movementss'), 'url' => url('/item_movements')],
        ['label' => trans('texts.new_warehouse'), 'url' => url('/warehouses')],
        ])->split(); ?>

    <?php endif; ?>
<?php elseif($entityType == ENTITY_ITEM_TRANSFER): ?>
    <?php if(Auth::user()->can('create', [ENTITY_ITEM_TRANSFER])): ?>
        <?php echo DropdownButton::normal(trans('texts.maintenance'))
        ->withAttributes(['class'=>'maintenanceDropdown'])
        ->withContents([
        ['label' => trans('texts.new_item_store'), 'url' => url('/item_stores')],
        ['label' => trans('texts.new_warehouse'), 'url' => url('/warehouses')],
        ['label' => trans('texts.list_item_movementss'), 'url' => url('/item_movements')],
        ])->split(); ?>

    <?php endif; ?>
<?php elseif($entityType == ENTITY_ITEM_MOVEMENT): ?>
    <?php if(Auth::user()->can('create', [ENTITY_ITEM_MOVEMENT])): ?>
        <?php echo DropdownButton::normal(trans('texts.maintenance'))
        ->withAttributes(['class'=>'maintenanceDropdown'])
        ->withContents([
        ['label' => trans('texts.new_item_store'), 'url' => url('/item_stores')],
        ['label' => trans('texts.new_warehouse'), 'url' => url('/warehouses')],
        ['label' => trans('texts.new_item_transfer'), 'url' => url('/item_transfers')],
        ])->split(); ?>

    <?php endif; ?>
<?php elseif($entityType == ENTITY_ITEM_STORE): ?>
    <?php if(Auth::user()->can('create', [ENTITY_ITEM_STORE])): ?>
        <?php echo DropdownButton::normal(trans('texts.maintenance'))
        ->withAttributes(['class'=>'maintenanceDropdown'])
        ->withContents([
        ['label' => trans('texts.new_product'), 'url' => url('/products')],
        ['label' => trans('texts.new_warehouse'), 'url' => url('/warehouses')],
        ['label' => trans('texts.new_branch'), 'url' => url('/branches')],
        ['label' => trans('texts.new_item_transfer'), 'url' => url('/item_transfers')],
        ['label' => trans('texts.list_item_movements'), 'url' => url('/item_movements')],
        ['label' => trans('texts.new_item_price'), 'url' => url('/item_prices')],
        ])->split(); ?>

    <?php endif; ?>
<?php elseif($entityType == ENTITY_ITEM_PRICE): ?>
    <?php if(Auth::user()->can('create', [ENTITY_ITEM_PRICE])): ?>
        <?php echo DropdownButton::normal(trans('texts.maintenance'))
        ->withAttributes(['class'=>'maintenanceDropdown'])
        ->withContents([
        ['label' => trans('texts.new_product'), 'url' => url('/products')],
        ['label' => trans('texts.new_warehouse'), 'url' => url('/warehouses')],
        ['label' => trans('texts.new_branch'), 'url' => url('/branches')],
        ['label' => trans('texts.new_item_store'), 'url' => url('/item_stores')],
        ['label' => trans('texts.new_item_transfer'), 'url' => url('/item_transfers')],
        ['label' => trans('texts.list_item_movements'), 'url' => url('/item_movements')],
        ])->split(); ?>

    <?php endif; ?>
<?php elseif($entityType == ENTITY_WAREHOUSE): ?>
    <?php if(Auth::user()->can('create', [ENTITY_WAREHOUSE])): ?>
        <?php echo DropdownButton::normal(trans('texts.maintenance'))
        ->withAttributes(['class'=>'maintenanceDropdown'])
        ->withContents([
        ['label' => trans('texts.new_product'), 'url' => url('/products')],
        ['label' => trans('texts.new_item_store'), 'url' => url('/item_stores')],
        ['label' => trans('texts.new_item_transfer'), 'url' => url('/item_transfers')],
        ['label' => trans('texts.list_item_movements'), 'url' => url('/item_movements')],
        ['label' => trans('texts.new_branch'), 'url' => url('/branches')],
        ])->split(); ?>

    <?php endif; ?>
<?php elseif($entityType == ENTITY_BRANCH): ?>
    <?php if(Auth::user()->can('create', [ENTITY_BRANCH])): ?>
        <?php echo DropdownButton::normal(trans('texts.maintenance'))
        ->withAttributes(['class'=>'maintenanceDropdown'])
        ->withContents([
        ['label' => trans('texts.new_user'), 'url' => url('/users')],
        ['label' => trans('texts.new_product'), 'url' => url('/products')],
        ['label' => trans('texts.new_item_store'), 'url' => url('/item_stores')],
        ['label' => trans('texts.new_item_transfer'), 'url' => url('/item_transfers')],
        ['label' => trans('texts.new_item_request'), 'url' => url('/item_requests')],
        ['label' => trans('texts.list_item_movements'), 'url' => url('/item_movements')],
        ['label' => trans('texts.new_warehouse'), 'url' => url('/warehouses')],
        ])->split(); ?>

    <?php endif; ?>
<?php endif; ?>