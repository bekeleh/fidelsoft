

<?php $__env->startSection('content'); ?>
    ##parent-placeholder-040f06fd774092478d450774f5ba30c5da78acc8##
    <?php echo Former::open($url)
    ->method($method)
    ->autocomplete('off')
    ->rules(['name' => 'required|max:255','store_id' => 'required','location_id' => 'required','notes' => 'required|max:255'])
    ->addClass('col-lg-10 col-lg-offset-1 main-form warn-on-exit'); ?>

    <?php if($branch): ?>
        <?php echo e(Former::populate($branch)); ?>

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
                    <!-- branch name -->
                <?php echo Former::text('name')->label('texts.branch_name'); ?>

                <!-- branch default store dropdown -->
                <?php echo Former::select('store_id')->addOption('', '')
                    ->label(trans('texts.store_name'))
                    ->addGroupClass('store-select')
                    ->help(trans('texts.store_help') . ' | ' . link_to('/stores/', trans('texts.customize_options'))); ?>

                <!-- location dropdown -->
                    <?php echo Former::select('location_id')->addOption('', '')
                    ->label(trans('texts.location_name'))
                    ->addGroupClass('location-select')
                    ->help(trans('texts.location_help') . ' | ' . link_to('/locations/', trans('texts.customize_options'))); ?>

                    <?php echo Former::textarea('notes')->rows(6); ?>

                </div>
            </div>
        </div>
    </div>

    <?php if(Auth::user()->canCreateOrEdit(ENTITY_BRANCH, $branch)): ?>
        <center class="buttons">
            <?php echo Button::normal(trans('texts.cancel'))->large()->asLinkTo(HTMLUtils::previousUrl('/branches'))->appendIcon(Icon::create('remove-circle')); ?>

            <?php echo Button::success(trans('texts.save'))->submit()->large()->appendIcon(Icon::create('floppy-disk')); ?>

            <?php if($branch): ?>
                <?php echo DropdownButton::normal(trans('texts.more_actions'))
                ->withContents($branch->present()->moreActions())
                ->large()
                ->dropup(); ?>

            <?php endif; ?>
        </center>
    <?php endif; ?>

    <?php echo Former::close(); ?>

    <script type="text/javascript">
        $(function () {
            $('#name').focus();
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
    <script type="text/javascript">
        var locations = <?php echo $locations; ?>;
        var stores = <?php echo $stores; ?>;

        var locationMap = {};
        var storeMap = {};

        $(function () {
            // location dropdown
            var locationId = <?php echo e($locationPublicId ?: 0); ?>;
            var $locationSelect = $('select#location_id');
            <?php if(Auth::user()->can('create', ENTITY_LOCATION)): ?>
            $locationSelect.append(new Option("<?php echo e(trans('texts.create_location')); ?>: $name", '-1'));
                    <?php endif; ?>
            for (var i = 0; i < locations.length; i++) {
                var location = locations[i];
                locationMap[location.public_id] = location;
                $locationSelect.append(new Option(getClientDisplayName(location), location.public_id));
            }
            <?php echo $__env->make('partials/entity_combobox', ['entityType' => ENTITY_LOCATION], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            if (locationId) {
                var location = locationMap[locationId];
                setComboboxValue($('.location-select'), location.public_id, location.name);
            }
            // store dropdown
            var storeId = <?php echo e($storePublicId ?: 0); ?>;
            var $storeSelect = $('select#store_id');
            <?php if(Auth::user()->can('create', ENTITY_STORE)): ?>
            $storeSelect.append(new Option("<?php echo e(trans('texts.create_store')); ?>: $name", '-1'));
                    <?php endif; ?>
            for (var i = 0; i < stores.length; i++) {
                var store = stores[i];
                storeMap[store.public_id] = store;
                $storeSelect.append(new Option(getClientDisplayName(store), store.public_id));
            }
            <?php echo $__env->make('partials/entity_combobox', ['entityType' => ENTITY_STORE], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            if (storeId) {
                var store = storeMap[storeId];
                setComboboxValue($('.store-select'), store.public_id, store.name);
            }
        });

    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('header', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>