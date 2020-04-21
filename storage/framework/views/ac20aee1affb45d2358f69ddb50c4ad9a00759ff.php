<?php $__env->startSection('content'); ?>
    ##parent-placeholder-040f06fd774092478d450774f5ba30c5da78acc8##
    <?php echo Former::open($url)
    ->method($method)
    ->autocomplete('off')
    ->rules(['product_id' => 'required' ,'previous_store_id' => 'required' ,'current_store_id' => 'required','qty' => 'required|numeric','notes' => 'required' ])
    ->addClass('col-lg-10 col-lg-offset-1 main-form warn-on-exit'); ?>

    <?php if($itemTransfer): ?>
        <?php echo e(Former::populate($itemTransfer)); ?>

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
                    <?php echo Former::select('previous_store_id')->addOption('', '')
                    ->label(trans('texts.from_store_name'))->addGroupClass('store-select')
                    ->help(trans('texts.item_store_help') . ' | ' . link_to('/item_stores/', trans('texts.customize_options'))); ?>

                    <?php echo Former::select('current_store_id')->addOption('', '')
                    ->label(trans('texts.to_store_name'))->addGroupClass('store-select'); ?>

                    <?php echo Former::text('qty')->label('texts.qty'); ?>


                    <?php echo Former::label('allQty', trans('texts.allQty')); ?>

                    <?php echo e(Form::checkbox('allQty' , 1, false ),['class'=>'square']); ?>

                    <br/>
                    <?php echo Former::label('item_list', trans('texts.item_id')); ?>

                    <?php echo Form::select('product_id[]', ['1'=>'12'], null, ['class' => 'form-control padding-right', 'multiple' => 'multiple',]); ?>

                    <?php if($errors->has('product_id') ): ?>
                        <div class="alert alert-danger" role="alert">
                            One or more of the products you selected are empty/invalid. Please try again.
                        </div>
                    <?php endif; ?>
                    <br/>
                    <?php echo Former::textarea('notes')->rows(2); ?>

                </div>
            </div>
        </div>
    </div>

    <?php $__currentLoopData = Module::getOrdered(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $module): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php if(View::exists($module->alias . '::item_transfers.edit')): ?>
            <div class="row">
                <div class="col-lg-10 col-lg-offset-1">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title in-white">
                                <i class="fa fa-<?php echo e($module->icon); ?>"></i>
                                <?php echo e($module->name); ?>

                            </h3>
                        </div>
                        <div class="panel-body form-padding-right">
                            <?php if ($__env->exists($module->alias . '::item_transfers.edit')) echo $__env->make($module->alias . '::item_transfers.edit', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php if(Auth::user()->canCreateOrEdit(ENTITY_ITEM_STORE, $itemTransfer)): ?>
        <center class="buttons">
            <?php echo Button::normal(trans('texts.cancel'))->large()->asLinkTo(HTMLUtils::previousUrl('/item_transfers'))->appendIcon(Icon::create('remove-circle')); ?>

            <?php echo Button::success(trans('texts.save'))->submit()->large()->appendIcon(Icon::create('floppy-disk')); ?>

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
        var products = <?php echo $products; ?>;
        var previousStores = <?php echo $previousStores; ?>;
        var currentStores = <?php echo $currentStores; ?>;

        var productMap = {};
        var previousMap = {};
        var currentMap = {};
        $(function () {
            $('#qty').focus();
        });

        $(function () {
            var productSelect = $('select#product_id');
            var sourceStoreId = $('select#previous_store_id option:selected').val();
            if (sourceStoreId != '') {
                alert(sourceStoreId);
            }
            productSelect.append("<option value='" + 1 + "' selected>" + 'test' + "</option>");
//        previous store (from)
            var storeFromId = <?php echo e($previousStorePublicId ?: 0); ?>;
            var $storeSelect = $('select#previous_store_id');
            <?php if(Auth::user()->can('create', ENTITY_STORE)): ?>
            $storeSelect.append(new Option("<?php echo e(trans('texts.create_store')); ?>: $name", '-1'));
                    <?php endif; ?>
            for (var i = 0; i < previousStores.length; i++) {
                var storeFrom = previousStores[i];
                previousMap[storeFrom.public_id] = storeFrom;
                $storeSelect.append(new Option(getClientDisplayName(storeFrom), storeFrom.public_id));
            }
            <?php echo $__env->make('partials/entity_combobox', ['entityType' => ENTITY_STORE], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            if (storeFromId) {
                var storeFrom = previousMap[storeFromId];
                setComboboxValue($('.store-select'), storeFrom.public_id, storeFrom.name);
            }

//        current store (to)
            var storeToId = <?php echo e($currentStorePublicId ?: 0); ?>;
            var $store_toSelect = $('select#current_store_id');
            <?php if(Auth::user()->can('create', ENTITY_STORE)): ?>
            $store_toSelect.append(new Option("<?php echo e(trans('texts.create_store_to')); ?>: $name", '-1'));
                    <?php endif; ?>
            for (var i = 0; i < currentStores.length; i++) {
                var storeTo = currentStores[i];
                currentMap[storeTo.public_id] = storeTo;
                $store_toSelect.append(new Option(getClientDisplayName(storeTo), storeTo.public_id));
            }
            <?php echo $__env->make('partials/entity_combobox', ['entityType' => ENTITY_STORE_TO], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            if (storeToId) {
                var storeTo = currentMap[storeToId];
                setComboboxValue($('.store-to-select'), storeTo.public_id, storeTo.name);
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
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('header', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>