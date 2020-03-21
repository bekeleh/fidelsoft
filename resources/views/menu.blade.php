<!-- menu -->
@if (Auth::user()->can('create', [ENTITY_ITEM_PRICE, ENTITY_STORE, ENTITY_ITEM_STORE]))
    {!! DropdownButton::normal(trans('texts.maintenance'))
    ->withAttributes(['class'=>'maintenanceDropdown'])
    ->withContents([
['label' => trans('texts.new_item_price'), 'url' => url('/item_prices/create')],
['label' => trans('texts.new_item_store'), 'url' => url('/item_stores/create')],
['label' => trans('texts.new_item_movement'), 'url' => url('/item_movements/create')],
['label' => trans('texts.new_sale_type'), 'url' => url('/sale_types/create')],
['label' => trans('texts.new_item_category'), 'url' => url('/item_categories/create')],
['label' => trans('texts.new_unit'), 'url' => url('/units/create')],
['label' => trans('texts.new_store'), 'url' => url('/stores/create')],
['label' => trans('texts.new_location'), 'url' => url('/locations/create')]
    ])->split() !!}
@else
    {!! DropdownButton::normal(trans('texts.maintenance'))
->withAttributes(['class'=>'maintenanceDropdown'])
->withContents([
['label' => trans('texts.new_sale_type'), 'url' => url('/sale_types/create')],
['label' => trans('texts.new_item_category'), 'url' => url('/item_categories/create')],
['label' => trans('texts.new_unit'), 'url' => url('/units/create')],
['label' => trans('texts.new_store'), 'url' => url('/stores/create')],
['label' => trans('texts.new_location'), 'url' => url('/locations/create')]
])->split() !!}
@endif
<script type="text/javascript">
    $(function () {
        $('.item_pricesDropdown:not(.dropdown-toggle)').click(function (event) {
            openUrlOnClick('{{ url('/item_prices') }}', event);
        });
    });
    $(function () {
        $('.item_storesDropdown:not(.dropdown-toggle)').click(function (event) {
            openUrlOnClick('{{ url('/item_stores') }}', event);
        });
    });
    $(function () {
        $('.item_movementsDropdown:not(.dropdown-toggle)').click(function (event) {
            openUrlOnClick('{{ url('/item_movements') }}', event);
        });
    });
    $(function () {
        $('.unitsDropdown:not(.dropdown-toggle)').click(function (event) {
            openUrlOnClick('{{ url('/sale_types') }}', event);
        });
    });
    $(function () {
        $('.item_categoriesDropdown:not(.dropdown-toggle)').click(function (event) {
            openUrlOnClick('{{ url('/item_categories') }}', event);
        });
    });
    $(function () {
        $('.unitsDropdown:not(.dropdown-toggle)').click(function (event) {
            openUrlOnClick('{{ url('/units') }}', event);
        });
    });
    $(function () {
        $('.storesDropdown:not(.dropdown-toggle)').click(function (event) {
            openUrlOnClick('{{ url('/stores') }}', event);
        });
    });
    $(function () {
        $('.locationsDropdown:not(.dropdown-toggle)').click(function (event) {
            openUrlOnClick('{{ url('/locations') }}', event);
        });
    });
</script><!-- /. product -->