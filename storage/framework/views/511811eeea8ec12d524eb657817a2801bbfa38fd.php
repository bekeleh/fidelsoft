<?php $__env->startSection('content'); ?>
    ##parent-placeholder-040f06fd774092478d450774f5ba30c5da78acc8##
    <?php echo Former::open($url)
    ->method($method)
    ->autocomplete('off')
    ->rules(['product_id' => 'required','sale_type_id' => 'required','price' => 'required|numeric','start_date' => 'required|date', 'end_date' => 'required|date', ])
    ->addClass('col-lg-10 col-lg-offset-1 main-form warn-on-exit'); ?>

    <!-- notification -->
    <?php echo $__env->make('notifications', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
    <?php if($itemPrice): ?>
        <?php echo e(Former::populate($itemPrice)); ?>

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
                    <?php echo Former::select('sale_type_id')->addOption('', '')
                    ->label(trans('texts.sale_type'))
                    ->addGroupClass('sale-type-select'); ?>

                    <?php echo Former::select('product_id')->addOption('', '')
                    ->label(trans('texts.product_name'))
                    ->addGroupClass('product-select'); ?>

                    <?php echo Former::text('price')->label('texts.item_price'); ?>


                    <?php echo Former::text('start_date')
               ->data_date_format(Session::get(SESSION_DATE_PICKER_FORMAT, DEFAULT_DATE_PICKER_FORMAT))
               ->appendIcon('calendar')
               ->addGroupClass('start_date'); ?>

                    <?php echo Former::text('end_date')
                    ->data_date_format(Session::get(SESSION_DATE_PICKER_FORMAT, DEFAULT_DATE_PICKER_FORMAT))
                    ->addGroupClass('end_date')
                    ->appendIcon('calendar'); ?>


                    <?php echo Former::textarea('notes')->rows(6); ?>

                </div>
            </div>
        </div>
    </div>

    <?php $__currentLoopData = Module::getOrdered(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $module): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php if(View::exists($module->alias . '::item_prices.edit')): ?>
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
                            <?php if ($__env->exists($module->alias . '::item_prices.edit')) echo $__env->make($module->alias . '::item_prices.edit', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php if(Auth::user()->canCreateOrEdit(ENTITY_SALE_TYPE, $itemPrice)): ?>
        <center class="buttons">
            <?php echo Button::normal(trans('texts.cancel'))->large()->asLinkTo(HTMLUtils::previousUrl('/item_prices'))->appendIcon(Icon::create('remove-circle')); ?>

            <?php echo Button::success(trans('texts.save'))->submit()->large()->appendIcon(Icon::create('floppy-disk')); ?>

            <?php if($itemPrice): ?>
                <?php echo DropdownButton::normal(trans('texts.more_actions'))
                ->withContents($itemPrice->present()->moreActions())
                ->large()
                ->dropup(); ?>

            <?php endif; ?>
        </center>
    <?php endif; ?>
    <?php echo Former::close(); ?>

    <script type="text/javascript">
        var products = <?php echo $products; ?>;
        var productMap = {};
        <!-- types type -->
        var types = <?php echo $saleTypes; ?>;
        var typeMap = {};

        $(function () {
            $('#name').focus();
        });

        $(function () {
            <!-- product -->
            var productId = <?php echo e($productPublicId ?: 0); ?>;
            var $productSelect = $('select#product_id');
            <?php if(Auth::user()->can('create', ENTITY_PRODUCT)): ?>
            $productSelect.append(new Option("<?php echo e(trans('texts.create_product')); ?>: $name", '-1'));
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
            }<!-- /. product  -->

            var typeId = <?php echo e($saleTypePublicId ?: 0); ?>;
            var $sale_typeSelect = $('select#sale_type_id');
            <?php if(Auth::user()->can('create', ENTITY_SALE_TYPE)): ?>
            $sale_typeSelect.append(new Option("<?php echo e(trans('texts.create_sale_type')); ?>: $name", '-1'));
                    <?php endif; ?>
            for (var i = 0; i < types.length; i++) {
                var type = types[i];
                typeMap[type.public_id] = type;
                $sale_typeSelect.append(new Option(type.name, type.public_id));
            }
            <?php echo $__env->make('partials/entity_combobox', ['entityType' => ENTITY_SALE_TYPE], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            if (typeId) {
                var type = typeMap[typeId];
                setComboboxValue($('.sale-type-select'), type.public_id, type.name);
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

        $('#start_date').datepicker('update', '<?php echo e($itemPrice ? Utils::fromSqlDate($itemPrice->start_date) : ''); ?>');
        $('#end_date').datepicker('update', '<?php echo e($itemPrice ? Utils::fromSqlDate($itemPrice->end_date) : ''); ?>');
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('header', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>