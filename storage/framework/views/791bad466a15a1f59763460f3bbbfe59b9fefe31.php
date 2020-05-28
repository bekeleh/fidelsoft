<?php $__env->startSection('content'); ?>
    ##parent-placeholder-040f06fd774092478d450774f5ba30c5da78acc8##
    <?php echo Former::open($url)
    ->method($method)
    ->autocomplete('off')
    ->rules(['product_id' => 'required', 'status_id' => 'required', 'store_id' => 'required', 'department_id' => 'required', 'required_date' => 'required', 'qty' => 'required', 'notes' => 'required' ])
    ->addClass('col-lg-10 col-lg-offset-1 main-form warn-on-exit'); ?>

    <?php if($itemRequest): ?>
        <?php echo e(Former::populate($itemRequest)); ?>

        <div style="display:none">
            <?php echo Former::text('public_id'); ?>

        </div>
    <?php endif; ?>
    <span style="display:none">
<?php echo Former::text('public_id'); ?>

        <?php echo Former::text('action'); ?>

</span>
    <div class="row">
        <div class="col-lg-10 col-lg-offset-1">
            <div class="panel panel-default">
                <div class="panel-body form-padding-right">
                    <!-- product -->
                <?php echo Former::select('product_id')->addOption('', '')
                ->label(trans('texts.product_name'))->addGroupClass('product-select')
                ->help(trans('texts.product_help') . ' | ' . link_to('/products/', trans('texts.customize_options'))); ?>

                <!-- department -->
                <?php echo Former::select('department_id')->addOption('', '')
                ->label(trans('texts.department_name'))->addGroupClass('department-select')
                ->help(trans('texts.department_help') . ' | ' . link_to('/departments/', trans('texts.customize_options'))); ?>

                <!-- store -->
                <?php echo Former::select('store_id')->addOption('', '')
                ->label(trans('texts.store_name'))->addGroupClass('store-select')
                ->help(trans('texts.store_help') . ' | ' . link_to('/stores/', trans('texts.customize_options'))); ?>

                <!-- qty -->
                <?php echo Former::text('qty')->label('texts.qty'); ?>

                <!-- required date -->
                
                <?php echo Former::text('required_date')
                    ->data_date_format(Session::get(SESSION_DATE_PICKER_FORMAT, DEFAULT_DATE_PICKER_FORMAT))
                    ->appendIcon('calendar')
                    ->addGroupClass('required_date'); ?>


                <!-- NOTES -->
                    <?php echo Former::textarea('notes')->rows(4); ?>

                </div>
            </div>
        </div>
    </div>

    <?php if(Auth::user()->canCreateOrEdit(ENTITY_ITEM_REQUEST, $itemRequest)): ?>
        <center class="buttons">
            <?php echo Button::normal(trans('texts.cancel'))->large()->asLinkTo(HTMLUtils::previousUrl('/item_requests'))->appendIcon(Icon::create('remove-circle')); ?>

            <?php echo Button::success(trans('texts.save'))->submit()->large()->appendIcon(Icon::create('floppy-disk')); ?>

            <?php if($itemRequest): ?>
                <?php echo DropdownButton::normal(trans('texts.more_actions'))
                ->withContents($itemRequest->present()->moreActions())
                ->large()
                ->dropup(); ?>

            <?php endif; ?>
        </center>
    <?php endif; ?>

    <?php echo Former::close(); ?>


    <script type="text/javascript">
        var products = <?php echo $products; ?>;
        var departments = <?php echo $departments; ?>;
        var statuses = <?php echo $statuses; ?>;
        var stores = <?php echo $stores; ?>;

        var productMap = {};
        var departmentMap = {};
        var storeMap = {};
        var statusMap = {};

        $(function () {
// product
            var productId = <?php echo e($productPublicId ?: 0); ?>;
            var $productSelect = $('select#product_id');
            <?php if(Auth::user()->can('create', ENTITY_PRODUCT)): ?>
            $productSelect.append(new Option("<?php echo e(trans('texts.create_product')); ?> : $name", '-1'));
                    <?php endif; ?>
            for (var i = 0; i < products.length; i++) {
                var product = products[i];
                productMap[product.public_id] = product;
                $productSelect.append(new Option(getClientDisplayName(product), product.public_id));
            }
            <?php echo $__env->make('partials/entity_combobox', ['entityType' => ENTITY_PRODUCT], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            if (productId) {
                var product = productMap[productId];
                setComboboxValue($('.product-select'), product.public_id, product.name);
            }
// department
            var departmentId = <?php echo e($departmentPublicId ?: 0); ?>;
            var $departmentSelect = $('select#department_id');
            <?php if(Auth::user()->can('create', ENTITY_DEPARTMENT)): ?>
            $departmentSelect.append(new Option("<?php echo e(trans('texts.create_department')); ?>: $name", '-1'));
                    <?php endif; ?>
            for (var i = 0; i < departments.length; i++) {
                var department = departments[i];
                departmentMap[department.public_id] = department;
                $departmentSelect.append(new Option(getClientDisplayName(department), department.public_id));
            }
            <?php echo $__env->make('partials/entity_combobox', ['entityType' => ENTITY_DEPARTMENT], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            if (departmentId) {
                var department = departmentMap[departmentId];
                setComboboxValue($('.department-select'), department.public_id, department.name);
            }
// store
            var storeId = <?php echo e($storePublicId ?: 0); ?>;
            var $storeSelect = $('select#store_id');
            <?php if(Auth::user()->can('create', ENTITY_STORE)): ?>
            $storeSelect.append(new Option("<?php echo e(trans('texts.create_store')); ?>: $name", '-1'));
                    <?php endif; ?>
            for (var i = 0; i < stores.length; i++) {
                var store = stores[i];
                storeMap[store.public_id] = store;
                $storeSelect.append(new Option(getClientDisplayName(store), store.public_id));
            }
            <?php echo $__env->make('partials/entity_combobox', ['entityType' => ENTITY_STORE], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            if (storeId) {
                var store = storeMap[storeId];
                setComboboxValue($('.store-select'), store.public_id, store.name);
            }
            // status
            var statusId = <?php echo e($statusPublicId ?: 0); ?>;
            var $statusSelect = $('select#status_id');
            <?php if(Auth::user()->can('create', ENTITY_STATUS)): ?>
            $statusSelect.append(new Option("<?php echo e(trans('texts.create_status')); ?>: $name", '-1'));
                    <?php endif; ?>
            for (var i = 0; i < statuses.length; i++) {
                var status = statuses[i];
                statusMap[status.public_id] = status;
                $statusSelect.append(new Option(getClientDisplayName(status), status.public_id));
            }
            <?php echo $__env->make('partials/entity_combobox', ['entityType' => ENTITY_STATUS], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            if (statusId) {
                var status = statusMap[statusId];
                setComboboxValue($('.status-select'), status.public_id, status.name);
            }

        });

        function submitAction(action) {
            $('#action').val(action);
            $('.main-form').submit();
        }

        function onDeleteClick() {
            sweetConfirm(function () {
                submitAction('delete');
            });
        }

        $('#required_date').datepicker('update', '<?php echo e($itemRequest ? Utils::fromSqlDate($itemRequest->required_date) : ''); ?>');
        $('#dispatch_date').datepicker('update', '<?php echo e($itemRequest ? Utils::fromSqlDate($itemRequest->dispatch_date) : ''); ?>');
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('header', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>