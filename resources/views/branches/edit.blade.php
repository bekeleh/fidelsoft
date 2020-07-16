@extends('header')

@section('content')
    @parent
    {!! Former::open($url)
        ->method($method)
        ->autocomplete('off')
        ->rules(['name' => 'required|max:255',
        'warehouse_id' => 'required',
        'location_id' => 'required',
        'notes' => 'required|max:255'])
        ->addClass('col-lg-10 col-lg-offset-1 main-form warn-on-exit') !!}
    @if ($branch)
        {{ Former::populate($branch) }}
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
                    <!-- branch name -->
                {!! Former::text('name')->label('texts.branch_name') !!}
                <!-- branch default warehouse dropdown -->
                {!! Former::select('warehouse_id')->addOption('', '')
                ->label(trans('texts.warehouse_name'))
                ->addGroupClass('warehouse-select')
                ->help(trans('texts.warehouse_help') . ' | ' . link_to('/warehouses/', trans('texts.customize_options')))
                !!}
                <!-- location dropdown -->
                    {!! Former::select('location_id')->addOption('', '')
                    ->label(trans('texts.location_name'))
                    ->addGroupClass('location-select')
                    ->help(trans('texts.location_help') . ' | ' . link_to('/locations/', trans('texts.customize_options')))
                    !!}
                    {!! Former::textarea('notes')->rows(6) !!}
                </div>
            </div>
        </div>
    </div>

    @if (Auth::user()->canCreateOrEdit(ENTITY_BRANCH, $branch))
        <center class="buttons">
            {!! Button::normal(trans('texts.cancel'))->large()->asLinkTo(HTMLUtils::previousUrl('/branches'))->appendIcon(Icon::create('remove-circle')) !!}
            {!! Button::success(trans('texts.save'))->submit()->large()->appendIcon(Icon::create('floppy-disk')) !!}
            @if ($branch)
                {!! DropdownButton::normal(trans('texts.more_actions'))
                ->withContents($branch->present()->moreActions())
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

        function onDeleteClick() {
            sweetConfirm(function () {
                submitAction('delete');
            });
        }
    </script>
    <script type="text/javascript">
        var locations = {!! $locations !!};
        var warehouses = {!! $warehouses !!};

        var locationMap = {};
        var warehouseMap = {};

        $(function () {
            // location dropdown
            var locationId = {{ $locationPublicId ?: 0 }};
            var $locationSelect = $('select#location_id');
            @if (Auth::user()->can('create', ENTITY_LOCATION))
            $locationSelect.append(new Option("{{ trans('texts.create_location')}}: $name", '-1'));
                    @endif
            for (var i = 0; i < locations.length; i++) {
                var location = locations[i];
                locationMap[location.public_id] = location;
                $locationSelect.append(new Option(location.name, location.public_id));
            }
            @include('partials/entity_combobox', ['entityType' => ENTITY_LOCATION])
            if (locationId) {
                var location = locationMap[locationId];
                setComboboxValue($('.location-select'), location.public_id, location.name);
            }
            // warehouse dropdown
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

    </script>
@stop
