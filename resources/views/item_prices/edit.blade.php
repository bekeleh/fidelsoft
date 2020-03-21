@extends('header')

@section('content')
    @parent
    {!! Former::open($url)
    ->method($method)
    ->autocomplete('off')
    ->rules(['product_id' => 'required','sale_type_id' => 'required','item_price' => 'required|numeric','start_date' => 'required|date', 'end_date' => 'required|date','notes' => 'required', ])
    ->addClass('col-lg-10 col-lg-offset-1 main-form warn-on-exit') !!}
    @if ($itemPrice)
        {{ Former::populate($itemPrice) }}
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
                    {!! Former::select('product_id')->addOption('', '')
                    ->label(trans('texts.product_name'))
                    ->addGroupClass('product-select') !!}
                    {!! Former::select('sale_type_id')->addOption('', '')
                    ->label(trans('texts.sale_type'))
                    ->addGroupClass('sale-type-select') !!}

                    {!! Former::text('item_price')->label('texts.item_price') !!}

                    {!! Former::text('start_date')
                    ->data_date_format(Session::get(SESSION_DATE_PICKER_FORMAT, DEFAULT_DATE_PICKER_FORMAT))
                    ->appendIcon('calendar')
                    ->addGroupClass('start_date')
                    !!}
                    {!! Former::text('end_date')
                    ->data_date_format(Session::get(SESSION_DATE_PICKER_FORMAT, DEFAULT_DATE_PICKER_FORMAT))
                    ->addGroupClass('end_date')
                    ->appendIcon('calendar')
                    !!}

                    {!! Former::textarea('notes')->rows(6) !!}
                </div>
            </div>
        </div>
    </div>

    @foreach(Module::getOrdered() as $module)
        @if(View::exists($module->alias . '::item_prices.edit'))
            <div class="row">
                <div class="col-lg-10 col-lg-offset-1">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title in-white">
                                <i class="fa fa-{{ $module->icon }}"></i>
                                {{ $module->name}}
                            </h3>
                        </div>
                        <div class="panel-body form-padding-right">
                            @includeIf($module->alias . '::item_prices.edit')
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endforeach
    @if (Auth::user()->canCreateOrEdit(ENTITY_SALE_TYPE, $itemPrice))
        <center class="buttons">
            {!! Button::normal(trans('texts.cancel'))->large()->asLinkTo(HTMLUtils::previousUrl('/item_prices'))->appendIcon(Icon::create('remove-circle')) !!}
            {!! Button::success(trans('texts.save'))->submit()->large()->appendIcon(Icon::create('floppy-disk')) !!}
            @if ($itemPrice)
                {!! DropdownButton::normal(trans('texts.more_actions'))
                ->withContents($itemPrice->present()->moreActions())
                ->large()
                ->dropup() !!}
            @endif
        </center>
    @endif
    {!! Former::close() !!}
    <script type="text/javascript">
        var products = {!! $products !!};
        var productMap = {};
        <!-- types type -->
        var types = {!! $saleTypes !!};
        var typeMap = {};

        $(function () {
            $('#name').focus();
        });

        $(function () {
            <!-- product -->
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
            }<!-- /. product  -->

            var typeId = {{ $saleTypePublicId ?: 0 }};
            var $sale_typeSelect = $('select#sale_type_id');
            @if (Auth::user()->can('create', ENTITY_SALE_TYPE))
            $sale_typeSelect.append(new Option("{{ trans('texts.create_sale_type')}}: $name", '-1'));
                    @endif
            for (var i = 0; i < types.length; i++) {
                var type = types[i];
                typeMap[type.public_id] = type;
                $sale_typeSelect.append(new Option(type.name, type.public_id));
            }
            @include('partials/entity_combobox', ['entityType' => ENTITY_SALE_TYPE])
            if (typeId) {
                var type = typeMap[typeId];
                setComboboxValue($('.sale-type-select'), type.public_id, type.name);
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

        $('#start_date').datepicker('update', '{{ $itemPrice ? Utils::fromSqlDate($itemPrice->start_date) : '' }}');
        $('#end_date').datepicker('update', '{{ $itemPrice ? Utils::fromSqlDate($itemPrice->end_date) : '' }}');
    </script>
@stop
