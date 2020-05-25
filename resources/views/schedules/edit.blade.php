@extends('header')

@section('content')

    {!! Former::open($url)
            ->addClass('col-lg-10 col-lg-offset-1 warn-on-exit')
            ->method($method)
            ->rules([
                'title' => 'required',
                'description' => 'required',
            ]) !!}

    @if ($schedule)
        {!! Former::populate($schedule) !!}
    @endif

    <span style="display:none">
        {!! Former::text('public_id') !!}
    </span>

    <div class="row">
        <div class="col-lg-10 col-lg-offset-1">
            <div class="panel panel-default">
                <div class="panel-body">
                    {!! Former::text('title')->label('texts.title') !!}
                    {!! Former::text('description')->label('texts.description') !!}
                    {!! Former::text('rrule')->label('texts.rrule') !!}
                    {!! Former::text('url')->label('texts.url') !!}
                    {!! Former::textarea('notes')->rows(6)->label('texts.notes') !!}
                </div>
            </div>
        </div>
    </div>

    <center class="buttons">
        {!! Button::normal(trans('texts.cancel'))->large()->asLinkTo(HTMLUtils::previousUrl('/schedules'))->appendIcon(Icon::create('remove-circle')) !!}
        {!! Button::success(trans('texts.save'))->submit()->large()->appendIcon(Icon::create('floppy-disk')) !!}
    </center>

    {!! Former::close() !!}

    <script>
        $(function () {
            $('#name').focus();
        });
    </script>

@stop
