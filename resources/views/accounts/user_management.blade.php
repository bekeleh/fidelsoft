@extends('header')
@section('content')
    @parent
    @include('accounts.nav', ['selected' => ACCOUNT_USER_MANAGEMENT, 'advanced' => true])
    @if (Utils::hasFeature(FEATURE_USERS))
        @if (Auth::user()->canAddUsers())
            @include('list',
            [
            'entityType' => ENTITY_USER,
            'datatable' => new \App\Ninja\Datatables\UserDatatable(true, true),
            'url' => url('api/users/'),
            ])
        @endif
    @elseif (Utils::isTrial())
        <div class="alert alert-warning">{!! trans('texts.add_users_not_supported') !!}</div>
    @endif

    <script>
        window.onDatatableReady = actionListHandler;

        function setTrashVisible() {
            var checked = $('#trashed').is(':checked');
            var url = '{{ URL::to('set_entity_filter/user') }}' + (checked ? '/active,archived' : '/active');
            $.get(url, function (data) {
                refreshDatatable();
            })
        }
    </script>
@stop
