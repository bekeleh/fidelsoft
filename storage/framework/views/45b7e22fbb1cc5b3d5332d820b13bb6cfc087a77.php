<?php $__env->startSection('content'); ?>
    ##parent-placeholder-040f06fd774092478d450774f5ba30c5da78acc8##
    <?php echo Former::open($url)
    ->method($method)
    ->autocomplete('off')
    ->rules(['name' => 'required|max:255','unit_cost' => 'required|numeric','item_brand_id' => 'required|numeric','category_id' => 'required|numeric','tax_category_id' => 'required|numeric','unit_id' => 'required|numeric','notes' => 'required|string'])
    ->addClass('col-lg-10 col-lg-offset-1 main-form warn-on-exit'); ?>


    <?php if($product): ?>
        <?php echo e(Former::populate($product)); ?>

        <?php echo e(Former::populateField('unit_cost', Utils::roundSignificant($product->unit_cost))); ?>

    <?php endif; ?>
    <span style="display:none">
        <?php echo Former::text('action'); ?>

    </span>
    <div class="row">
        <div class="col-lg-10 col-lg-offset-1">
            <div class="panel panel-default">
                <div class="panel-body form-padding-right">
                    <!-- item code-->
                <?php echo Former::text('name')->label('texts.item_name'); ?>

                <?php echo Former::text('item_serial')->label('texts.item_serial'); ?>

                <?php echo Former::text('item_barcode')->label('texts.item_barcode'); ?>

                <?php echo Former::text('item_tag')->label('texts.item_tag'); ?>

                <?php echo Former::text('UPC')->label('texts.UPC'); ?>

                <?php echo Former::text('unit_cost')->label('unit_cost'); ?>

                <!-- item brand-->
                <?php echo Former::select('item_brand_id')
                ->placeholder(trans('texts.select_item_brand'))
                ->label(trans('texts.item_brand'))
                ->addGroupClass('item-brand-select')
                ->help(trans('texts.item_brand_help') . ' | ' . link_to('/item_brands/', trans('texts.customize_options'))); ?>

                <!-- category-->
                <?php echo Former::select('category_id')->addOption('','')
                ->label(trans('texts.category'))
                ->fromQuery($categories, 'name', 'id'); ?>

                <!-- tax category-->
                <?php echo Former::select('tax_category_id')->addOption('','')
                ->label(trans('texts.tax_category_name'))
                ->fromQuery($taxCategories, 'name', 'id'); ?>

                <!-- unit of measure-->
                <?php echo Former::select('unit_id')->addOption('','')
                ->label(trans('texts.unit_name'))
                ->fromQuery($units, 'name', 'id'); ?>

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
        var brands = <?php echo $itemBrands; ?>;
        var brandMap = {};

        $(function () {
            $('#name').focus();
        });

        $(function () {
            <!-- item brand -->
            var brandId = <?php echo e($itemBrandPublicId ?: 0); ?>;
            var $item_brandSelect = $('select#item_brand_id');
            <?php if(Auth::user()->can('create', ENTITY_ITEM_BRAND)): ?>
            $item_brandSelect.append(new Option("<?php echo e(trans('texts.create_item_brand')); ?>:$name", '-1'));
                    <?php endif; ?>
            for (var i = 0; i < brands.length; i++) {
                var brand = brands[i];
                brandMap[brand.public_id] = brand;
                $item_brandSelect.append(new Option(getClientDisplayName(brand), brand.public_id));
            }

            <?php echo $__env->make('partials/entity_combobox', ['entityType' => ENTITY_ITEM_BRAND], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            if (brandId) {
                var brand = brandMap[brandId];
                setComboboxValue($('.item-brand-select'), brand.public_id, brand.name);
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