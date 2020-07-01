@extends('header')

@section('content')
    @parent
    {!! Former::open($url)
    ->method($method)
    ->autocomplete('off')
    ->rules(['product_id' => 'required',
    'client_type_id' => 'required',
    'unit_price' => 'required|numeric',
    'start_date' => 'required|date',
     'end_date' => 'required|date',
     'notes' => 'required', ])
    ->addClass('col-lg-10 col-lg-offset-1 main-form warn-on-exit') !!}

    @if ($itemPrice)
        {{ Former::populate($itemPrice) }}
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
                    <!-- product key -->
                {!! Former::select('product_id')->addOption('', '')
                ->label(trans('texts.product_key'))
                ->addGroupClass('product-select')
                ->help(trans('texts.item_help') . ' | ' . link_to('/products/', trans('texts.customize_options')))
                !!}
                <!-- client type -->
                {!! Former::select('client_type_id')
                ->addOption('', '')
                ->fromQuery($clientTypes, 'name', 'id')
                ->label(trans('texts.client_type_name')) !!}
                <!-- item price -->
                    {!! Former::text('unit_price')->label('texts.unit_price') !!}
                    {!! Former::text('start_date')
                    ->data_date_format(Session::get(SESSION_DATE_PICKER_FORMAT, DEFAULT_DATE_PICKER_FORMAT))
                    ->appendIcon('calendar')
                    ->addGroupClass('start_date')
                    !!}
                    {!! Former::text('end_date')
                    ->data_date_format(Session::get(SESSION_DATE_PICKER_FORMAT, DEFAULT_DATE_PICKER_FORMAT))
                    ->appendIcon('calendar')
                    ->addGroupClass('end_date')
                    !!}

                    {!! Former::textarea('notes')->rows(6) !!}
                </div>
            </div>
        </div>
    </div>
    @if (Auth::user()->canCreateOrEdit(ENTITY_ITEM_PRICE, $itemPrice))
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

        $(function () {
            $('#name').focus();
        });

        $(function () {
            <!-- product -->
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
            }<!-- /. product  -->

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
