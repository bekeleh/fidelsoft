<?php $__env->startSection('content'); ?>
    ##parent-placeholder-040f06fd774092478d450774f5ba30c5da78acc8##
    <?php echo Former::open($url)
        ->method($method)
        ->autocomplete('off')
        ->rules([
        'product_id' => 'required',
        'status_id' => 'required',
        'previous_warehouse_id' => 'required' ,
        'current_warehouse_id' => 'required',
        'notes' => 'required'
        ])
        ->addClass('col-lg-10 col-lg-offset-1 main-form warn-on-exit'); ?>

    <?php if($itemTransfer): ?>
        <?php echo e(Former::populate($itemTransfer)); ?>

        <div style="display:none">
            <?php echo Former::text('public_id'); ?>

        </div>
    <?php endif; ?>
    <span style="display:none">
        <?php echo Former::text('action'); ?>

    </span>
    <div class="row">
        <div class="col-lg-10 col-lg-offset-1">
            <div class="panel panel-default">
                <div class="panel-body form-padding-right">
                    <!-- status -->
                
                
                
                

                <!-- from store -->
                <?php echo Former::select('previous_warehouse_id')->addOption('', '')
                ->onchange('selectProductAction()')
                ->label(trans('texts.from_warehouse'))->addGroupClass('warehouse-select')
                ->help(trans('texts.warehouse_help') . ' | ' . link_to('/warehouses/', trans('texts.customize_options'))); ?>

                <!-- to warehouse -->
                <?php echo Former::select('current_warehouse_id')->addOption('', '')
                ->label(trans('texts.to_warehouse'))->addGroupClass('warehouse-to-select'); ?>

                <!-- list item -->
                <?php echo $__env->make('partials.select_product', ['label'=>'product_id','field_name'=>'product_id','check_item_name'=>'transfer_all_item'], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                <!-- transfer all qty -->
                <?php echo Former::checkbox('transfer_all_item')->label(trans('texts.allQty'))
                ->value(1)->onchange('transferAllQtyChecked()'); ?>

                <!-- qty -->
                <?php echo Former::text('qty')->label('texts.qty')->help('texts.item_qty_help'); ?>


                <!-- NOTES -->
                    <?php echo Former::textarea('notes')->rows(4); ?>

                </div>
            </div>
        </div>
    </div>

    <?php if(Auth::user()->canCreateOrEdit(ENTITY_ITEM_TRANSFER, $itemTransfer)): ?>
        <center class="buttons">
            <?php echo Button::normal(trans('texts.cancel'))->large()->asLinkTo(HTMLUtils::previousUrl('/item_transfers'))->appendIcon(Icon::create('remove-circle')); ?>

            <?php if(!$itemTransfer): ?>
                <?php echo Button::success(trans('texts.save'))->submit()->large()->appendIcon(Icon::create('floppy-disk')); ?>

            <?php endif; ?>
            <?php if($itemTransfer): ?>
                <?php echo DropdownButton::normal(trans('texts.more_actions'))
                ->withContents($itemTransfer->present()->moreActions())
                ->large()
                ->dropup(); ?>

            <?php endif; ?>
        </center>
    <?php endif; ?>

    <?php echo Former::close(); ?>


    <script type="text/javascript">
        var $productModel = $('#product_id');
        var statuses = <?php echo isset($statuses) ? $statuses:null; ?>;
        var previousWarehouses = <?php echo isset($previousWarehouses) ? $previousWarehouses:null; ?>;
        var currentWarehouses = <?php echo isset($currentWarehouses) ? $currentWarehouses:null; ?>;

        var statusMap = {};
        var previousMap = {};
        var currentMap = {};
        $(function () {
            $('#qty').focus();
        });
        $(function () {
            // status
            var statusId = <?php echo e($statusPublicId ?: 0); ?>;
            var $statusSelect = $('select#status_id');
            <?php if(Auth::user()->can('create', ENTITY_STATUS)): ?>
            $statusSelect.append(new Option("<?php echo e(trans('texts.create_status')); ?>: $name", '-1'));
                    <?php endif; ?>
            for (var i = 0; i < statuses.length; i++) {
                var status = statuses[i];
                statusMap[status.public_id] = status;
                $statusSelect.append(new Option(status.name, status.public_id));
            }
            <?php echo $__env->make('partials/entity_combobox', ['entityType' => ENTITY_STATUS], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            if (statusId) {
                var status = statusMap[statusId];
                setComboboxValue($('.status-select'), status.public_id, status.name);
            }

            // from warehouse
            var warehouseFromId = <?php echo e($previousWarehousePublicId ?: 0); ?>;
            var $warehouseSelect = $('select#previous_warehouse_id');
            <?php if(Auth::user()->can('create', ENTITY_WAREHOUSE)): ?>
            $warehouseSelect.append(new Option("<?php echo e(trans('texts.create_warehouse')); ?>: $name", '-1'));
                    <?php endif; ?>
            for (var i = 0; i < previousWarehouses.length; i++) {
                var warehouseFrom = previousWarehouses[i];
                previousMap[warehouseFrom.public_id] = warehouseFrom;
                $warehouseSelect.append(new Option(warehouseFrom.name, warehouseFrom.public_id));
            }
            <?php echo $__env->make('partials/entity_combobox', ['entityType' => ENTITY_WAREHOUSE], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            if (warehouseFromId) {
                var warehouseFrom = previousMap[warehouseFromId];
                setComboboxValue($('.warehouse-select'), warehouseFrom.public_id, warehouseFrom.name);
            }

            //  current warehouse (to)
            var warehouseToId = <?php echo e($currentWarehousePublicId ?: 0); ?>;
            var $warehouse_toSelect = $('select#current_warehouse_id');
            <?php if(Auth::user()->can('create', ENTITY_WAREHOUSE)): ?>
            $warehouse_toSelect.append(new Option("<?php echo e(trans('texts.create_warehouse_to')); ?>: $name", '-1'));
                    <?php endif; ?>
            for (var i = 0; i < currentWarehouses.length; i++) {
                var warehouseTo = currentWarehouses[i];
                currentMap[warehouseTo.public_id] = warehouseTo;
                $warehouse_toSelect.append(new Option(warehouseTo.name, warehouseTo.public_id));
            }
            <?php echo $__env->make('partials/entity_combobox', ['entityType' => ENTITY_WAREHOUSE_TO], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            if (warehouseToId) {
                var warehouseTo = currentMap[warehouseToId];
                setComboboxValue($('.warehouse-to-select'), warehouseTo.public_id, warehouseTo.name);
            }
        });

        function selectProductAction() {
            var $sourceWarehouseId = $('select#previous_warehouse_id').val();
            if ($sourceWarehouseId != '' && $productModel != null) {
                $productModel.empty();
                onSourceWarehouseChanges($productModel, $sourceWarehouseId);
            }
        }

        function onSourceWarehouseValueChanges() {
            // var $sourceWarehouseId = $('select#previous_warehouse_id').val();
            // if ($sourceWarehouseId != '' && $productModel != null) {
            //     $productModel.empty();
            //     onSourceWarehouseChanges($productModel, $sourceWarehouseId);
            // }
        }

        // find items in the selected store.
        function onSourceWarehouseChanges($productModel, $sourceWarehouseId, $item_checked = null) {
            if ($sourceWarehouseId != null && $sourceWarehouseId != '') {
                $.ajax({
                    url: '<?php echo e(URL::to('item_stores/item_list')); ?>',
                    type: 'POST',
                    dataType: "json",
                    data: 'warehouse_id=' + $sourceWarehouseId,
                    success: function (result) {
                        if (result.success) {
                            appendItems($productModel, result.data);
                        } else {
                            swal(<?php echo json_encode(trans('texts.item_does_not_exist')); ?>);
                        }
                    },
                    error: function () {
                        swal(<?php echo json_encode(trans('texts.item_does_not_exist')); ?>);
                    },
                });
            }
        }

        function appendItems($productModel, $data) {
            if ($productModel != '' && $data != '') {
                if ($data.length > 0) {
                    $productModel.empty();
                    for (var i in $data) {
                        var row = $data[i];
                        $productModel.append("<option value='" + row.id + "' selected>" + row.name + "</option>");
                    }
                }
            }
        }

        function transferAllQtyChecked() {
            var $transferAllQty = $('#transfer_all_item').val();

            if (document.getElementById('transfer_all_item').checked) {
                document.getElementById('qty').value = '';
                document.getElementById('qty').disabled = true;
            } else {
                document.getElementById('qty').disabled = false;
            }
        }

        function submitAction(action) {
            $('#action').val(action);
            $('.main-form').submit();
        }

        function onDeleteClick() {
            sweetConfirm(function () {
                submitAction('delete');
            });
        }
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('header', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>