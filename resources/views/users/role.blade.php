@extends('header')

@section('content')
    @parent
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">{!! trans('texts.permissions') !!}</h3>
        </div>
        <div class="panel-body">
            {!! Former::checkbox('is_admin')
                ->label('&nbsp;')
                ->value(1)
                ->text(trans('texts.administrator'))
                ->help(trans('texts.administrator_help')) !!}
            <div class="panel-body">
                <table class="table table-striped dataTable">
                    <thead>
                    <th></th>
                    <th style="padding-bottom:0px">{!! Former::checkbox('create')
                        ->text( trans('texts.create') )
                        ->value('create_')
                        ->label('&nbsp;')
                        ->id('create_all') !!}
                    </th>
                    <th style="padding-bottom:0px">{!! Former::checkbox('view')
                        ->text( trans('texts.view') )
                        ->value('view_')
                        ->label('&nbsp;')
                        ->id('view_all') !!}
                    </th>
                    <th style="padding-bottom:0px">{!! Former::checkbox('edit')
                        ->text( trans('texts.edit') )
                        ->value('edit_')
                        ->label('&nbsp;')
                        ->id('edit_all') !!}
                    </th>
                    </thead>
                    <tbody>
                    @foreach (json_decode(PERMISSION_ENTITIES,1) as $permissionEntity)
                        <?php
                        if ($user)
                            $permissions = json_decode($user->permissions, 1);
                        else
                            $permissions = [];
                        ?>
                        <tr>
                            <td>{{ ucfirst($permissionEntity) }}</td>
                            <td>{!! Former::checkbox('permissions[create_' . $permissionEntity . ']')
                            ->label('&nbsp;')
                            ->value('create_' . $permissionEntity . '')
                            ->id('create_' . $permissionEntity . '')
                            ->check(is_array($permissions) && in_array('create_' . $permissionEntity, $permissions, FALSE) ? true : false) !!}
                            </td>
                            <td>{!! Former::checkbox('permissions[view_' . $permissionEntity . ']')
                                ->label('&nbsp;')
                                ->value('view_' . $permissionEntity . '')
                                ->id('view_' . $permissionEntity . '')
                                ->check(is_array($permissions) && in_array('view_' . $permissionEntity, $permissions, FALSE) ? true : false) !!}
                            </td>
                            <td>{!! Former::checkbox('permissions[edit_' . $permissionEntity . ']')
                            ->label('&nbsp;')
                            ->value('edit_' .
                             . '')
                            ->id('edit_' . $permissionEntity . '')
                            ->check(is_array($permissions) && in_array('edit_' . $permissionEntity, $permissions, FALSE) ? true : false) !!}
                            </td>
                        </tr>
                    @endforeach
                    <tr>
                        <td>
                            <input type="checkbox" id="view_contact" value="view_contact"
                                   name="permissions[view_contact]" style="display:none">
                            <input type="checkbox" id="edit_contact" value="edit_contact"
                                   name="permissions[edit_contact]" style="display:none">
                            <input type="checkbox" id="create_contact" value="create_contact"
                                   name="permissions[create_contact]" style="display:none"></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    {!! Former::close() !!}
    <script type="text/javascript">
        function submitAction(value) {
            $('#action').val(value);
            $('.user-form').submit();
        }
    </script>
@stop
@section('onReady')
@stop
