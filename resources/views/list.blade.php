<!-- landing page -->
{!! Former::open(\App\Models\EntityModel::getFormUrl($entityType) . '/bulk')->addClass('listForm_' . $entityType) !!}

<div style="display:none">
    {!! Former::text('action')->id('action_' . $entityType) !!}
    {!! Former::text('public_id')->id('public_id_' . $entityType) !!}
    {!! Former::text('datatable')->value('true') !!}
</div>

<div class="pull-left">
    @if (in_array($entityType, [ENTITY_TASK, ENTITY_EXPENSE, ENTITY_PRODUCT, ENTITY_PROJECT]))
        @can('create', 'invoice')
            {!! Button::primary(trans('texts.invoice'))->withAttributes(['class'=>'invoice', 'onclick' =>'submitForm_'.$entityType.'("invoice")'])->appendIcon(Icon::create('check')) !!}
        @endcan
    @endif

    {!! DropdownButton::normal(trans('texts.archive'))
    ->withContents($datatable->bulkActions())
    ->withAttributes(['class'=>'archive'])
    ->split() !!}

    <span id="statusWrapper_{{ $entityType }}" style="display:none">
    <select class="form-control" style="width: 220px" id="statuses_{{ $entityType }}" multiple="true">
@if (count(\App\Models\EntityModel::getStatusesFor($entityType)))
            <optgroup label="{{ trans('texts.entity_state') }}">
          @foreach (\App\Models\EntityModel::getStatesFor($entityType) as $key => $value)
                    <option value="{{ $key }}">{{ $value }}</option>
                @endforeach
        </optgroup>
            <optgroup label="{{ trans('texts.status') }}">
           @foreach (\App\Models\EntityModel::getStatusesFor($entityType) as $key => $value)
                    <option value="{{ $key }}">{{ $value }}</option>
                @endforeach
        </optgroup>
        @else
            @foreach (\App\Models\EntityModel::getStatesFor($entityType) as $key => $value)
                <option value="{{ $key }}">{{ $value }}</option>
            @endforeach
        @endif
    </select>
    </span>
</div>
<div id="top_right_buttons" class="pull-right">
    <input id="tableFilter_{{ $entityType }}" type="text"
           style="width:180px;margin-right:17px;background-color: white !important"
           class="form-control pull-left" placeholder="{{ trans('texts.filter') }}"
           value="{{ Input::get('filter') }}"/>

@if (in_array($entityType, [ENTITY_PROPOSAL,ENTITY_PROPOSAL_TEMPLATE,ENTITY_PROPOSAL_SNIPPET]))
    @if (Auth::user()->can('create', [ENTITY_PROPOSAL_TEMPLATE,ENTITY_PROPOSAL_SNIPPET]))
        {!! DropdownButton::normal(trans('texts.maintenance'))
        ->withAttributes(['class'=>'maintenanceDropdown'])
        ->withContents([
        ['label' => trans('texts.new_proposal_template'), 'url' => url('/proposals/templates/create')],
        ['label' => trans('texts.new_proposal_snippet'), 'url' => url('/proposals/snippets/create')],
        ])->split() !!}
    @endif
@endif
@if (in_array($entityType, [ENTITY_PROPOSAL_SNIPPET,ENTITY_PROPOSAL_CATEGORY]))
    @if (Auth::user()->can('create', [ENTITY_PROPOSAL_CATEGORY]))
        {!! DropdownButton::normal(trans('texts.maintenance'))
        ->withAttributes(['class'=>'maintenanceDropdown'])
        ->withContents([
        ['label' => trans('texts.new_proposal_category'), 'url' => url('/proposals/categories/create')],
        ])->split() !!}
    @endif
@endif
@if (in_array($entityType, [ENTITY_EXPENSE,ENTITY_EXPENSE_CATEGORY,ENTITY_RECURRING_EXPENSE, ENTITY_RECURRING_INVOICE,ENTITY_VENDOR]))
    @if (Auth::user()->can('create', [ENTITY_EXPENSE_CATEGORY,ENTITY_RECURRING_EXPENSE, ENTITY_RECURRING_INVOICE,ENTITY_VENDOR]))
        {!! DropdownButton::normal(trans('texts.maintenance'))
        ->withAttributes(['class'=>'maintenanceDropdown'])
        ->withContents([
        ['label' => trans('texts.new_recurring_expense'), 'url' => url('/recurring_expenses')],
        ['label' => trans('texts.new_expense_category'), 'url' => url('/expense_categories')],
        ])->split() !!}
    @endif
@endif
<!-- entity task -->
@if ($entityType == ENTITY_TASK)
    {!! Button::normal(trans('texts.kanban'))->asLinkTo(url('/tasks/kanban' . (! empty($clientId) ? ('/' . $clientId . (! empty($projectId) ? '/' . $projectId : '')) : '')))->appendIcon(Icon::create('th')) !!}
    {!! Button::normal(trans('texts.time_tracker'))->asLinkTo('javascript:openTimeTracker()')->appendIcon(Icon::create('time')) !!}
@endif

@if (Auth::user()->can('create', $entityType) && empty($vendorId))
    {!! Button::primary(mtrans($entityType, "new_{$entityType}"))
    ->asLinkTo(url(
    (in_array($entityType, [ENTITY_PROPOSAL_SNIPPET, ENTITY_PROPOSAL_CATEGORY, ENTITY_PROPOSAL_TEMPLATE]) ? str_replace('_', 's/', Utils::pluralizeEntityType($entityType)) : Utils::pluralizeEntityType($entityType)) .
    '/create/' . (isset($clientId) ? ($clientId . (isset($projectId) ? '/' . $projectId : '')) : '')
    ))
    ->appendIcon(Icon::create('plus-sign')) !!}
@endif
@if (in_array($entityType, [ENTITY_INVOICE,ENTITY_INVOICE_ITEM,ENTITY_CLIENT,ENTITY_CREDIT]))
    @if (Auth::user()->can('create', [ENTITY_INVOICE,ENTITY_INVOICE_ITEM,ENTITY_CLIENT,ENTITY_CREDIT]))
        {!! DropdownButton::normal(trans('texts.maintenance'))
        ->withAttributes(['class'=>'maintenanceDropdown'])
        ->withContents([
        ['label' => trans('texts.new_client'), 'url' => url('/clients')],
                ['label' => trans('texts.new_quote'), 'url' => url('/quotes')],
        ['label' => trans('texts.new_credit'), 'url' => url('/credits')],
        ['label' => trans('texts.new_expense'), 'url' => url('/expenses')],
        ])->split() !!}
    @endif
@endif
<!-- navigation menu -->
    @if (in_array($entityType, [ENTITY_PRODUCT,ENTITY_ITEM_BRAND,ENTITY_ITEM_CATEGORY,ENTITY_ITEM_PRICE, ENTITY_ITEM_STORE, ENTITY_STORE]))
        @if (Auth::user()->can('create', [ENTITY_PRODUCT,ENTITY_ITEM_BRAND,ENTITY_ITEM_CATEGORY,ENTITY_ITEM_PRICE, ENTITY_ITEM_STORE, ENTITY_STORE]))
            {!! DropdownButton::normal(trans('texts.maintenance'))
            ->withAttributes(['class'=>'maintenanceDropdown'])
            ->withContents([
            ['label' => trans('texts.new_item_brand'), 'url' => url('/item_brands/create')],
            ['label' => trans('texts.new_item_category'), 'url' => url('/item_categories')],
            ['label' => trans('texts.new_item_price'), 'url' => url('/item_prices')],
            ['label' => trans('texts.new_item_store'), 'url' => url('/item_stores')],
            ['label' => trans('texts.new_item_movement'), 'url' => url('/item_movements')],
            ['label' => trans('texts.new_store'), 'url' => url('/stores')],
            ['label' => trans('texts.new_sale_type'), 'url' => url('/sale_types')],
            ['label' => trans('texts.new_unit'), 'url' => url('/units')],
            ])->split() !!}
        @endif
    @endif
</div>
{!! Datatable::table()
->addColumn(Utils::trans($datatable->columnFields(), $datatable->entityType))
->setUrl(empty($url) ? url('api/' . Utils::pluralizeEntityType($entityType)) : $url)
->setCustomValues('entityType', Utils::pluralizeEntityType($entityType))
->setCustomValues('clientId', isset($clientId) && $clientId && empty($projectId))
->setOptions('sPaginationType', 'bootstrap')
->setOptions('aaSorting', [[isset($clientId) ? ($datatable->sortCol-1) : $datatable->sortCol, 'desc']])
->render('datatable') !!}

@if ($entityType == ENTITY_PAYMENT)
    @include('partials/refund_payment')
@endif

{!! Former::close() !!}

<style type="text/css">

    @foreach ($datatable->rightAlignIndices() as $index)
.listForm_{{ $entityType }} table.dataTable td:nth-child({{ $index }}) {
        text-align: right;
    }

    @endforeach

@foreach ($datatable->centerAlignIndices() as $index)
.listForm_{{ $entityType }} table.dataTable td:nth-child({{ $index }}) {
        text-align: center;
    }
    @endforeach
</style>

<script type="text/javascript">
    var submittedForm;

    function submitForm_{{ $entityType }}(action, id) {
// prevent duplicate form submissions
        if (submittedForm) {
            swal("{{ trans('texts.processing_request') }}")
            return;
        }
        submittedForm = true;

        if (id) {
            $('#public_id_{{ $entityType }}').val(id);
        }

        if (action == 'delete' || action == 'emailInvoice') {
            sweetConfirm(function () {
                $('#action_{{ $entityType }}').val(action);
                $('form.listForm_{{ $entityType }}').submit();
            });
        } else {
            $('#action_{{ $entityType }}').val(action);
            $('form.listForm_{{ $entityType }}').submit();
        }
    }

    $(function () {

// Handle datatable filtering
        var tableFilter = '';
        var searchTimeout = false;

        function filterTable_{{ $entityType }}(val) {
            if (val == tableFilter) {
                return;
            }
            tableFilter = val;
            var oTable0 = $('.listForm_{{ $entityType }} .data-table').dataTable();
            oTable0.fnFilter(val);
        }

        $('#tableFilter_{{ $entityType }}').on('keyup', function () {
            if (searchTimeout) {
                window.clearTimeout(searchTimeout);
            }
            searchTimeout = setTimeout(function () {
                filterTable_{{ $entityType }}($('#tableFilter_{{ $entityType }}').val());
            }, 500);
        })

        if ($('#tableFilter_{{ $entityType }}').val()) {
            filterTable_{{ $entityType }}($('#tableFilter_{{ $entityType }}').val());
        }

        $('.listForm_{{ $entityType }} .head0').click(function (event) {
            if (event.target.type !== 'checkbox') {
                $('.listForm_{{ $entityType }} .head0 input[type=checkbox]').click();
            }
        });

// Enable/disable bulk action buttons
        window.onDatatableReady_{{ Utils::pluralizeEntityType($entityType) }} = function () {
            $(':checkbox').click(function () {
                setBulkActionsEnabled_{{ $entityType }}();
            });

            $('.listForm_{{ $entityType }} tbody tr').unbind('click').click(function (event) {
                if (event.target.type !== 'checkbox' && event.target.type !== 'button' && event.target.tagName.toLowerCase() !== 'a') {
                    $checkbox = $(this).closest('tr').find(':checkbox:not(:disabled)');
                    var checked = $checkbox.prop('checked');
                    $checkbox.prop('checked', !checked);
                    setBulkActionsEnabled_{{ $entityType }}();
                }
            });

            actionListHandler();
            $('[data-toggle="tooltip"]').tooltip();
        }

        $('.listForm_{{ $entityType }} .archive, .invoice').prop('disabled', true);
        $('.listForm_{{ $entityType }} .archive:not(.dropdown-toggle)').click(function () {
            submitForm_{{ $entityType }}('archive');
        });

        $('.listForm_{{ $entityType }} .selectAll').click(function () {
            $(this).closest('table').find(':checkbox:not(:disabled)').prop('checked', this.checked);
        });

        function setBulkActionsEnabled_{{ $entityType }}() {
            var buttonLabel = "{{ trans('texts.archive') }}";
            var count = $('.listForm_{{ $entityType }} tbody :checkbox:checked').length;
            $('.listForm_{{ $entityType }} button.archive, .listForm_{{ $entityType }} button.invoice').prop('disabled', !count);
            if (count) {
                buttonLabel += ' (' + count + ')';
            }
            $('.listForm_{{ $entityType }} button.archive').not('.dropdown-toggle').text(buttonLabel);
        }

// Setup state/status filter
        $('#statuses_{{ $entityType }}').select2({
            placeholder: "{{ trans('texts.status') }}",
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
        }).val('{{ session('entity_state_filter:' . $entityType, STATUS_ACTIVE) . ',' . session('entity_status_filter:' . $entityType) }}'.split(','))
            .trigger('change')
            .on('change', function () {
                var filter = $('#statuses_{{ $entityType }}').val();
                if (filter) {
                    filter = filter.join(',');
                } else {
                    filter = '';
                }
                var url = '{{ URL::to('set_entity_filter/' . $entityType) }}' + '/' + filter;
                $.get(url, function (data) {
                    refreshDatatable_{{ Utils::pluralizeEntityType($entityType) }}();
                })
            }).maximizeSelect2Height();

        $('#statusWrapper_{{ $entityType }}').show();
        @for ($i = 1; $i <= 10; $i++)
        Mousetrap.bind('g {{ $i }}', function (e) {
            var link = $('.data-table').find('tr:nth-child({{ $i }})').find('a').attr('href');
            if (link) {
                location.href = link;
            }
        });
        @endfor
    });

</script>
