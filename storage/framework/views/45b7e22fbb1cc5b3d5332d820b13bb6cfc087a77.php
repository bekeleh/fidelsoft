<?php $__env->startSection('content'); ?>
    ##parent-placeholder-040f06fd774092478d450774f5ba30c5da78acc8##
    <?php echo Former::open($url)
    ->method($method)
    ->autocomplete('off')
    ->rules(['name' => 'required|max:255',
    'cost' => 'required|numeric',
    'item_brand_id' => 'required|numeric',
    'item_type_id' => 'required|numeric',
    'tax_category_id' => 'required|numeric',
    'unit_id' => 'required|numeric',
    'notes' => 'required|string'])
    ->addClass('col-lg-10 col-lg-offset-1 main-form warn-on-exit'); ?>


    <?php if($product): ?>
        <?php echo e(Former::populate($product)); ?>

        <?php echo e(Former::populateField('cost', Utils::roundSignificant($product->cost))); ?>

    <?php endif; ?>
    <span style="display:none">
        <?php echo Former::text('action'); ?>

    </span>
    <div class="row">
        <div class="col-lg-10 col-lg-offset-1">
            <div class="panel panel-default">
                <div class="panel-body form-padding-right">
                    <!-- item code-->
                <?php echo Former::text('product_key')->label(trans('texts.product_key')); ?>

                <?php echo Former::text('item_serial')->label(trans('texts.item_serial')); ?>

                <?php echo Former::text('item_barcode')->label(trans('texts.item_barcode')); ?>

                <?php echo Former::text('item_tag')->label(trans('texts.item_tag')); ?>

                <?php echo Former::text('upc')->label(trans('texts.upc')); ?>

                <?php echo Former::text('cost')->label(trans('texts.cost')); ?>

                <!-- item brand-->
                <?php echo Former::select('item_brand_id')
                ->placeholder(trans('texts.select_item_brand'))
                ->label(trans('texts.item_brand'))
                ->addGroupClass('item-brand-select')
                ->help(trans('texts.item_brand_help') . ' | ' . link_to('/item_brands/', trans('texts.customize_options'))); ?>

                <!-- item type product/service-->
                <?php echo Former::select('item_type_id')
                ->fromQuery($itemTypes, 'name', 'id')
                ->addOption('','')
                ->label(trans('texts.item_type_name')); ?>

                <!-- tax category-->
                <?php echo Former::select('tax_category_id')
                ->fromQuery($taxCategories, 'name', 'id')
                ->addOption('','')
                ->label(trans('texts.tax_category_name')); ?>

                <!-- unit of measure-->
                <?php echo Former::select('unit_id')
                ->fromQuery($units, 'name', 'id')
                ->addOption('','')
                ->label(trans('texts.unit_name')); ?>

                <!-- product notes -->
                    <?php echo Former::textarea('notes')->rows(6); ?>

                    <?php echo $__env->make('partials/custom_fields', ['entityType' => ENTITY_PRODUCT], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                    <?php if($account->invoice_item_taxes): ?>
                        <?php echo $__env->make('partials.tax_rates', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <?php if(Auth::user()->canCreateOrEdit(ENTITY_PRODUCT, $product)): ?>
        <center class="buttons">
            <?php echo Button::normal(trans('texts.cancel'))->large()->asLinkTo(HTMLUtils::previousUrl('/products'))->appendIcon(Icon::create('remove-circle')); ?>

            <?php echo Button::success(trans('texts.save'))->submit()->large()->appendIcon(Icon::create('floppy-disk')); ?>

            <?php if($product): ?>
                <?php echo DropdownButton::normal(trans('texts.more_actions'))
                ->withContents($product->present()->moreActions())
                ->large()
                ->dropup(); ?>

            <?php endif; ?>
        </center>
    <?php endif; ?>

    <?php echo Former::close(); ?>

    <script type="text/javascript">
        var itemBrands = <?php echo $itemBrands; ?>;
        var itemBrandMap = {};

        $(function () {
            $('#name').focus();
        });

        $(function () {
            <!-- item brand -->
            var itemBrandId = <?php echo e($itemBrandPublicId ?: 0); ?>;
            var $item_brandSelect = $('select#item_brand_id');
            <?php if(Auth::user()->can('create', ENTITY_ITEM_BRAND)): ?>
            $item_brandSelect.append(new Option("<?php echo e(trans('texts.create_item_brand')); ?>:$name", '-1'));
                    <?php endif; ?>
            for (var i = 0; i < itemBrands.length; i++) {
                var itemBrand = itemBrands[i];
                itemBrandMap[itemBrand.public_id] = itemBrand;
                $item_brandSelect.append(new Option(getClientDisplayName(itemBrand), itemBrand.public_id));
            }

            <?php echo $__env->make('partials/entity_combobox', ['entityType' => ENTITY_ITEM_BRAND], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            if (itemBrandId) {
                var itemBrand = itemBrandMap[itemBrandId];
                setComboboxValue($('.item-brand-select'), itemBrand.public_id, itemBrand.name);
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
        });

    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('header', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>