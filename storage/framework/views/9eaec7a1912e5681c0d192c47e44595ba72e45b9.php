<?php $__env->startSection('content'); ?>
    ##parent-placeholder-040f06fd774092478d450774f5ba30c5da78acc8##
    <?php echo Former::open($url)
    ->method($method)
    ->autocomplete('off')
    ->rules(['name' => 'required|max:255','item_cost' => 'required|numeric','item_category_id' => 'required|numeric','unit_id' => 'required|numeric','notes' => 'required|string'])
    ->addClass('col-lg-10 col-lg-offset-1 main-form warn-on-exit'); ?>

    <?php if($product): ?>
        <?php echo e(Former::populate($product)); ?>

        <?php echo e(Former::populateField('item_cost', Utils::roundSignificant($product->item_cost))); ?>

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
                    <?php echo Former::text('name')->label('texts.item_name'); ?>


                    <?php echo Former::select('item_category_id')->addOption('', '')
                    ->label(trans('texts.item_category'))
                    ->addGroupClass('item-category-select'); ?>


                    <?php echo Former::select('unit_id')->addOption('', '')
                    ->label(trans('texts.unit'))
                    ->addGroupClass('unit-select'); ?>


                    <?php echo Former::text('barcode')->label('texts.barcode'); ?>

                    <?php echo Former::text('item_tag')->label('texts.item_tag'); ?>

                    <?php echo Former::text('item_cost')->label('item_cost'); ?>

                    <?php echo Former::textarea('notes')->rows(6); ?>

                    <?php echo $__env->make('partials/custom_fields', ['entityType' => ENTITY_PRODUCT], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                    <?php if($account->invoice_item_taxes): ?>
                        <?php echo $__env->make('partials.tax_rates', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <?php $__currentLoopData = Module::getOrdered(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $module): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php if(View::exists($module->alias . '::products.edit')): ?>
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
                            <?php if ($__env->exists($module->alias . '::products.edit')) echo $__env->make($module->alias . '::products.edit', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

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
        var categories = <?php echo $itemCategories; ?>;
        var units = <?php echo $units; ?>;
        var categoryMap = {};
        var unitMap = {};

        $(function () {
            $('#name').focus();
        });

        $(function () {
            <!-- category -->
            var categoryId = <?php echo e($itemCategoryPublicId ?: 0); ?>;
            var $item_categorySelect = $('select#item_category_id');
            <?php if(Auth::user()->can('create', ENTITY_ITEM_CATEGORY)): ?>
            $item_categorySelect.append(new Option("<?php echo e(trans('texts.create_item_category')); ?>:$name", '-1'));
                    <?php endif; ?>
            for (var i = 0; i < categories.length; i++) {
                var category = categories[i];
                categoryMap[category.public_id] = category;
                $item_categorySelect.append(new Option(getClientDisplayName(category), category.public_id));
            }
            <?php echo $__env->make('partials/entity_combobox', ['entityType' => ENTITY_ITEM_CATEGORY], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            if (categoryId) {
                var category = categoryMap[categoryId];
                setComboboxValue($('.item-category-select'), category.public_id, category.name);
            }
            <!-- /. category  -->

            <!--  unit  -->
            var unitId = <?php echo e($unitPublicId ?: 0); ?>;
            var $unitSelect = $('select#unit_id');
            <?php if(Auth::user()->can('create', ENTITY_UNIT)): ?>
            $unitSelect.append(new Option("<?php echo e(trans('texts.create_unit')); ?>:$name", '-1'));
                    <?php endif; ?>
            for (var i = 0; i < units.length; i++) {
                var unit = units[i];
                unitMap[unit.public_id] = unit;
                $unitSelect.append(new Option(getClientDisplayName(unit), unit.public_id));
            }
            <?php echo $__env->make('partials/entity_combobox', ['entityType' => ENTITY_UNIT], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            if (unitId) {
                var unit = unitMap[unitId];
                setComboboxValue($('.unit-select'), unit.public_id, unit.name);
            }
        });<!-- /. item unit  -->

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