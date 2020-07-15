@extends('header')

@section('content')
@parent
{!! Former::open($url)
    ->method($method)
    ->autocomplete('off')
    ->rules(['name' => 'required|max:255','store_id' => 'required','location_id' => 'required','notes' => 'required|max:255'])
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
                    <!-- branch default store dropdown -->
                    {!! Former::select('store_id')->addOption('', '')
                    ->label(trans('texts.store_name'))
                    ->addGroupClass('store-select')
                    ->help(trans('texts.store_help') . ' | ' . link_to('/stores/', trans('texts.customize_options')))
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
        var stores = {!! $stores !!};

        var locationMap = {};
        var storeMap = {};

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
            // store dropdown
            var storeId = {{ $storePublicId ?: 0 }};
            var $storeSelect = $('select#store_id');
            @if (Auth::user()->can('create', ENTITY_STORE))
            $storeSelect.append(new Option("{{ trans('texts.create_store')}}: $name", '-1'));
            @endif
            for (var i = 0; i < stores.length; i++) {
                var store = stores[i];
                storeMap[store.public_id] = store;
                $storeSelect.append(new Option(store.name, store.public_id));
            }
            @include('partials/entity_combobox', ['entityType' => ENTITY_STORE])
            if (storeId) {
                var store = storeMap[storeId];
                setComboboxValue($('.store-select'), store.public_id, store.name);
            }
        });

    </script>
    @stop
