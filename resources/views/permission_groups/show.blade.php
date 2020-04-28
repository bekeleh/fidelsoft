@extends('header')
@section('head')
    @parent
    <script src="{{ asset('js/select2.min.js') }}" type="text/javascript"></script>
    <link href="{{ asset('css/select2.css') }}" rel="stylesheet" type="text/css"/>

@stop

@section('content')
    <div class="panel panel-default">
        <div class="panel-body">
            <div class="row">
                <div class="col-md-6">
                    <ol class="breadcrumb">
                        <li>{{ link_to('/permission_groups', trans('texts.edit_permission_group')) }}</li>
                        <li class='active'>{{ $permissionGroup->name }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <ul class="nav nav-tabs nav-justified">
        {!! Form::tab_link('#activity', trans('texts.activity'), true) !!}
        @if ($permissionGroup)
            {!! Form::tab_link('#permissions', trans('texts.permissions')) !!}
        @endif
        @if (true)
            {!! Form::tab_link('#users', trans('texts.users')) !!}
        @endif
    </ul>
    <br/>
    <div class="tab-content">
        @if (true)
            <div class="tab-pane" id="permissions">
                @include('permission_groups.permission',[
                'permissions' => $permissions,
                'groupPermissions' => $groupPermissions,
                ])
            </div>
        @endif

        @if (true)
            <div class="tab-pane" id="users">
                <h4>user list</h4>
            </div>
        @endif

    </div>
    <script type="text/javascript">
        var loadedTabs = {};
        $(function () {
            $('.normalDropDown:not(.dropdown-toggle)').click(function (event) {
                openUrlOnClick('{{ URL::to('permission_groups/' . $permissionGroup->public_id . '/edit') }}', event);
            });

            // load datatable data when tab is shown and remember last tab selected
            $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
                var target = $(e.target).attr("href") // activated tab
                target = target.substring(1);
                if (isStorageSupported()) {
                    localStorage.setItem('permission_tab', target);
                }
                if (!loadedTabs.hasOwnProperty(target) && window['load_' + target]) {
                    loadedTabs[target] = true;
                    window['load_' + target]();
                }
            });

            var tab = window.location.hash || (localStorage.getItem('permission_tab') || '');
            tab = tab.replace('#', '');
            var selector = '.nav-tabs a[href="#' + tab + '"]';

            if (tab && tab != 'permission' && $(selector).length && window['load_' + tab]) {
                $(selector).tab('show');
            } else {
                // window['load_activity']();
            }
        });

        function onArchiveClick() {
            $('#action').val('archive');
            $('.mainForm').submit();
        }

        function onRestoreClick() {
            $('#action').val('restore');
            $('.mainForm').submit();
        }

        function onDeleteClick() {
            sweetConfirm(function () {
                $('#action').val('delete');
                $('.mainForm').submit();
            });
        }

    </script>
@stop
