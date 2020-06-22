<?php $__env->startSection('content'); ?>
    ##parent-placeholder-040f06fd774092478d450774f5ba30c5da78acc8##
    <?php echo Former::open($url)
    ->method($method)
    ->autocomplete('off')
    ->rules(['bin' => 'required|max:90','product_id' => 'required' ,'store_id' => 'required','qty' => 'required|numeric','reorder_level' => 'required|numeric','notes' => 'required' ])
    ->addClass('col-lg-10 col-lg-offset-1 main-form warn-on-exit'); ?>

    <?php if($itemStore): ?>
        <?php echo e(Former::populate($itemStore)); ?>

        <?php echo e(Former::populateField('qty','0.00')); ?>

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
                    <?php echo Former::select('product_id')->addOption('', '')
                    ->label(trans('texts.product_key'))
                    ->addGroupClass('product-select')
                    ->help(trans('texts.item_help') . ' | ' . link_to('/products/', trans('texts.customize_options'))); ?>

                    <?php echo Former::select('store_id')->addOption('', '')
                    ->label(trans('texts.store_name'))->addGroupClass('store-select')
                    ->help(trans('texts.store_help') . ' | ' . link_to('/stores/', trans('texts.customize_options'))); ?>


                    <?php echo Former::text('bin')->label('texts.bin'); ?>

                    <?php echo Former::text('qty')->label('texts.qty'); ?>

                    <?php echo Former::text('reorder_level')->label('texts.reorder_level'); ?>

                    <?php echo Former::text('EOQ')->label('texts.EOQ'); ?>

                    <?php echo Former::textarea('notes')->rows(6); ?>

                </div>
            </div>
        </div>
    </div>

    <?php $__currentLoopData = Module::getOrdered(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $module): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php if(View::exists($module->alias . '::item_stores.edit')): ?>
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
                            <?php if ($__env->exists($module->alias . '::item_stores.edit')) echo $__env->make($module->alias . '::item_stores.edit', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php if(Auth::user()->canCreateOrEdit(ENTITY_ITEM_STORE, $itemStore)): ?>
        <center class="buttons">
            <?php echo Button::normal(trans('texts.cancel'))->large()->asLinkTo(HTMLUtils::previousUrl('/item_stores'))->appendIcon(Icon::create('remove-circle')); ?>

            <?php echo Button::success(trans('texts.save'))->submit()->large()->appendIcon(Icon::create('floppy-disk')); ?>

            <?php if($itemStore): ?>
                <?php echo DropdownButton::normal(trans('texts.more_actions'))
                ->withContents($itemStore->present()->moreActions())
                ->large()
                ->dropup(); ?>

            <?php endif; ?>
        </center>
    <?php endif; ?>
    <?php echo Former::close(); ?>

    <script type="text/javascript">
        var products = <?php echo $products; ?>;
        var stores = <?php echo $stores; ?>;

        var productMap = {};
        var storeMap = {};
        $(function () {
            $('#bin').focus();
        });

        $(function () {
//          append product
            var productId = <?php echo e($productPublicId ?: 0); ?>;
            var $productSelect = $('select#product_id');
            <?php if(Auth::user()->can('create', ENTITY_PRODUCT)): ?>
            $productSelect.append(new Option("<?php echo e(trans('texts.create_product')); ?>: $product_key", '-1'));
                    <?php endif; ?>
            for (var i = 0; i < products.length; i++) {
                var product = products[i];
                productMap[product.public_id] = product;
                $productSelect.append(new Option(product.product_key, product.public_id));
            }
            <?php echo $__env->make('partials/entity_combobox', ['entityType' => ENTITY_PRODUCT], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            if (productId) {
                var product = productMap[productId];
                setComboboxValue($('.product-select'), product.public_id, product.product_key);
            }
//        default store
            var storeId = <?php echo e($storePublicId ?: 0); ?>;
            var $storeSelect = $('select#store_id');
            <?php if(Auth::user()->can('create', ENTITY_STORE)): ?>
            $storeSelect.append(new Option("<?php echo e(trans('texts.create_store')); ?>: $name", '-1'));
                    <?php endif; ?>
            for (var i = 0; i < stores.length; i++) {
                var store = stores[i];
                storeMap[store.public_id] = store;
                $storeSelect.append(new Option(store.name, store.public_id));
            }
            <?php echo $__env->make('partials/entity_combobox', ['entityType' => ENTITY_STORE], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            if (storeId) {
                var store = storeMap[storeId];
                setComboboxValue($('.store-select'), store.public_id, store.name);
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