@extends('header')

@section('content')
    @parent
    {!! Former::open($url)
    ->method($method)
    ->autocomplete('off')
    ->rules(['name' => 'required|max:255','item_cost' => 'required|numeric','item_brand_id' => 'required|numeric','unit_id' => 'required|numeric','notes' => 'required|string'])
    ->addClass('col-lg-10 col-lg-offset-1 main-form warn-on-exit') !!}
    @if ($product)
        {{ Former::populate($product) }}
        {{ Former::populateField('item_cost', Utils::roundSignificant($product->item_cost)) }}
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
                <div class="panel-heading" style="color:white;background-color: #777 !important;">
                    <h3 class="panel-title in-bold-white"><h3>
                    {!! Former::text('name')->label('texts.item_name') !!}
                    {!! Former::select('item_brand_id')
                    ->placeholder(trans('texts.select_item_brand'))
                    ->label(trans('texts.item_brand'))
                    ->addGroupClass('item-brand-select')
                    ->help(trans('texts.item_brand_help') . ' | ' . link_to('/item_brands/', trans('texts.customize_options')))
                    !!}
                    {!! Former::select('unit_id')
                    ->placeholder(trans('texts.select_item_unit'))
                    ->label(trans('texts.unit'))
                    ->addGroupClass('unit-select')
                    ->help(trans('texts.item_unit_help') . ' | ' . link_to('/units/', trans('texts.customize_options')))
                    !!}

                    {!! Former::text('item_barcode')->label('texts.item_barcode') !!}
                    {!! Former::text('item_tag')->label('texts.item_tag') !!}
                    {!! Former::text('item_cost')->label('item_cost') !!}
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
        var brands = {!! $itemBrands !!};
        var units = {!! $units !!};
        var brandMap = {};
        var unitMap = {};

        $(function () {
            $('#name').focus();
        });

        $(function () {
            <!-- brand -->
            var brandId = {{ $itemBrandPublicId ?: 0 }};
            var $item_brandSelect = $('select#item_brand_id');
            @if (Auth::user()->can('create', ENTITY_ITEM_BRAND))
            $item_brandSelect.append(new Option("{{ trans('texts.create_item_brand')}}:$name", '-1'));
                    @endif
            for (var i = 0; i < brands.length; i++) {
                var brand = brands[i];
                brandMap[brand.public_id] = brand;
                $item_brandSelect.append(new Option(getClientDisplayName(brand), brand.public_id));
            }
            @include('partials/entity_combobox', ['entityType' => ENTITY_ITEM_BRAND])
            if (brandId) {
                var brand = brandMap[brandId];
                setComboboxValue($('.item-brand-select'), brand.public_id, brand.name);
            }
            <!-- /. brand  -->

            <!--  unit  -->
            var unitId = {{ $unitPublicId ?: 0 }};
            var $unitSelect = $('select#unit_id');
            @if (Auth::user()->can('create', ENTITY_UNIT))
            $unitSelect.append(new Option("{{ trans('texts.create_unit')}}:$name", '-1'));
                    @endif
            for (var i = 0; i < units.length; i++) {
                var unit = units[i];
                unitMap[unit.public_id] = unit;
                $unitSelect.append(new Option(getClientDisplayName(unit), unit.public_id));
            }
            @include('partials/entity_combobox', ['entityType' => ENTITY_UNIT])
            if (unitId) {
                var unit = unitMap[unitId];
                setComboboxValue($('.unit-select'), unit.public_id, unit.name);
            }
        });<!-- /. item unit  -->

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
