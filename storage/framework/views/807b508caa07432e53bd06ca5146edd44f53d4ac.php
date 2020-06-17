<!-- landing page -->
<?php use App\Models\EntityModel;

echo Former::open(EntityModel::getFormUrl($entityType) . '/bulk')->addClass('listForm_' . $entityType); ?>


<div style="display:none">
    <?php echo Former::text('action')->id('action_' . $entityType); ?>

    <?php echo Former::text('public_id')->id('public_id_' . $entityType); ?>

    <?php echo Former::text('datatable')->value('true'); ?>

</div>
<div class="row">
    <div class="pull-left">
    <?php if(Auth::user()->can('create', $entityType || Utils::isAdmin())): ?>
        <?php echo Button::primary(trans('texts.invoice'))->withAttributes(['class'=>'invoice', 'onclick' =>'submitForm_'.$entityType.'("invoice")'])->appendIcon(Icon::create('check')); ?>

    <?php endif; ?>
    <!-- action buttons -->
        <?php if(Auth::user()->can('edit', $entityType) || Utils::isAdmin()): ?>
            <?php echo DropdownButton::normal(trans('texts.archive'))
            ->withContents($datatable->bulkActions())
            ->withAttributes(['class'=>'archive'])
            ->split(); ?>

            <span id="statusWrapper_<?php echo e($entityType); ?>" style="display:none">
            <select class="form-control" style="width: 220px" id="statuses_<?php echo e($entityType); ?>" multiple="true">
            <?php if(count(EntityModel::getStatusesFor($entityType))): ?>
                    <optgroup label="<?php echo e(trans('texts.entity_state')); ?>">
            <?php $__currentLoopData = EntityModel::getStatesFor($entityType); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($key); ?>"><?php echo e($value); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </optgroup>
                    <optgroup label="<?php echo e(trans('texts.status')); ?>">
            <?php $__currentLoopData = EntityModel::getStatusesFor($entityType); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($key); ?>"><?php echo e($value); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </optgroup>
                <?php else: ?>
                    <?php $__currentLoopData = EntityModel::getStatesFor($entityType); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($key); ?>"><?php echo e($value); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php endif; ?>
            </select>
            </span>
        <?php endif; ?>
    </div>
    <!-- search record -->
    <div id="top_right_buttons" class="row pull-right">
        <input id="tableFilter_<?php echo e($entityType); ?>" type="text"
               style="width:180px;margin-right:17px;background-color: white !important"
               class="form-control pull-left" placeholder="<?php echo e(trans('texts.filter')); ?>"
               value="<?php echo e(Input::get('filter')); ?>"/>
        <!-- create records -->
        <?php if(Auth::user()->can('create', $entityType) || Utils::isAdmin()): ?>
            <?php echo Button::primary(mtrans($entityType, "new_{$entityType}"))
            ->asLinkTo(url(
            (in_array($entityType, [ENTITY_PROPOSAL_SNIPPET, ENTITY_PROPOSAL_CATEGORY, ENTITY_PROPOSAL_TEMPLATE]) ? str_replace('_', 's/', Utils::pluralizeEntityType($entityType)) : Utils::pluralizeEntityType($entityType)) .
            '/create/' . (isset($clientId) ? ($clientId . (isset($projectId) ? '/' . $projectId : '')) : '')
            ))->appendIcon(Icon::create('plus-sign')); ?>

        <?php endif; ?>

        <?php echo $__env->make('menu',['entityType', $entityType], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
    </div>
</div>
<!-- data view -->
<div class="row">
    <?php if( Auth::check() || Utils::isAdmin() || Auth::user()->can('view', $entityType)): ?>
        <?php echo Datatable::table()
        ->addColumn(Utils::trans($datatable->columnFields(), $datatable->entityType))
        ->setUrl(empty($url) ? url('api/' . Utils::pluralizeEntityType($entityType)) : $url)
        ->setCustomValues('entityType', Utils::pluralizeEntityType($entityType))
        ->setCustomValues('clientId', isset($clientId) && $clientId && empty($projectId))
        ->setOptions('sPaginationType', 'bootstrap')
        ->setOptions('aaSorting', [[isset($clientId) ? ($datatable->sortCol-1) : $datatable->sortCol, 'desc']])
        ->render('datatable'); ?>

    <?php endif; ?>
</div>

<?php if($entityType == ENTITY_PAYMENT): ?>
    <?php echo $__env->make('partials/refund_payment', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
<?php endif; ?>

<?php echo Former::close(); ?>


<style type="text/css">

    <?php $__currentLoopData = $datatable->rightAlignIndices(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    .listForm_<?php echo e($entityType); ?> table.dataTable td:nth-child(<?php echo e($index); ?>) {
        text-align: right;
    }

    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    <?php $__currentLoopData = $datatable->centerAlignIndices(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    .listForm_<?php echo e($entityType); ?> table.dataTable td:nth-child(<?php echo e($index); ?>) {
        text-align: center;
    }
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</style>

<script type="text/javascript">

    var submittedForm;

    function submitForm_<?php echo e($entityType); ?>(action, id) {
        // prevent duplicate form submissions
        if (submittedForm) {
            swal("<?php echo e(trans('texts.processing_request')); ?>");
            return;
        }
        submittedForm = true;

        if (id) {
            $('#public_id_<?php echo e($entityType); ?>').val(id);
        }

        if (action == 'delete' || action == 'emailInvoice') {
            sweetConfirm(function () {
                $('#action_<?php echo e($entityType); ?>').val(action);
                $('form.listForm_<?php echo e($entityType); ?>').submit();
            });
        } else {
            $('#action_<?php echo e($entityType); ?>').val(action);
            $('form.listForm_<?php echo e($entityType); ?>').submit();
        }
    }

    $(function () {

        // Handle datatable filtering
        var tableFilter = '';
        var searchTimeout = false;

        function filterTable_<?php echo e($entityType); ?>(val) {
            if (val == tableFilter) {
                return;
            }
            tableFilter = val;
            var oTable0 = $('.listForm_<?php echo e($entityType); ?> .data-table').dataTable();
            oTable0.fnFilter(val);
        }

        $('#tableFilter_<?php echo e($entityType); ?>').on('keyup', function () {
            if (searchTimeout) {
                window.clearTimeout(searchTimeout);
            }
            searchTimeout = setTimeout(function () {
                filterTable_<?php echo e($entityType); ?>($('#tableFilter_<?php echo e($entityType); ?>').val());
            }, 500);
        });

        if ($('#tableFilter_<?php echo e($entityType); ?>').val()) {
            filterTable_<?php echo e($entityType); ?>($('#tableFilter_<?php echo e($entityType); ?>').val());
        }

        $('.listForm_<?php echo e($entityType); ?> .head0').click(function (event) {
            if (event.target.type !== 'checkbox') {
                $('.listForm_<?php echo e($entityType); ?> .head0 input[type=checkbox]').click();
            }
        });

        // Enable/disable bulk action buttons
        window.onDatatableReady_<?php echo e(Utils::pluralizeEntityType($entityType)); ?> = function () {
            $(':checkbox').click(function () {
                setBulkActionsEnabled_<?php echo e($entityType); ?>();
            });

            $('.listForm_<?php echo e($entityType); ?> tbody tr').unbind('click').click(function (event) {
                if (event.target.type !== 'checkbox' && event.target.type !== 'button' && event.target.tagName.toLowerCase() !== 'a') {
                    $checkbox = $(this).closest('tr').find(':checkbox:not(:disabled)');
                    var checked = $checkbox.prop('checked');
                    $checkbox.prop('checked', !checked);
                    setBulkActionsEnabled_<?php echo e($entityType); ?>();
                }
            });

            actionListHandler();
            $('[data-toggle="tooltip"]').tooltip();
        };

        $('.listForm_<?php echo e($entityType); ?> .archive, .invoice').prop('disabled', true);
        $('.listForm_<?php echo e($entityType); ?> .archive:not(.dropdown-toggle)').click(function () {
            submitForm_<?php echo e($entityType); ?>('archive');
        });

        $('.listForm_<?php echo e($entityType); ?> .selectAll').click(function () {
            $(this).closest('table').find(':checkbox:not(:disabled)').prop('checked', this.checked);
        });

        function setBulkActionsEnabled_<?php echo e($entityType); ?>() {
            var buttonLabel = "<?php echo e(trans('texts.archive')); ?>";
            var count = $('.listForm_<?php echo e($entityType); ?> tbody :checkbox:checked').length;
            $('.listForm_<?php echo e($entityType); ?> button.archive, .listForm_<?php echo e($entityType); ?> button.invoice').prop('disabled', !count);
            if (count) {
                buttonLabel += ' (' + count + ')';
            }
            $('.listForm_<?php echo e($entityType); ?> button.archive').not('.dropdown-toggle').text(buttonLabel);
        }

        // Setup state/status filter
        $('#statuses_<?php echo e($entityType); ?>').select2({
            placeholder: "<?php echo e(trans('texts.status')); ?>",
            //allowClear: true,
            templateSelection: function (data, container) {
                if (data.id == 'archived') {
                    $(container).css('color', '#fff');
                    $(container).css('background-color', '#f0ad4e');
                    $(container).css('border-color', '#eea236');
                } else if (data.id == 'deleted') {
                    $(container).css('color', '#fff');
                    $(container).css('background-color', '#d9534f');
                    $(container).css('border-color', '#d43f3a');
                }
                return data.text;
            }
        }).val('<?php echo e(session('entity_state_filter:' . $entityType, STATUS_ACTIVE) . ',' . session('entity_status_filter:' . $entityType)); ?>'.split(','))
            .trigger('change')
            .on('change', function () {
                var filter = $('#statuses_<?php echo e($entityType); ?>').val();
                if (filter) {
                    filter = filter.join(',');
                } else {
                    filter = '';
                }
                var url = '<?php echo e(URL::to('set_entity_filter/' . $entityType)); ?>' + '/' + filter;
                $.get(url, function (data) {
                    refreshDatatable_<?php echo e(Utils::pluralizeEntityType($entityType)); ?>();
                })
            }).maximizeSelect2Height();

        $('#statusWrapper_<?php echo e($entityType); ?>').show();
        <?php for($i = 1; $i <= 10; $i++): ?>
        Mousetrap.bind('g <?php echo e($i); ?>', function (e) {
            var link = $('.data-table').find('tr:nth-child(<?php echo e($i); ?>)').find('a').attr('href');
            if (link) {
                location.href = link;
            }
        });
        <?php endfor; ?>
    });

</script>
