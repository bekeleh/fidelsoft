@extends('header')

@section('content')
    @parent
    {!! Former::open($url)
    ->method($method)
    ->autocomplete('off')
    ->rules(['name' => 'required|max:90','product_id' => 'required','notes' => 'required'])
    ->addClass('col-lg-10 col-lg-offset-1 main-form warn-on-exit') !!}
    @if ($invoiceItem)
        {{ Former::populate($invoiceItem) }}
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
                    {!! Former::text('name')->label('texts.item_name') !!}
                    {!! Former::select('item_id')->addOption('', '')
                    ->label(trans('texts.item'))
                    ->addGroupClass('item-select')
                    ->help(trans('texts.product_help') . ' | ' . link_to('/products/', trans('texts.customize_options')))
                    !!}
                    {!! Former::textarea('notes')->rows(6) !!}
                </div>
            </div>
        </div>
    </div>

    @if (Auth::user()->canCreateOrEdit(ENTITY_PRODUCT, $invoiceItem))
        <center class="buttons">
            {!! Button::normal(trans('texts.cancel'))->large()->asLinkTo(HTMLUtils::previousUrl('/item_brands'))->appendIcon(Icon::create('remove-circle')) !!}
            {!! Button::success(trans('texts.save'))->submit()->large()->appendIcon(Icon::create('floppy-disk')) !!}
            @if ($invoiceItem)
                {!! DropdownButton::normal(trans('texts.more_actions'))
                ->withContents($invoiceItem->present()->moreActions())
                ->large()
                ->dropup() !!}
            @endif
        </center>
    @endif
    {!! Former::close() !!}
    <script type="text/javascript">
        $(function () {
            $('#name').focus();
        });

        function submitAction(action) {
            $('#action').val(action);
            $('.main-form').submit();
        }
    </script>
    <script type="text/javascript">
        var products = {!! $products !!};
        var productMap = {};

        $(function () {
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
                setComboboxValue($('.product-select'), product.public_id, product.product_key);
            }
        });

    </script>
@stop
