<?php $__env->startSection('content'); ?>
    ##parent-placeholder-040f06fd774092478d450774f5ba30c5da78acc8##
    <?php echo Former::open($url)
    ->method($method)
    ->autocomplete('off')
    ->rules(['first_name' => 'required|max:50','last_name' => 'required|max:50','username' => 'required|max:50','email' => 'required|email|max:50','location_id' => 'required','notes' => 'required|max:255'])
    ->addClass('col-lg-10 col-lg-offset-1 main-form warn-on-exit'); ?>

    <?php if($user): ?>
        <?php echo e(Former::populate($user)); ?>

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
                    <?php echo Former::text('first_name')->label('texts.first_name'); ?>

                    <?php echo Former::text('last_name')->label('texts.last_name'); ?>

                    <?php echo Former::text('username')->label('texts.username'); ?>

                    <?php echo Former::text('email')->label('texts.email'); ?>

                    <?php echo Former::text('phone')->label('texts.phone'); ?>

                    <?php echo Former::select('location_id')
                     ->placeholder(trans('texts.select_location'))
                     ->label(trans('texts.location'))
                     ->addGroupClass('location-select'); ?>

                    <?php echo Former::textarea('notes')->rows(6); ?>

                </div>
            </div>
        </div>
    </div>
    <?php $__currentLoopData = Module::getOrdered(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $module): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php if(View::exists($module->alias . '::users.edit')): ?>
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
                            <?php if ($__env->exists($module->alias . '::users.edit')) echo $__env->make($module->alias . '::users.edit', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php if(Auth::user()->canCreateOrEdit(ENTITY_USER, $user)): ?>
        <center class="buttons">
            <?php echo Button::normal(trans('texts.cancel'))->large()->asLinkTo(HTMLUtils::previousUrl('/users'))->appendIcon(Icon::create('remove-circle')); ?>

            <?php echo Button::success(trans('texts.save'))->submit()->large()->appendIcon(Icon::create('floppy-disk')); ?>

            <?php if($user): ?>
                <?php echo DropdownButton::normal(trans('texts.more_actions'))
                ->withContents($user->present()->moreActions())
                ->large()
                ->dropup(); ?>

            <?php endif; ?>
        </center>
    <?php endif; ?>
    <?php echo Former::close(); ?>

    <script type="text/javascript">
        var locations = <?php echo $locations; ?>;
        var locationMap = {};

        $(function () {
            $('#name').focus();
        });

        $(function () {
            <!-- user location -->
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
            }<!-- /. user location  -->
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