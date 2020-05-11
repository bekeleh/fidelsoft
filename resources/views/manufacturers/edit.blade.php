@extends('header')

@section('content')
    @parent
    {!! Former::open($url)
            ->method($method)
            ->autocomplete('off')
            ->rules(['name' => 'required|max:255', 'notes' => 'required'])
            ->addClass('col-lg-10 col-lg-offset-1 main-form warn-on-exit')
             !!}
    <!-- Main content area-->
    @if ($manufacturer)
        {{ Former::populate($manufacturer) }}
    @endif

    <span style="display:none">
        {!! Former::text('public_id') !!}
        {!! Former::text('action') !!}
    </span>

    <div class="row">
        <div class="col-lg-10 col-lg-offset-1">
            <div class="panel panel-default">
                <div class="panel-body form-padding-right">
                    {!! Former::text('name')->label('texts.manufacturer_name') !!}
                    {!! Former::textarea('notes')->rows(6) !!}
                </div>
            </div>
        </div>
    </div>

    @if (Auth::user()->canCreateOrEdit(ENTITY_MANUFACTURER, $manufacturer))
        <center class="buttons">
            {!! Button::normal(trans('texts.cancel'))->large()->asLinkTo(HTMLUtils::previousUrl('/manufacturers'))->appendIcon(Icon::create('remove-circle')) !!}
            {!! Button::success(trans('texts.save'))->submit()->large()->appendIcon(Icon::create('floppy-disk')) !!}
            @if ($manufacturer)
                {!! DropdownButton::normal(trans('texts.more_actions'))
                        ->withContents($manufacturer->present()->moreActions())
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
@stop
