@extends('header')

@section('content')
    @parent
    {!! Former::open($url)
    ->method($method)
    ->autocomplete('off')
    ->rules(['name' => 'required|max:255',
    'cost' => 'required|numeric',
    'item_brand_id' => 'required|numeric',
    'item_type_id' => 'required|numeric',
    'tax_category_id' => 'required|numeric',
    'unit_id' => 'required|numeric',
    'notes' => 'required|string'])
    ->addClass('col-lg-10 col-lg-offset-1 main-form warn-on-exit') !!}

    @if ($product)
        {{ Former::populate($product) }}
        {{ Former::populateField('cost', Utils::roundSignificant($product->cost)) }}
    @endif
    <span style="display:none">
        {!! Former::text('action') !!}
    </span>
    <div class="row">
        <div class="col-lg-10 col-lg-offset-1">
            <div class="panel panel-default">
                <div class="panel-body form-padding-right">
                    <!-- item code-->
                {!! Former::text('product_key')->label(trans('texts.product_key')) !!}
                {!! Former::text('item_serial')->label(trans('texts.item_serial')) !!}
                {!! Former::text('item_barcode')->label(trans('texts.item_barcode')) !!}
                {!! Former::text('item_tag')->label(trans('texts.item_tag')) !!}
                {!! Former::text('upc')->label(trans('texts.upc')) !!}
                {!! Former::text('cost')->label(trans('texts.cost')) !!}
                <!-- item brand-->
                {!! Former::select('item_brand_id')
                ->placeholder(trans('texts.select_item_brand'))
                ->label(trans('texts.item_brand'))
                ->addGroupClass('item-brand-select')
                ->help(trans('texts.item_brand_help') . ' | ' . link_to('/item_brands/', trans('texts.customize_options')))
                !!}
                <!-- item type product/service-->
                {!! Former::select('item_type_id')
                ->fromQuery($itemTypes, 'name', 'id')
                ->addOption('','')
                ->label(trans('texts.item_type_name'))
                !!}
                <!-- tax category-->
                {!! Former::select('tax_category_id')
                ->fromQuery($taxCategories, 'name', 'id')
                ->addOption('','')
                ->label(trans('texts.tax_category_name'))
                !!}
                <!-- unit of measure-->
                {!! Former::select('unit_id')
                ->fromQuery($units, 'name', 'id')
                ->addOption('','')
                ->label(trans('texts.unit_name'))
                !!}
                <!-- product notes -->
                    {!! Former::textarea('notes')->rows(6) !!}
                    @include('partials/custom_fields', ['entityType' => ENTITY_PRODUCT])
                    @if ($account->invoice_item_taxes)
                        @include('partials.tax_rates')
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if (Auth::user()->canCreateOrEdit(ENTITY_PRODUCT, $product))
        <center class="buttons">
            {!! Button::normal(trans('texts.cancel'))->large()->asLinkTo(HTMLUtils::previousUrl('/products'))->appendIcon(Icon::create('remove-circle')) !!}
            {!! Button::success(trans('texts.save'))->submit()->large()->appendIcon(Icon::create('floppy-disk')) !!}
            @if ($product)
                {!! DropdownButton::normal(trans('texts.more_actions'))
                ->withContents($product->present()->moreActions())
                ->large()
                ->dropup() !!}
            @endif
        </center>
    @endif

    {!! Former::close() !!}
    <script type="text/javascript">
        var itemBrands = {!! $itemBrands !!};
        var itemBrandMap = {};

        $(function () {
            $('#name').focus();
        });

        $(function () {
            <!-- item brand -->
            var itemBrandId = {{ $itemBrandPublicId ?: 0 }};
            var $item_brandSelect = $('select#item_brand_id');
            @if (Auth::user()->can('create', ENTITY_ITEM_BRAND))
            $item_brandSelect.append(new Option("{{ trans('texts.create_item_brand')}}:$name", '-1'));
                    @endif
            for (var i = 0; i < itemBrands.length; i++) {
                var itemBrand = itemBrands[i];
                itemBrandMap[itemBrand.public_id] = itemBrand;
                $item_brandSelect.append(new Option(getClientDisplayName(itemBrand), itemBrand.public_id));
            }

            @include('partials/entity_combobox', ['entityType' => ENTITY_ITEM_BRAND])
            if (itemBrandId) {
                var itemBrand = itemBrandMap[itemBrandId];
                setComboboxValue($('.item-brand-select'), itemBrand.public_id, itemBrand.name);
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
        });

    </script>
@stop
