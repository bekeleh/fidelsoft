@extends('header')

@section('content')
    @parent
    {!! Former::open($url)
        ->method($method)
        ->autocomplete('off')
        ->rules(['bin' => 'required|max:90',
        'product_id' => 'required' ,
        'warehouse_id' => 'required',
        'new_qty' => 'required|numeric',
        'reorder_level' => 'required|numeric',
        'notes' => 'required' ])
        ->addClass('col-lg-10 col-lg-offset-1 main-form warn-on-exit') !!}
    @if ($itemStore)
        {{ Former::populate($itemStore) }}
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
                    {!! Former::select('product_id')->addOption('', '')
                    ->label(trans('texts.product_key'))
                    ->addGroupClass('product-select')
                    ->help(trans('texts.item_help') . ' | ' . link_to('/products/', trans('texts.customize_options')))
                    !!}
                    {!! Former::select('warehouse_id')->addOption('', '')
                    ->label(trans('texts.warehouse_name'))->addGroupClass('warehouse-select')
                    ->help(trans('texts.warehouse_help') . ' | ' . link_to('/warehouses/', trans('texts.customize_options')))
                    !!}

                    {!! Former::text('bin')->label('texts.bin') !!}
                    @if ($itemStore)
                        {!! Former::text('current_qty')->readonly()->value($itemStore->qty)!!}
                    @endif
                    {!! Former::text('new_qty')->label('texts.new_qty') !!}
                    {!! Former::text('reorder_level')->label('texts.reorder_level') !!}
                    {!! Former::text('EOQ')->label('texts.EOQ') !!}
                    {!! Former::textarea('notes')->rows(6) !!}
                </div>
            </div>
        </div>
    </div>
    @if (Auth::user()->canCreateOrEdit(ENTITY_ITEM_STORE, $itemStore))
        <center class="buttons">
            {!! Button::normal(trans('texts.cancel'))->large()->asLinkTo(HTMLUtils::previousUrl('/item_stores'))->appendIcon(Icon::create('remove-circle')) !!}
            {!! Button::success(trans('texts.save'))->submit()->large()->appendIcon(Icon::create('floppy-disk')) !!}
            @if ($itemStore)
                {!! DropdownButton::normal(trans('texts.more_actions'))
                ->withContents($itemStore->present()->moreActions())
                ->large()
                ->dropup() !!}
            @endif
        </center>
    @endif
    {!! Former::close() !!}
    <script type="text/javascript">
        var products = {!! $products !!};
        var warehouses = {!! $warehouses !!};

        var productMap = {};
        var warehouseMap = {};
        $(function () {
            $('#bin').focus();
        });

        $(function () {
//          append product
            var productId = {{ $productPublicId ?: 0 }};
            var $productSelect = $('select#product_id');
            @if (Auth::user()->can('create', ENTITY_PRODUCT))
            $productSelect.append(new Option("{{ trans('texts.create_product')}}: $product_key", '-1'));
                    @endif
            for (var i = 0; i < products.length; i++) {
                var product = products[i];
                productMap[product.public_id] = product;
                $productSelect.append(new Option(product.product_key, product.public_id));
            }
            @include('partials/entity_combobox', ['entityType' => ENTITY_PRODUCT])
            if (productId) {
                var product = productMap[productId];
                setComboboxValue($('.product-select'), product.public_id, product.product_key);
            }
//        default warehouse
            var warehouseId = {{ $warehousePublicId ?: 0 }};
            var $warehouseSelect = $('select#warehouse_id');
            @if (Auth::user()->can('create', ENTITY_WAREHOUSE))
            $warehouseSelect.append(new Option("{{ trans('texts.create_warehouse')}}: $name", '-1'));
                    @endif
            for (var i = 0; i < warehouses.length; i++) {
                var warehouse = warehouses[i];
                warehouseMap[warehouse.public_id] = warehouse;
                $warehouseSelect.append(new Option(warehouse.name, warehouse.public_id));
            }
            @include('partials/entity_combobox', ['entityType' => ENTITY_WAREHOUSE])
            if (warehouseId) {
                var warehouse = warehouseMap[warehouseId];
                setComboboxValue($('.warehouse-select'), warehouse.public_id, warehouse.name);
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
@stop
