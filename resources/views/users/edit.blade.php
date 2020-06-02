@extends('header')

@section('content')
    @parent
    {!! Former::open($url)
    ->method($method)
    ->autocomplete('off')
    ->rules(['first_name' => 'required|max:50','last_name' => 'required|max:50','username' => 'required|max:50','email' => 'required|email|max:50','branch_id' => 'required','location_id' => 'required','notes' => 'required|max:255'])
    ->addClass('col-lg-10 col-lg-offset-1 main-form warn-on-exit') !!}
    @if ($user)
        {{ Former::populate($user) }}
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
                {!! Former::text('first_name')->label('texts.first_name') !!}
                {!! Former::text('last_name')->label('texts.last_name') !!}
                {!! Former::text('username')->label('texts.username') !!}
                {!! Former::text('email')->label('texts.email') !!}
                {!! Former::text('phone')->label('texts.phone') !!}
                <!-- default user branch-->
                {!! Former::select('branch_id')
                ->placeholder(trans('texts.select_branch'))
                ->label(trans('texts.branch_name'))
                ->addGroupClass('branch-select')
                  ->help(trans('texts.branch_help') . ' | ' . link_to('/branches/', trans('texts.customize_options')))
                !!}
                <!-- default user location-->
                {!! Former::select('location_id')
                ->placeholder(trans('texts.select_location'))
                ->label(trans('texts.location_name'))
                ->addGroupClass('location-select')
                  ->help(trans('texts.location_help') . ' | ' . link_to('/locations/', trans('texts.customize_options')))
                !!}
                <!-- activate user -->
                {!! Former::checkbox('activated')->label('activated')->text(trans('texts.activated'))->value(1) !!}
                <!-- notes -->
                {!! Former::textarea('notes')->rows(4) !!}
                <!-- user permission_groups -->
                    {!! Former::label('permission_groups', trans('texts.group'))!!}
                    {!! Form::select('permission_groups[]', $groups, $userGroups, ['class' => 'form-control padding-right', 'multiple' => 'multiple']) !!}
                    @if($errors->has('permission_groups') )
                        <div class="alert alert-danger" role="alert">
                            One or more of the groups you selected are empty/invalid. Please try again.
                        </div>
                    @endif
                    <div class="col-md-7">
                        {{ link_to('/permission_groups', trans('texts.group_permission_help')) }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    @if (Auth::user()->canCreateOrEdit(ENTITY_USER, $user))
        <center class="buttons">
            {!! Button::normal(trans('texts.cancel'))->large()->asLinkTo(HTMLUtils::previousUrl('/users'))->appendIcon(Icon::create('remove-circle')) !!}
            {!! Button::success(trans('texts.save'))->submit()->large()->appendIcon(Icon::create('floppy-disk')) !!}
            @if ($user)
                {!! DropdownButton::normal(trans('texts.more_actions'))
                ->withContents($user->present()->moreActions())
                ->large()
                ->dropup() !!}
            @endif
        </center>
    @endif
    {!! Former::close() !!}
    <script type="text/javascript">
        var locations = {!! $locations !!};
        var branches = {!! $branches !!};

        var branchMap = {};
        var locationMap = {};

        $(function () {
            $('#name').focus();
        });

        $(function () {
            <!-- user location -->
            var locationId = {{ $locationPublicId ?: 0 }};
            var $locationSelect = $('select#location_id');
            @if (Auth::user()->can('create', ENTITY_LOCATION))
            $locationSelect.append(new Option("{{ trans('texts.create_location')}}: $name", '-1'));
                    @endif
            for (var i = 0; i < locations.length; i++) {
                var location = locations[i];
                locationMap[location.public_id] = location;
                $locationSelect.append(new Option(getClientDisplayName(location), location.public_id));
            }
            @include('partials/entity_combobox', ['entityType' => ENTITY_LOCATION])
            if (locationId) {
                var location = locationMap[locationId];
                setComboboxValue($('.location-select'), location.public_id, location.name);
            }<!-- /. user location  -->

            <!-- default branch -->
            var branchId = {{ $branchPublicId ?: 0 }};
            var $branchSelect = $('select#branch_id');
            @if (Auth::user()->can('create', ENTITY_BRANCH))
            $branchSelect.append(new Option("{{ trans('texts.create_branch')}}: $name", '-1'));
                    @endif
            for (var i = 0; i < branches.length; i++) {
                var branch = branches[i];
                branchMap[branch.public_id] = branch;
                $branchSelect.append(new Option(getClientDisplayName(branch), branch.public_id));
            }
            @include('partials/entity_combobox', ['entityType' => ENTITY_BRANCH])
            if (branchId) {
                var branch = branchMap[branchId];
                setComboboxValue($('.branch-select'), branch.public_id, branch.name);
            }<!-- /. default branch  -->

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
