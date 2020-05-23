@extends('header')

@section('content')

    {!! Former::open($url)
            ->addClass('col-lg-10 col-lg-offset-1 warn-on-exit')
            ->method($method)
            ->rules([
                'name' => 'required',
                'notes' => 'required',
            ]) !!}

    @if ($scheduleCategory)
        {!! Former::populate($scheduleCategory) !!}
    @endif

    <span style="display:none">
        {!! Former::text('public_id') !!}
    </span>

    <div class="row">
        <div class="col-lg-10 col-lg-offset-1">
            <div class="panel panel-default">
                <div class="panel-body">
                    {!! Former::text('name')->label('texts.schedule_category_name') !!}
                    {!! Former::text('text_color')->label('texts.text_color') !!}
                    {!! Former::text('bg_color')->label('texts.bg_color') !!}
                    {!! Former::text('notes')->label('texts.notes') !!}
                </div>
            </div>
        </div>
    </div>

    <center class="buttons">
        {!! Button::normal(trans('texts.cancel'))->large()->asLinkTo(HTMLUtils::previousUrl('/schedule_categories'))->appendIcon(Icon::create('remove-circle')) !!}
        {!! Button::success(trans('texts.save'))->submit()->large()->appendIcon(Icon::create('floppy-disk')) !!}
        @if ($scheduleCategory && Auth::user()->can('create', ENTITY_EXPENSE))
            {!! Button::primary(trans('texts.new_schedule_category'))->large()
                    ->asLinkTo(url("/schedule_categories/create/0/0/{$scheduleCategory->public_id}"))
                    ->appendIcon(Icon::create('plus-sign')) !!}
        @endif
    </center>

    {!! Former::close() !!}

    <script>
        $(function () {
            $('#name').focus();
        });
    </script>

@stop
