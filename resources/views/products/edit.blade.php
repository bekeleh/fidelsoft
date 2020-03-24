@extends('header')

@section('content')
    @parent
    {!! Former::open($url)
    ->method($method)
    ->autocomplete('off')
    ->rules(['name' => 'required|max:255','item_cost' => 'required|numeric','item_category_id' => 'required|numeric','unit_id' => 'required|numeric','notes' => 'required|string'])
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
                <div class="panel-body form-padding-right">
                    {!! Former::text('name')->label('texts.item_name') !!}

                    {!! Former::select('item_category_id')
                    ->placeholder(trans('texts.select_item_category'))
                    ->label(trans('texts.item_category'))
                    ->addGroupClass('item-category-select') !!}

                    {!! Former::select('unit_id')
                    ->placeholder(trans('texts.select_item_unit'))
                    ->label(trans('texts.unit'))
                    ->addGroupClass('unit-select') !!}

                    {!! Former::text('barcode')->label('texts.barcode') !!}
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

    @foreach(Module::getOrdered() as $module)
        @if(View::exists($module->alias . '::products.edit'))
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
                            @includeIf($module->alias . '::products.edit')
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endforeach

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
        var categories = {!! $itemCategories !!};
        var units = {!! $units !!};
        var categoryMap = {};
        var unitMap = {};

        $(function () {
            $('#name').focus();
        });

        $(function () {
            <!-- category -->
            var categoryId = {{ $itemCategoryPublicId ?: 0 }};
            var $item_categorySelect = $('select#item_category_id');
            @if (Auth::user()->can('create', ENTITY_ITEM_CATEGORY))
            $item_categorySelect.append(new Option("{{ trans('texts.create_item_category')}}:$name", '-1'));
                    @endif
            for (var i = 0; i < categories.length; i++) {
                var category = categories[i];
                categoryMap[category.public_id] = category;
                $item_categorySelect.append(new Option(getClientDisplayName(category), category.public_id));
            }
            @include('partials/entity_combobox', ['entityType' => ENTITY_ITEM_CATEGORY])
            if (categoryId) {
                var category = categoryMap[categoryId];
                setComboboxValue($('.item-category-select'), category.public_id, category.name);
            }
            <!-- /. category  -->

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
