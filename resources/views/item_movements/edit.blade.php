@extends('header')

@section('content')
    @parent
    {!! Former::open($url)
    ->method($method)
    ->autocomplete('off')
    ->rules([])
    ->addClass('col-lg-10 col-lg-offset-1 main-form warn-on-exit') !!}
    <!-- notification -->
    @include('notifications')
    @if ($itemMovement)
        {{ Former::populate($itemMovement) }}
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
                    {!! Former::text('qty')->label('texts.qty') !!}
                    {!! Former::text('qoh')->label('texts.qoh') !!}
                    {!! Former::textarea('notes')->rows(6) !!}
                </div>
            </div>
        </div>
    </div>

    @foreach(Module::getOrdered() as $module)
        @if(View::exists($module->alias . '::item_movements.edit'))
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
                            @includeIf($module->alias . '::item_movements.edit')
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endforeach

    @if (Auth::user()->canCreateOrEdit(ENTITY_ITEM_MOVEMENT, $itemMovement))
        <center class="buttons">
            {!! Button::normal(trans('texts.cancel'))->large()->asLinkTo(HTMLUtils::previousUrl('/item_movements'))->appendIcon(Icon::create('remove-circle')) !!}
            {!! Button::success(trans('texts.save'))->submit()->large()->appendIcon(Icon::create('floppy-disk')) !!}
            @if ($itemMovement)
                {!! DropdownButton::normal(trans('texts.more_actions'))
                ->withContents($itemMovement->present()->moreActions())
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
