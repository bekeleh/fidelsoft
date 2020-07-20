@extends('header')

@section('content')
@parent
{!! Former::open($url)
    ->method($method)
    ->autocomplete('off')
    ->rules(['status_id' => 'required', 'dispatch_date' => 'required', 'delivered_qty' => 'required' ])
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
                    <!-- stock request approve -->
                    {!! Former::select('status_id')
                    ->addOption('', '')
                    ->fromQuery($statuses, 'name', 'id')
                    ->label(trans('texts.status_name')) !!}
                    <!-- product -->
                    {!! Former::select('product_id')->addOption('', '')
                    ->label(trans('texts.product_name'))->addGroupClass('product-select')->readonly()
                    ->help(trans('texts.product_help') . ' | ' . link_to('/products/', trans('texts.customize_options')))
                    !!}
                    <!-- department -->
                    {!! Former::select('department_id')->addOption('', '')
                    ->label(trans('texts.department_name'))->addGroupClass('department-select')->readonly()
                    ->help(trans('texts.department_help') . ' | ' . link_to('/departments/', trans('texts.customize_options')))
                    !!}
                    <!-- store -->
                    {!! Former::select('warehouse_id')->addOption('', '')
                    ->label(trans('texts.store_name'))->addGroupClass('store-select')
                    ->help(trans('texts.store_help') . ' | ' . link_to('/warehouses/', trans('texts.customize_options')))
                    !!}
                    <!-- required qty -->
                    {!! Former::text('qty')->label('texts.required_qty')->readonly()!!}
                    <!-- delivered qty -->
                    {!! Former::text('delivered_qty')->label('texts.delivered_qty')!!}
                    <!-- required date -->
                    {!! Former::text('required_date')->label('texts.required_date')->readonly()
                    ->data_date_format(Session::get(SESSION_DATE_PICKER_FORMAT, DEFAULT_DATE_PICKER_FORMAT))
                    !!}
                    <!-- dispatch date -->
                    {!! Former::text('dispatch_date')->required()
                    ->data_date_format(Session::get(SESSION_DATE_PICKER_FORMAT, DEFAULT_DATE_PICKER_FORMAT))
                    ->appendIcon('calendar')
                    ->addGroupClass('dispatch_date')
                    !!}
                    <!-- NOTES -->
                    {!! Former::textarea('notes')->rows(4)->readonly() !!}
                </div>
            </div>
        </div>
    </div>

    @if (Auth::user()->canCreateOrEdit(ENTITY_ITEM_REQUEST, $itemRequest))
    <center class="buttons">
        {!! Button::normal(trans('texts.cancel'))->large()->asLinkTo(HTMLUtils::previousUrl('/item_requests'))->appendIcon(Icon::create('remove-circle')) !!}
        {!! ($itemRequest) ? Button::success(trans('texts.save'))->withAttributes(['onclick' => 'submitAction()'])->large()->appendIcon(Icon::create('floppy-disk')) : false !!}
    </center>
    @endif

    {!! Former::close() !!}

    <script type="text/javascript">
        var products = {!! $products !!};
        var departments = {!! $departments !!};
        var stores = {!! $stores !!};

        var productMap = {};
        var departmentMap = {};
        var storeMap = {};

        $(function () {
// product
var productId = {{ $productPublicId ?: 0 }};
var $productSelect = $('select#product_id');
@if (Auth::user()->can('create', ENTITY_PRODUCT))
$productSelect.append(new Option("{{ trans('texts.create_product')}} : $name", '-1'));
@endif
for (var i = 0; i < products.length; i++) {
    var product = products[i];
    productMap[product.public_id] = product;
    $productSelect.append(new Option(getClientDisplayName(product), product.public_id));
}
@include('partials/entity_combobox', ['entityType' => ENTITY_PRODUCT])
if (productId) {
    var product = productMap[productId];
    setComboboxValue($('.product-select'), product.public_id, product.product_key);
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
var storeId = {{ $warehousePublicId ?: 0 }};
var $storeSelect = $('select#warehouse_id');
@if (Auth::user()->can('create', ENTITY_WAREHOUSE))
$storeSelect.append(new Option("{{ trans('texts.create_store')}}: $name", '-1'));
@endif
for (var i = 0; i < stores.length; i++) {
    var store = stores[i];
    storeMap[store.public_id] = store;
    $storeSelect.append(new Option(getClientDisplayName(store), store.public_id));
}
@include('partials/entity_combobox', ['entityType' => ENTITY_WAREHOUSE])
if (storeId) {
    var store = storeMap[storeId];
    setComboboxValue($('.store-select'), store.public_id, store.name);
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

        function submitAction() {
            var $statusSelect = $('select#status_id').val();
            console.log($statusSelect);
            var $qty = $('#qty').val();
            var $delivered_qty = $('#delivered_qty').val();
            var $dispatch_date = $('#dispatch_date').val();

            var $id ={{$itemRequest->id}};
            var $account_id ={{$itemRequest->account_id}};
            var $public_id ={{$itemRequest->public_id}};

            if ($delivered_qty > $qty) {
                swal("{{trans('texts.item_delivered_qty_error')}}");
            } else if ($delivered_qty == 0) {
                swal("{{trans('texts.error_delivered_qty')}}");
            } else if ($statusSelect == '') {
                swal("{{trans('texts.error_status')}}");
            } else {
                $.ajax({
                    url: '{{ URL::to('/item_requests/approve') }}',
                    type: 'POST',
                    dataType: 'json',
                    data: 'id=' + $id + '&account_id=' + $account_id + '&public_id=' + $public_id + '&status_id=' + $statusSelect + '&delivered_qty=' + $delivered_qty + '&dispatch_date=' + $dispatch_date,
                    success: function (result) {
                        if (result.success) {
                            swal("{{trans('texts.approved_success')}}");
                        }
                    },
                    error: function (result) {
                        if (result) {
                            swal("{{trans('texts.approved_failure')}}");
                        }
                    }
                });
            }
        }

        {{--$('#required_date').datepicker('update', '{{ $itemRequest ? Utils::fromSqlDate($itemRequest->required_date) : '' }}');--}}
        $('#dispatch_date').datepicker('update', '{{ $itemRequest ? Utils::fromSqlDate($itemRequest->dispatch_date) : '' }}');
    </script>
    @stop
