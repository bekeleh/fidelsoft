<?php if($entityType == ENTITY_USER): ?>
    <?php if(Auth::user()->can('create', [ENTITY_PERMISSION_GROUP])): ?>
        <?php echo DropdownButton::normal(trans('texts.maintenance'))
        ->withAttributes(['class'=>'maintenanceDropdown'])
        ->withContents([
        ['label' => trans('texts.new_permission_group'), 'url' => url('/permission_groups/create')],
        ])->split(); ?>

    <?php endif; ?>
<?php elseif($entityType == ENTITY_PERMISSION_GROUP): ?>
    <?php if(Auth::user()->can('create', [ENTITY_PERMISSION_GROUP])): ?>
        <?php echo DropdownButton::normal(trans('texts.maintenance'))
        ->withAttributes(['class'=>'maintenanceDropdown'])
        ->withContents([
        ['label' => trans('texts.new_user'), 'url' => url('/users/create')],
        ])->split(); ?>

    <?php endif; ?>
<?php elseif(in_array($entityType, [ENTITY_PROPOSAL_SNIPPET,ENTITY_PROPOSAL_CATEGORY])): ?>
    <?php if(Auth::user()->can('create', [ENTITY_PROPOSAL_CATEGORY])): ?>
        <?php echo DropdownButton::normal(trans('texts.maintenance'))
        ->withAttributes(['class'=>'maintenanceDropdown'])
        ->withContents([
        ['label' => trans('texts.new_proposal_category'), 'url' => url('/proposals/categories/create')],
        ])->split(); ?>

    <?php endif; ?>
<?php elseif(in_array($entityType, [ENTITY_SCHEDULE])): ?>
    <?php if(Auth::user()->can('create', [ENTITY_SCHEDULE])): ?>
        <?php echo DropdownButton::normal(trans('texts.maintenance'))
        ->withAttributes(['class'=>'maintenanceDropdown'])
        ->withContents([
            ['label' => trans('texts.list_scheduled_reports'), 'url' => url('/scheduled_reports')],
        ['label' => trans('texts.new_schedule_category'), 'url' => url('/schedule_categories/create')],
        ])->split(); ?>

    <?php endif; ?>
<?php endif; ?>
<?php if(in_array($entityType, [ENTITY_EXPENSE,ENTITY_EXPENSE_CATEGORY,ENTITY_RECURRING_EXPENSE, ENTITY_RECURRING_INVOICE,ENTITY_VENDOR])): ?>
    <?php if(Auth::user()->can('create', [ENTITY_EXPENSE_CATEGORY,ENTITY_RECURRING_EXPENSE, ENTITY_RECURRING_INVOICE,ENTITY_VENDOR])): ?>
        <?php echo DropdownButton::normal(trans('texts.maintenance'))
        ->withAttributes(['class'=>'maintenanceDropdown'])
        ->withContents([
        ['label' => trans('texts.new_recurring_expense'), 'url' => url('/recurring_expenses')],
        ['label' => trans('texts.new_expense_category'), 'url' => url('/expense_categories')],
        ])->split(); ?>

    <?php endif; ?>
<?php elseif(in_array($entityType, [ENTITY_PROPOSAL,ENTITY_PROPOSAL_TEMPLATE,ENTITY_PROPOSAL_SNIPPET])): ?>
    <?php if(Auth::user()->can('create', [ENTITY_PROPOSAL_TEMPLATE,ENTITY_PROPOSAL_SNIPPET])): ?>
        <?php echo DropdownButton::normal(trans('texts.maintenance'))
        ->withAttributes(['class'=>'maintenanceDropdown'])
        ->withContents([
        ['label' => trans('texts.new_proposal_template'), 'url' => url('/proposals/templates/create')],
        ['label' => trans('texts.new_proposal_snippet'), 'url' => url('/proposals/snippets/create')],
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
<?php elseif(in_array($entityType, [ENTITY_CLIENT])): ?>
    <?php if(Auth::user()->can('create', [ENTITY_CLIENT])): ?>
        <?php echo DropdownButton::normal(trans('texts.maintenance'))
        ->withAttributes(['class'=>'maintenanceDropdown'])
        ->withContents([
        ['label' => trans('texts.new_invoice'), 'url' => url('/invoices')],
        ['label' => trans('texts.new_quote'), 'url' => url('/quotes')],
        ['label' => trans('texts.new_credit'), 'url' => url('/credits')],
        ['label' => trans('texts.new_expense'), 'url' => url('/expenses')],
        ['label' => trans('texts.new_sale_type'), 'url' => url('/sale_types')],
        ['label' => trans('texts.new_hold_reason'), 'url' => url('/hold_reasons')],
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
<!-- inventory menu -->
<?php if(in_array($entityType, [ENTITY_PRODUCT])): ?>
    <?php if(Auth::user()->can('create', [ENTITY_PRODUCT])): ?>
        <?php echo DropdownButton::normal(trans('texts.maintenance'))
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
        ])->split(); ?>

    <?php endif; ?>
<?php elseif($entityType == ENTITY_ITEM_TRANSFER): ?>
    <?php if(Auth::user()->can('create', [ENTITY_ITEM_TRANSFER])): ?>
        <?php echo DropdownButton::normal(trans('texts.maintenance'))
        ->withAttributes(['class'=>'maintenanceDropdown'])
        ->withContents([
        ['label' => trans('texts.new_item_store'), 'url' => url('/item_stores')],
        ['label' => trans('texts.new_store'), 'url' => url('/stores')],
        ['label' => trans('texts.list_item_movements'), 'url' => url('/item_movements')],
        ])->split(); ?>

    <?php endif; ?>
<?php elseif($entityType == ENTITY_ITEM_MOVEMENT): ?>
    <?php if(Auth::user()->can('create', [ENTITY_ITEM_MOVEMENT])): ?>
        <?php echo DropdownButton::normal(trans('texts.maintenance'))
        ->withAttributes(['class'=>'maintenanceDropdown'])
        ->withContents([
        ['label' => trans('texts.new_item_store'), 'url' => url('/item_stores')],
        ['label' => trans('texts.new_store'), 'url' => url('/stores')],
        ['label' => trans('texts.new_item_transfer'), 'url' => url('/item_transfers')],
        ])->split(); ?>

    <?php endif; ?>
<?php elseif($entityType == ENTITY_ITEM_STORE): ?>
    <?php if(Auth::user()->can('create', [ENTITY_ITEM_STORE])): ?>
        <?php echo DropdownButton::normal(trans('texts.maintenance'))
        ->withAttributes(['class'=>'maintenanceDropdown'])
        ->withContents([
        ['label' => trans('texts.new_product'), 'url' => url('/items')],
        ['label' => trans('texts.new_store'), 'url' => url('/stores')],
        ['label' => trans('texts.new_item_transfer'), 'url' => url('/item_transfers')],
        ['label' => trans('texts.new_item_movement'), 'url' => url('/item_movements')],
        ['label' => trans('texts.new_item_price'), 'url' => url('/item_prices')],
        ])->split(); ?>

    <?php endif; ?>
<?php elseif($entityType == ENTITY_ITEM_PRICE): ?>
    <?php if(Auth::user()->can('create', [ENTITY_ITEM_PRICE])): ?>
        <?php echo DropdownButton::normal(trans('texts.maintenance'))
        ->withAttributes(['class'=>'maintenanceDropdown'])
        ->withContents([
        ['label' => trans('texts.new_product'), 'url' => url('/items')],
        ['label' => trans('texts.new_store'), 'url' => url('/stores')],
        ['label' => trans('texts.new_item_transfer'), 'url' => url('/item_transfers')],
        ['label' => trans('texts.new_item_movement'), 'url' => url('/item_movements')],
        ['label' => trans('texts.new_sale_type'), 'url' => url('/sale_types')],
        ])->split(); ?>

    <?php endif; ?>
<?php elseif($entityType == ENTITY_STORE): ?>
    <?php if(Auth::user()->can('create', [ENTITY_STORE])): ?>
        <?php echo DropdownButton::normal(trans('texts.maintenance'))
        ->withAttributes(['class'=>'maintenanceDropdown'])
        ->withContents([
        ['label' => trans('texts.new_product'), 'url' => url('/items')],
        ['label' => trans('texts.new_item_store'), 'url' => url('/item_stores')],
        ['label' => trans('texts.new_item_transfer'), 'url' => url('/item_transfers')],
        ['label' => trans('texts.new_item_movement'), 'url' => url('/item_movements')],
        ])->split(); ?>

    <?php endif; ?>
<?php elseif($entityType == ENTITY_SALE_TYPE): ?>
    <?php if(Auth::user()->can('create', [ENTITY_SALE_TYPE])): ?>
        <?php echo DropdownButton::normal(trans('texts.maintenance'))
        ->withAttributes(['class'=>'maintenanceDropdown'])
        ->withContents([
        ['label' => trans('texts.new_product'), 'url' => url('/items')],
        ['label' => trans('texts.new_item_price'), 'url' => url('/item_prices')],
        ])->split(); ?>

    <?php endif; ?>
<?php endif; ?>