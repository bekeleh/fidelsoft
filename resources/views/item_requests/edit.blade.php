@extends('header')

@section('content')
    @parent
    {!! Former::open($url)
    ->method($method)
    ->autocomplete('off')
    ->rules(['product_id' => 'required', 'status_id' => 'required', 'store_id' => 'required', 'department_id' => 'required', 'required_date' => 'required', 'qty' => 'required', 'notes' => 'required' ])
    ->addClass('col-lg-10 col-lg-offset-1 main-form warn-on-exit') !!}
    @if ($itemRequest)
        {{ Former::populate($itemRequest) }}
        <div style="display:none">
            {!! Former::text('public_id') !!}
        </div>
    @endif
    <span style="display:none">
{!! Former::text('public_id') !!}
        {!! Former::text('action') !!}
</span>
    <div class="row">
        <div class="col-lg-10 col-lg-offset-1">
            <div class="panel panel-default">
                <div class="panel-body form-padding-right">
                    <!-- product -->
                {!! Former::select('product_id')->addOption('', '')
                ->label(trans('texts.product_name'))->addGroupClass('product-select')
                ->help(trans('texts.product_help') . ' | ' . link_to('/products/', trans('texts.customize_options')))
                !!}
                <!-- department -->
                {!! Former::select('department_id')->addOption('', '')
                ->label(trans('texts.department_name'))->addGroupClass('department-select')
                ->help(trans('texts.department_help') . ' | ' . link_to('/departments/', trans('texts.customize_options')))
                !!}
                <!-- store -->
                {!! Former::select('store_id')->addOption('', '')
                ->label(trans('texts.store_name'))->addGroupClass('store-select')
                ->help(trans('texts.store_help') . ' | ' . link_to('/stores/', trans('texts.customize_options')))
                !!}
                <!-- qty -->
                {!! Former::text('qty')->label('texts.qty') !!}
                <!-- required date -->
                {!! Former::date('required_date')->label('texts.required_date') !!}

                <!-- NOTES -->
                    {!! Former::textarea('notes')->rows(4) !!}
                </div>
            </div>
        </div>
    </div>

    @if (Auth::user()->canCreateOrEdit(ENTITY_ITEM_REQUEST, $itemRequest))
        <center class="buttons">
            {!! Button::normal(trans('texts.cancel'))->large()->asLinkTo(HTMLUtils::previousUrl('/item_requests'))->appendIcon(Icon::create('remove-circle')) !!}
            {!! Button::success(trans('texts.save'))->submit()->large()->appendIcon(Icon::create('floppy-disk')) !!}
            @if ($itemRequest)
                {!! DropdownButton::normal(trans('texts.more_actions'))
                ->withContents($itemRequest->present()->moreActions())
                ->large()
                ->dropup() !!}
            @endif
        </center>
    @endif

    {!! Former::close() !!}

    <script type="text/javascript">
        var products = {!! $products !!};
        var departments = {!! $departments !!};
        var statuses = {!! $statuses !!};
        var stores = {!! $stores !!};

        var productMap = {};
        var departmentMap = {};
        var storeMap = {};
        var statusMap = {};

        $(function () {
            $('#name').focus();
        });

        $(function () {
// product
            var productId = {{ $productPublicId ?: 0 }};
            var $productSelect = $('select#product_id');
            @if (Auth::user()->can('create', ENTITY_PRODUCT))
            $productSelect.append(new Option("{{ trans('texts.create_product')}}: $name", '-1'));
                    @endif
            for (var i = 0; i < products.length; i++) {
                var product = products[i];
                productMap[product.public_id] = product;
                $productSelect.append(new Option(getClientDisplayName(product), product.public_id));
            }
            @include('partials/entity_combobox', ['entityType' => ENTITY_PRODUCT])
            if (productId) {
                var product = productMap[productId];
                setComboboxValue($('.product-select'), product.public_id, product.name);
            }
// department
            var departmentId = {{ $departmentPublicId ?: 0 }};
            var $departmentSelect = $('select#department_id');
            @if (Auth::user()->can('create', ENTITY_DEPARTMENT))
            $departmentSelect.append(new Option("{{ trans('texts.create_department')}}: $name", '-1'));
                    @endif
            for (var i = 0; i < departments.length; i++) {
                var department = departments[i];
                departmentMap[department.public_id] = department;
                $departmentSelect.append(new Option(getClientDisplayName(department), department.public_id));
            }
            @include('partials/entity_combobox', ['entityType' => ENTITY_DEPARTMENT])
            if (departmentId) {
                var department = departmentMap[departmentId];
                setComboboxValue($('.department-select'), department.public_id, department.name);
            }
            // store
            var storeId = {{ $storePublicId ?: 0 }};
            var $storeSelect = $('select#store_id');
            @if (Auth::user()->can('create', ENTITY_STORE))
            $storeSelect.append(new Option("{{ trans('texts.create_store')}}: $name", '-1'));
                    @endif
            for (var i = 0; i < stores.length; i++) {
                var store = stores[i];
                storeMap[store.public_id] = store;
                $storeSelect.append(new Option(getClientDisplayName(store), store.public_id));
            }
            @include('partials/entity_combobox', ['entityType' => ENTITY_STORE])
            if (storeId) {
                var store = storeMap[storeId];
                setComboboxValue($('.store-select'), store.public_id, store.name);
            }
// status
            var statusId = {{ $statusPublicId ?: 0 }};
            var $statusSelect = $('select#status_id');
            @if (Auth::user()->can('create', ENTITY_STATUS))
            $statusSelect.append(new Option("{{ trans('texts.create_status')}}: $name", '-1'));
                    @endif
            for (var i = 0; i < statuses.length; i++) {
                var status = statuses[i];
                statusMap[status.public_id] = status;
                $statusSelect.append(new Option(getClientDisplayName(status), status.public_id));
            }
            @include('partials/entity_combobox', ['entityType' => ENTITY_STATUS])
            if (statusId) {
                var status = statusMap[statusId];
                setComboboxValue($('.status-select'), status.public_id, status.name);
            }

        });

        function selectProductAction() {
// var $storeId = $('select#store_id').val();
// if ($storeId != '' && $productModel != null) {
//     $productModel.empty();
//     onSourceStoreChanges($productModel, $storeId);
// }
        }

        function onSourceStoreValueChanges() {
// var $storeId = $('select#store_id').val();
// if ($storeId != '' && $productModel != null) {
//     $productModel.empty();
//     onSourceStoreChanges($productModel, $storeId);
// }
        }

        // find items in the selected store.
        function onSourceStoreChanges($productModel, $storeId, $item_checked = null) {
            if ($storeId != null && $storeId != '') {
                $.ajax({
                    url: '{{ URL::to('item_stores/item_list') }}',
                    type: 'POST',
                    dataType: "json",
                    data: 'store_id=' + $storeId,
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
