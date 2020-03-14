<?php $__env->startSection('content'); ?>
    ##parent-placeholder-040f06fd774092478d450774f5ba30c5da78acc8##

<?php echo Former::open($url)
    ->method($method)
    ->autocomplete('off')
    ->rules(['name' => 'required|max:255', 'cost' => 'required|numeric', 'category_id' => 'required|numeric'])
    ->addClass('col-lg-10 col-lg-offset-1 main-form warn-on-exit'); ?>


<?php if ($product): ?>
    <?php echo e(Former::populate($product)); ?>

    <?php echo e(Former::populateField('cost', Utils::roundSignificant($product->cost))); ?>

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
                    <?php echo Former::text('name')->label('texts.product'); ?>

                    <?php echo Former::text('serial')->label('texts.serial'); ?>

                    <?php echo Former::text('tag')->label('texts.tag'); ?>


                    <?php echo Former::select('category_id')->addOption('', '')
                        ->label(trans('texts.category'))
                        ->addGroupClass('category-select'); ?>


                    <?php echo Former::textarea('notes')->rows(6); ?>

                    <?php echo $__env->make('partials/custom_fields', ['entityType' => ENTITY_PRODUCT], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                    <?php echo Former::text('cost'); ?>

                    <?php if ($account->invoice_item_taxes): ?>
                        <?php echo $__env->make('partials.tax_rates', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

<?php $__currentLoopData = Module::getOrdered();
$__env->addLoop($__currentLoopData);
foreach ($__currentLoopData as $module): $__env->incrementLoopIndices();
    $loop = $__env->getLastLoop(); ?>
    <?php if (View::exists($module->alias . '::products.edit')): ?>
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
<?php endforeach;
$__env->popLoop();
$loop = $__env->getLastLoop(); ?>

<?php if (Auth::user()->canCreateOrEdit(ENTITY_PRODUCT, $product)): ?>
    <center class="buttons">
        <?php echo Button::normal(trans('texts.cancel'))->large()->asLinkTo(HTMLUtils::previousUrl('/products'))->appendIcon(Icon::create('remove-circle')); ?>

        <?php echo Button::success(trans('texts.save'))->submit()->large()->appendIcon(Icon::create('floppy-disk')); ?>

        <?php if ($product): ?>
            <?php echo DropdownButton::normal(trans('texts.more_actions'))
                ->withContents($product->present()->moreActions())
                ->large()
                ->dropup(); ?>

        <?php endif; ?>
    </center>
<?php endif; ?>
<?php echo Former::close(); ?>

    <script type="text/javascript">
        var itemCategories = <?php echo $itemCategories; ?>;
        var categoryMap = {};

        $(function () {
            $('#name').focus();
        });

        $(function () {
            <!-- category -->
            var categoryId = <?php echo e($itemCategoryPublicId ?: 0); ?>;
            var $categorySelect = $('select#category_id');
            <?php if(Auth::user()->can('create', ENTITY_ITEM_CATEGORY)): ?>
            $categorySelect.append(new Option("<?php echo e(trans('texts.create_category')); ?>:$name", '-1'));
            <?php endif; ?>
            for (var i = 0; i < itemCategories.length; i++) {
                var category = itemCategories[i];
                categoryMap[category.public_id] = category;
                $categorySelect.append(new Option(getClientDisplayName(category), category.public_id));
            }
            <?php echo $__env->make('partials/entity_combobox', ['entityType' => 'category'], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            if (categoryId) {
                var category = categoryMap[categoryId];
                setComboboxValue($('.category-select'), category.public_id, category.name);
            }

            <!-- /. category  -->
        });

        <!-- /. item category  -->
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