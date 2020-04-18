<?php $__env->startSection('content'); ?>
    ##parent-placeholder-040f06fd774092478d450774f5ba30c5da78acc8##
    <?php echo Former::open($url)
    ->method($method)
    ->autocomplete('off')
    ->rules(['name' => 'required|max:90','item_category_id' => 'required'])
    ->addClass('col-lg-10 col-lg-offset-1 main-form warn-on-exit'); ?>

    <?php if($itemBrand): ?>
        <?php echo e(Former::populate($itemBrand)); ?>

        <?php echo e(Former::populateField('qty','0.00')); ?>

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
                    <?php echo Former::text('name')->label('texts.item_brand'); ?>

                    <?php echo Former::select('item_category_id')->addOption('', '')
                    ->label(trans('texts.item_category'))
                    ->addGroupClass('item-category-select')
                    ->help(trans('texts.item_category_help') . ' | ' . link_to('/item_categories/', trans('texts.customize_options'))); ?>

                    <?php echo Former::textarea('notes')->rows(6); ?>

                </div>
            </div>
        </div>
    </div>

    <?php $__currentLoopData = Module::getOrdered(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $module): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php if(View::exists($module->alias . '::item_brands.edit')): ?>
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
                            <?php if ($__env->exists($module->alias . '::item_brands.edit')) echo $__env->make($module->alias . '::item_brands.edit', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php if(Auth::user()->canCreateOrEdit(ENTITY_ITEM_CATEGORY, $itemBrand)): ?>
        <center class="buttons">
            <?php echo Button::normal(trans('texts.cancel'))->large()->asLinkTo(HTMLUtils::previousUrl('/item_brands'))->appendIcon(Icon::create('remove-circle')); ?>

            <?php echo Button::success(trans('texts.save'))->submit()->large()->appendIcon(Icon::create('floppy-disk')); ?>

            <?php if($itemBrand): ?>
                <?php echo DropdownButton::normal(trans('texts.more_actions'))
                ->withContents($itemBrand->present()->moreActions())
                ->large()
                ->dropup(); ?>

            <?php endif; ?>
        </center>
    <?php endif; ?>
    <?php echo Former::close(); ?>

    <script type="text/javascript">
        var categories = <?php echo $itemCategories; ?>;

        var categoryMap = {};

        $(function () {
            $('#name').focus();
        });

        $(function () {

            var categoryId = <?php echo e($itemCategoryPublicId ?: 0); ?>;
            var $item_categorySelect = $('select#item_category_id');
            <?php if(Auth::user()->can('create', ENTITY_ITEM_CATEGORY)): ?>
            $item_categorySelect.append(new Option("<?php echo e(trans('texts.create_item_category')); ?>: $name", '-1'));
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