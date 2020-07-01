<?php $__env->startSection('content'); ?>
    ##parent-placeholder-040f06fd774092478d450774f5ba30c5da78acc8##
    <?php echo Former::open($url)
    ->method($method)
    ->autocomplete('off')
    ->rules(['product_id' => 'required',
    'client_type_id' => 'required',
    'unit_price' => 'required|numeric',
    'start_date' => 'required|date',
     'end_date' => 'required|date',
     'notes' => 'required', ])
    ->addClass('col-lg-10 col-lg-offset-1 main-form warn-on-exit'); ?>

    <?php if($itemPrice): ?>
        <?php echo e(Former::populate($itemPrice)); ?>

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
                    <!-- product key -->
                <?php echo Former::select('product_id')->addOption('', '')
                ->label(trans('texts.product_key'))
                ->addGroupClass('product-select')
                ->help(trans('texts.item_help') . ' | ' . link_to('/products/', trans('texts.customize_options'))); ?>

                <!-- client type -->
                <?php echo Former::select('client_type_id')
                ->addOption('', '')
                ->fromQuery($clientTypes, 'name', 'id')
                ->label(trans('texts.client_type_name')); ?>

                <!-- item price -->
                    <?php echo Former::text('unit_price')->label('texts.unit_price'); ?>

                    <?php echo Former::text('start_date')
                    ->data_date_format(Session::get(SESSION_DATE_PICKER_FORMAT, DEFAULT_DATE_PICKER_FORMAT))
                    ->appendIcon('calendar')
                    ->addGroupClass('start_date'); ?>

                    <?php echo Former::text('end_date')
                    ->data_date_format(Session::get(SESSION_DATE_PICKER_FORMAT, DEFAULT_DATE_PICKER_FORMAT))
                    ->appendIcon('calendar')
                    ->addGroupClass('end_date'); ?>


                    <?php echo Former::textarea('notes')->rows(6); ?>

                </div>
            </div>
        </div>
    </div>
    <?php if(Auth::user()->canCreateOrEdit(ENTITY_ITEM_PRICE, $itemPrice)): ?>
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

        $(function () {
            $('#name').focus();
        });

        $(function () {
            <!-- product -->
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
            }<!-- /. product  -->

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