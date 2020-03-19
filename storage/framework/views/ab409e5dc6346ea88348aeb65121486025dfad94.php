<?php echo Former::open(\App\Models\EntityModel::getFormUrl($entityType) . '/bulk')
		->addClass('listForm_' . $entityType); ?>


<div style="display:none">
    <?php echo Former::text('action')->id('action_' . $entityType); ?>

    <?php echo Former::text('public_id')->id('public_id_' . $entityType); ?>

    <?php echo Former::text('datatable')->value('true'); ?>

</div>

<div class="pull-left">
    <?php if(in_array($entityType, [ENTITY_TASK, ENTITY_EXPENSE, ENTITY_PRODUCT, ENTITY_PROJECT])): ?>
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create', 'invoice')): ?>
            <?php echo Button::primary(trans('texts.invoice'))->withAttributes(['class'=>'invoice', 'onclick' =>'submitForm_'.$entityType.'("invoice")'])->appendIcon(Icon::create('check')); ?>

        <?php endif; ?>
    <?php endif; ?>

    <?php echo DropdownButton::normal(trans('texts.archive'))
            ->withContents($datatable->bulkActions())
            ->withAttributes(['class'=>'archive'])
            ->split(); ?>


    &nbsp;
    <span id="statusWrapper_<?php echo e($entityType); ?>" style="display:none">
		<select class="form-control" style="width: 220px" id="statuses_<?php echo e($entityType); ?>" multiple="true">
			<?php if(count(\App\Models\EntityModel::getStatusesFor($entityType))): ?>
                <optgroup label="<?php echo e(trans('texts.entity_state')); ?>">
					<?php $__currentLoopData = \App\Models\EntityModel::getStatesFor($entityType); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($key); ?>"><?php echo e($value); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
				</optgroup>
                <optgroup label="<?php echo e(trans('texts.status')); ?>">
					<?php $__currentLoopData = \App\Models\EntityModel::getStatusesFor($entityType); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($key); ?>"><?php echo e($value); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
				</optgroup>
            <?php else: ?>
                <?php $__currentLoopData = \App\Models\EntityModel::getStatesFor($entityType); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($key); ?>"><?php echo e($value); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endif; ?>
		</select>
	</span>
</div>

<div id="top_right_buttons" class="pull-right">
    <input id="tableFilter_<?php echo e($entityType); ?>" type="text"
           style="width:180px;margin-right:17px;background-color: white !important"
           class="form-control pull-left" placeholder="<?php echo e(trans('texts.filter')); ?>" value="<?php echo e(Input::get('filter')); ?>"/>

    <?php if($entityType == ENTITY_PROPOSAL): ?>
        <?php echo DropdownButton::normal(trans('texts.proposal_templates'))
            ->withAttributes(['class'=>'templatesDropdown'])
            ->withContents([
              ['label' => trans('texts.new_proposal_template'), 'url' => url('/proposals/templates/create')],
            ]
          )->split(); ?>

        <?php echo DropdownButton::normal(trans('texts.proposal_snippets'))
            ->withAttributes(['class'=>'snippetsDropdown'])
            ->withContents([
              ['label' => trans('texts.new_proposal_snippet'), 'url' => url('/proposals/snippets/create')],
            ]
          )->split(); ?>

        <script type="text/javascript">
            $(function () {
                $('.templatesDropdown:not(.dropdown-toggle)').click(function (event) {
                    openUrlOnClick('<?php echo e(url('/proposals/templates')); ?>', event);
                });
                $('.snippetsDropdown:not(.dropdown-toggle)').click(function (event) {
                    openUrlOnClick('<?php echo e(url('/proposals/snippets')); ?>', event);
                });
            });
        </script>
    <?php elseif($entityType == ENTITY_PROPOSAL_SNIPPET): ?>
        <?php echo DropdownButton::normal(trans('texts.proposal_categories'))
            ->withAttributes(['class'=>'categoriesDropdown'])
            ->withContents([
              ['label' => trans('texts.new_proposal_category'), 'url' => url('/proposals/categories/create')],
            ]
          )->split(); ?>

        <script type="text/javascript">
            $(function () {
                $('.categoriesDropdown:not(.dropdown-toggle)').click(function (event) {
                    openUrlOnClick('<?php echo e(url('/proposals/categories')); ?>', event);
                });
            });
        </script>
    <?php elseif($entityType == ENTITY_EXPENSE): ?>
        <?php echo DropdownButton::normal(trans('texts.recurring'))
            ->withAttributes(['class'=>'recurringDropdown'])
            ->withContents([
              ['label' => trans('texts.new_recurring_expense'), 'url' => url('/recurring_expenses/create')],
            ]
          )->split(); ?>

        <?php if(Auth::user()->can('create', ENTITY_EXPENSE_CATEGORY)): ?>
            <?php echo DropdownButton::normal(trans('texts.categories'))
                ->withAttributes(['class'=>'categoriesDropdown'])
                ->withContents([
                  ['label' => trans('texts.new_expense_category'), 'url' => url('/expense_categories/create')],
                ]
              )->split(); ?>

        <?php else: ?>
            <?php echo DropdownButton::normal(trans('texts.categories'))
                ->withAttributes(['class'=>'categoriesDropdown'])
                ->split(); ?>

        <?php endif; ?>
        <script type="text/javascript">
            $(function () {
                $('.recurringDropdown:not(.dropdown-toggle)').click(function (event) {
                    openUrlOnClick('<?php echo e(url('/recurring_expenses')); ?>', event)
                });
                $('.categoriesDropdown:not(.dropdown-toggle)').click(function (event) {
                    openUrlOnClick('<?php echo e(url('/expense_categories')); ?>', event);
                });
            });
        </script><!-- /. expense -->
        <!-- Entity product -->
    <?php elseif($entityType == ENTITY_PRODUCT): ?>
        <?php if(Auth::user()->can('create', [ENTITY_ITEM_PRICE,ENTITY_SALE_TYPE])): ?>
            <?php echo DropdownButton::normal(trans('texts.maintenance'))
            ->withAttributes(['class'=>'maintenanceDropdown'])
            ->withContents([
                ['label' => trans('texts.new_location'), 'url' => url('/locations/create')],
                ['label' => trans('texts.new_store'), 'url' => url('/stores/create')],
                ['label' => trans('texts.new_sale_type'), 'url' => url('/sale_types/create')],
                ['label' => trans('texts.new_item_store'), 'url' => url('/item_stores/create')],
                ['label' => trans('texts.new_item_price'), 'url' => url('/item_prices/create')],
                ['label' => trans('texts.new_item_category'), 'url' => url('/item_categories/create')],
                ['label' => trans('texts.new_unit'), 'url' => url('/units/create')]
            ])->split(); ?>

        <?php else: ?>
            <?php echo DropdownButton::normal(trans('texts.maintenance'))
            ->withAttributes(['class'=>'maintenanceDropdown'])
            ->withContents([
                ['label' => trans('texts.new_location'), 'url' => url('/locations/create')],
                ['label' => trans('texts.new_store'), 'url' => url('/stores/create')],
                ['label' => trans('texts.new_sale_type'), 'url' => url('/sale_types/create')],
                ['label' => trans('texts.new_item_store'), 'url' => url('/item_stores/create')],
                ['label' => trans('texts.new_item_price'), 'url' => url('/item_prices/create')],
                ['label' => trans('texts.new_item_category'), 'url' => url('/item_categories/create')],
                ['label' => trans('texts.new_unit'), 'url' => url('/units/create')]
            ])->split(); ?>

        <?php endif; ?>
        <script type="text/javascript">
            $(function () {
                $('.item_categoriesDropdown:not(.dropdown-toggle)').click(function (event) {
                    openUrlOnClick('<?php echo e(url('/item_categories')); ?>', event);
                });
            });
            $(function () {
                $('.unitsDropdown:not(.dropdown-toggle)').click(function (event) {
                    openUrlOnClick('<?php echo e(url('/units')); ?>', event);
                });
            });
            $(function () {
                $('.unitsDropdown:not(.dropdown-toggle)').click(function (event) {
                    openUrlOnClick('<?php echo e(url('/sale_types')); ?>', event);
                });
            });
        </script><!-- /. store -->
        <!-- store begin -->
    <?php elseif($entityType == ENTITY_STORE): ?>
        <?php if(Auth::user()->can('create', ENTITY_LOCATION)): ?>
            <?php echo DropdownButton::normal(trans('texts.maintenance'))
                ->withAttributes(['class'=>'locationsDropdown'])
                ->withContents([
                  ['label' => trans('texts.new_location'), 'url' => url('/locations/create')],
                ]
              )->split(); ?>

        <?php else: ?>
            <?php echo DropdownButton::normal(trans('texts.locations'))
                ->withAttributes(['class'=>'locationsDropdown'])
                ->split(); ?>

        <?php endif; ?>
        <script type="text/javascript">
            $(function () {
                $('.locationsDropdown:not(.dropdown-toggle)').click(function (event) {
                    openUrlOnClick('<?php echo e(url('/locations')); ?>', event);
                });
            });
        </script><!-- /. store -->
        <!-- item and store -->
    <?php elseif($entityType == ENTITY_ITEM_STORE): ?>
        <?php if(Auth::user()->can('create', [ENTITY_PRODUCT , ENTITY_STORE])): ?>
            <?php echo DropdownButton::normal(trans('texts.maintenance'))
            ->withAttributes(['class'=>'storesDropdown'])
            ->withContents([
            ['label' => trans('texts.new_item_movement'), 'url' => url('/item_movements/create')],
            ['label' => trans('texts.new_product'), 'url' => url('/products/create')],
            ['label' => trans('texts.new_store'), 'url' => url('/stores/create')],
            ])->split(); ?>

        <?php else: ?>
            <?php echo DropdownButton::normal(trans('texts.stores'))
            ->withAttributes(['class'=>'storesDropdown'])
            ->withContents([
            ['label' => trans('texts.new_item_movement'), 'url' => url('/item_movements/create')],
            ['label' => trans('texts.new_product'), 'url' => url('/products/create')],
            ['label' => trans('texts.new_store'), 'url' => url('/stores/create')],
            ])->split(); ?>

        <?php endif; ?>
        <script type="text/javascript">
            $(function () {
                $('.productsDropdown:not(.dropdown-toggle)').click(function (event) {
                    openUrlOnClick('<?php echo e(url('/item_movements')); ?>', event);
                });
            });
            $(function () {
                $('.productsDropdown:not(.dropdown-toggle)').click(function (event) {
                    openUrlOnClick('<?php echo e(url('/products')); ?>', event);
                });
            });
            $(function () {
                $('.storesDropdown:not(.dropdown-toggle)').click(function (event) {
                    openUrlOnClick('<?php echo e(url('/stores')); ?>', event);
                });
            });
        </script><!-- /. item store -->

    <?php elseif($entityType == ENTITY_TASK): ?>
        <?php echo Button::normal(trans('texts.kanban'))->asLinkTo(url('/tasks/kanban' . (! empty($clientId) ? ('/' . $clientId . (! empty($projectId) ? '/' . $projectId : '')) : '')))->appendIcon(Icon::create('th')); ?>

        <?php echo Button::normal(trans('texts.time_tracker'))->asLinkTo('javascript:openTimeTracker()')->appendIcon(Icon::create('time')); ?>

    <?php endif; ?>

    <?php if(Auth::user()->can('create', $entityType) && empty($vendorId)): ?>
        <?php echo Button::primary(mtrans($entityType, "new_{$entityType}"))
            ->asLinkTo(url(
                (in_array($entityType, [ENTITY_PROPOSAL_SNIPPET, ENTITY_PROPOSAL_CATEGORY, ENTITY_PROPOSAL_TEMPLATE]) ? str_replace('_', 's/', Utils::pluralizeEntityType($entityType)) : Utils::pluralizeEntityType($entityType)) .
                '/create/' . (isset($clientId) ? ($clientId . (isset($projectId) ? '/' . $projectId : '')) : '')
            ))
            ->appendIcon(Icon::create('plus-sign')); ?>

    <?php endif; ?>

</div>

<?php echo Datatable::table()
	->addColumn(Utils::trans($datatable->columnFields(), $datatable->entityType))
	->setUrl(empty($url) ? url('api/' . Utils::pluralizeEntityType($entityType)) : $url)
	->setCustomValues('entityType', Utils::pluralizeEntityType($entityType))
	->setCustomValues('clientId', isset($clientId) && $clientId && empty($projectId))
	->setOptions('sPaginationType', 'bootstrap')
    ->setOptions('aaSorting', [[isset($clientId) ? ($datatable->sortCol-1) : $datatable->sortCol, 'desc']])
	->render('datatable'); ?>


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
            swal("<?php echo e(trans('texts.processing_request')); ?>")
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
        })

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
        }

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
