@extends('header')

@section('content')
    @parent
    {!! Former::open($url)
        ->method($method)
        ->autocomplete('off')
        ->rules([
        'product_id' => 'required',
        'status_id' => 'required',
        'previous_warehouse_id' => 'required' ,
        'current_warehouse_id' => 'required',
        'notes' => 'required'
        ])
        ->addClass('col-lg-10 col-lg-offset-1 main-form warn-on-exit') !!}
    @if ($itemTransfer)
        {{ Former::populate($itemTransfer) }}
        <div style="display:none">
            {!! Former::text('public_id') !!}
        </div>
    @endif
    <span style="display:none">
        {!! Former::text('action') !!}
    </span>
    <div class="row">
        <div class="col-lg-10 col-lg-offset-1">
            <div class="panel panel-default">
                <div class="panel-body form-padding-right">
                    <!-- status -->
                {{--                {!! Former::select('status_id')->addOption('', '')--}}
                {{--                ->label(trans('texts.status_name'))->addGroupClass('status-select')--}}
                {{--                ->help(trans('texts.status_help') . ' | ' . link_to('/statuses/', trans('texts.customize_options')))--}}
                {{--                !!}--}}

                <!-- from store -->
                {!! Former::select('previous_warehouse_id')->addOption('', '')
                ->onchange('selectProductAction()')
                ->label(trans('texts.from_warehouse'))->addGroupClass('warehouse-select')
                ->help(trans('texts.warehouse_help') . ' | ' . link_to('/warehouses/', trans('texts.customize_options')))
                !!}
                <!-- to warehouse -->
                {!! Former::select('current_warehouse_id')->addOption('', '')
                ->label(trans('texts.to_warehouse'))->addGroupClass('warehouse-to-select')
                !!}
                <!-- list item -->
                @include ('partials.select_product', ['label'=>'product_id','field_name'=>'product_id','check_item_name'=>'transfer_all_item'])
                <!-- transfer all qty -->
                {!! Former::checkbox('transfer_all_item')->label(trans('texts.allQty'))
                ->value(1)->onchange('transferAllQtyChecked()') !!}
                <!-- qty -->
                {!! Former::text('qty')->label('texts.qty')->help('texts.item_qty_help') !!}

                <!-- NOTES -->
                    {!! Former::textarea('notes')->rows(4) !!}
                </div>
            </div>
        </div>
    </div>

    @if (Auth::user()->canCreateOrEdit(ENTITY_ITEM_TRANSFER, $itemTransfer))
        <center class="buttons">
            {!! Button::normal(trans('texts.cancel'))->large()->asLinkTo(HTMLUtils::previousUrl('/item_transfers'))->appendIcon(Icon::create('remove-circle')) !!}
            @if (!$itemTransfer)
                {!! Button::success(trans('texts.save'))->submit()->large()->appendIcon(Icon::create('floppy-disk')) !!}
            @endif
            @if ($itemTransfer)
                {!! DropdownButton::normal(trans('texts.more_actions'))
                ->withContents($itemTransfer->present()->moreActions())
                ->large()
                ->dropup() !!}
            @endif
        </center>
    @endif

    {!! Former::close() !!}

    <script type="text/javascript">
        var $productModel = $('#product_id');
        var statuses = {!! isset($statuses) ? $statuses:null !!};
        var previousWarehouses = {!! isset($previousWarehouses) ? $previousWarehouses:null !!};
        var currentWarehouses = {!! isset($currentWarehouses) ? $currentWarehouses:null !!};

        var statusMap = {};
        var previousMap = {};
        var currentMap = {};
        $(function () {
            $('#qty').focus();
        });
        $(function () {
            // status
            var statusId = {{ $statusPublicId ?: 0 }};
            var $statusSelect = $('select#status_id');
            @if (Auth::user()->can('create', ENTITY_STATUS))
            $statusSelect.append(new Option("{{ trans('texts.create_status')}}: $name", '-1'));
                    @endif
            for (var i = 0; i < statuses.length; i++) {
                var status = statuses[i];
                statusMap[status.public_id] = status;
                $statusSelect.append(new Option(status.name, status.public_id));
            }
            @include('partials/entity_combobox', ['entityType' => ENTITY_STATUS])
            if (statusId) {
                var status = statusMap[statusId];
                setComboboxValue($('.status-select'), status.public_id, status.name);
            }

            // from warehouse
            var warehouseFromId = {{ $previousWarehousePublicId ?: 0 }};
            var $warehouseSelect = $('select#previous_warehouse_id');
            @if (Auth::user()->can('create', ENTITY_WAREHOUSE))
            $warehouseSelect.append(new Option("{{ trans('texts.create_warehouse')}}: $name", '-1'));
                    @endif
            for (var i = 0; i < previousWarehouses.length; i++) {
                var warehouseFrom = previousWarehouses[i];
                previousMap[warehouseFrom.public_id] = warehouseFrom;
                $warehouseSelect.append(new Option(warehouseFrom.name, warehouseFrom.public_id));
            }
            @include('partials/entity_combobox', ['entityType' => ENTITY_WAREHOUSE])
            if (warehouseFromId) {
                var warehouseFrom = previousMap[warehouseFromId];
                setComboboxValue($('.warehouse-select'), warehouseFrom.public_id, warehouseFrom.name);
            }

            //  current warehouse (to)
            var warehouseToId = {{ $currentWarehousePublicId ?: 0 }};
            var $warehouse_toSelect = $('select#current_warehouse_id');
            @if (Auth::user()->can('create', ENTITY_WAREHOUSE))
            $warehouse_toSelect.append(new Option("{{ trans('texts.create_warehouse_to')}}: $name", '-1'));
                    @endif
            for (var i = 0; i < currentWarehouses.length; i++) {
                var warehouseTo = currentWarehouses[i];
                currentMap[warehouseTo.public_id] = warehouseTo;
                $warehouse_toSelect.append(new Option(warehouseTo.name, warehouseTo.public_id));
            }
            @include('partials/entity_combobox', ['entityType' => ENTITY_WAREHOUSE_TO])
            if (warehouseToId) {
                var warehouseTo = currentMap[warehouseToId];
                setComboboxValue($('.warehouse-to-select'), warehouseTo.public_id, warehouseTo.name);
            }
        });

        function selectProductAction() {
            var $sourceWarehouseId = $('select#previous_warehouse_id').val();
            if ($sourceWarehouseId != '' && $productModel != null) {
                $productModel.empty();
                onSourceWarehouseChanges($productModel, $sourceWarehouseId);
            }
        }

        function onSourceWarehouseValueChanges() {
            // var $sourceWarehouseId = $('select#previous_warehouse_id').val();
            // if ($sourceWarehouseId != '' && $productModel != null) {
            //     $productModel.empty();
            //     onSourceWarehouseChanges($productModel, $sourceWarehouseId);
            // }
        }

        // find items in the selected store.
        function onSourceWarehouseChanges($productModel, $sourceWarehouseId, $item_checked = null) {
            if ($sourceWarehouseId != null && $sourceWarehouseId != '') {
                $.ajax({
                    url: '{{ URL::to('item_stores/item_list') }}',
                    type: 'POST',
                    dataType: "json",
                    data: 'warehouse_id=' + $sourceWarehouseId,
                    success: function (result) {
                        if (result.success) {
                            appendItems($productModel, result.data);
                        } else {
                            swal({!! json_encode(trans('texts.item_does_not_exist')) !!});
                        }
                    },
                    error: function () {
                        swal({!! json_encode(trans('texts.item_does_not_exist')) !!});
                    },
                });
            }
        }

        function appendItems($productModel, $data) {
            if ($productModel != '' && $data != '') {
                if ($data.length > 0) {
                    $productModel.empty();
                    for (var i in $data) {
                        var row = $data[i];
                        $productModel.append("<option value='" + row.id + "' selected>" + row.name + "</option>");
                    }
                }
            }
        }

        function transferAllQtyChecked() {
            var $transferAllQty = $('#transfer_all_item').val();

            if (document.getElementById('transfer_all_item').checked) {
                document.getElementById('qty').value = '';
                document.getElementById('qty').disabled = true;
            } else {
                document.getElementById('qty').disabled = false;
            }
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
    </script>
@stop
