<!-- permission list -->

<style>
    .form-horizontal .control-label {
        padding-top: 0px;
    }

    input[type='text'][disabled], input[disabled], textarea[disabled], input[readonly], textarea[readonly], .form-control[disabled], .form-control[readonly], fieldset[disabled] .form-control {
        background-color: white;
        color: #555555;
        cursor: text;
    }

    table.permissions {
        display: flex;
        flex-direction: column;
    }

    .permissions.table > thead, .permissions.table > tbody {
        margin: 15px;
        margin-top: 0px;
    }

    .permissions.table > tbody + tbody {

    }

    .header-row {
        border-bottom: 1px solid #ccc;
    }

    .header-row h3 {
        margin: 0px;
    }

    .permissions-row {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
    }

    .table > tbody > tr > td.permissions-item {
        padding: 1px;
        padding-left: 8px;
    }

    .header-name {
        cursor: pointer;
    }

</style>

<table class="table table-striped permissions">
    <thead>
    <tr class="permissions-row">
        <th class="col-md-5">Permission</th>
        <th class="col-md-1">Grant</th>
        <th class="col-md-1">Deny</th>
        <th class="col-md-1">Inherit</th>
    </tr>
    </thead>
    <tbody>
    @foreach ($permissions as $area => $permissionsArray)
        <?php $localPermission = $permissionsArray[0]; ?>
        <table class="table table-striped dataTable">
            <thead>
            <th>{{ $area }}</th>
            <th style="padding-bottom:0px">
                {!! Former::radio(''.$area.'')
                        ->text( trans('texts.grant') )
                        ->value(1)
                        ->label('&nbsp;')
                        ->id(''. $area . '')!!}
            </th>
            <th style="padding-bottom:0px">
                {!! Former::radio(''.$area.'')
                        ->text( trans('texts.deny') )
                        ->value(-1)
                        ->label('&nbsp;')
                        ->id(''. $area . '')!!}
            </th>
            <th style="padding-bottom:0px">
                {!! Former::radio(''.$area.'')
                        ->text( trans('texts.inherit') )
                        ->value(0)
                        ->label('&nbsp;')
                        ->id(''. $area . '')!!}
            </th>
            </thead>
            @foreach ($permissionsArray as $index => $permission)
                <tr>
                    <td>{{ ucwords($permission['label']) }}</td>
                    <td>{!! Former::radio('' . $permission['permission'] . '')
                            ->label('&nbsp;')
                            ->value(1)
                            ->id('' . $permission['permission'] . '')
                            ->check(['value'=>"grant", 'class'=>'minimal', 'data-checker-group' => str_slug($area)])
                             !!}
                    </td>
                    <td>{!! Former::radio('' . $permission['permission'] . '')
                            ->label('&nbsp;')
                            ->value(-1)
                            ->id('' . $permission['permission'] . '')
                           ->check(['value'=>"deny", 'class'=>'minimal', 'data-checker-group' => str_slug($area)])
                             !!}

                    </td>
                    <td> {!! Former::radio('' . $permission['permission'] . '')
                            ->label('&nbsp;')
                            ->value(0)
                            ->id('' . $permission['permission'] . '')
                            ->check(['value'=>"inherit", 'class'=>'minimal', 'data-checker-group' => str_slug($area)])
                             !!}
                    </td>
                </tr>
            @endforeach
        </table>
    @endforeach
    </tbody>
</table>