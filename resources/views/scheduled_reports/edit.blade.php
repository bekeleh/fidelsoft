@extends('header')

@section('content')

    {!! Former::open($url)
            ->addClass('col-lg-10 col-lg-offset-1 warn-on-exit')
            ->method($method)
            ->rules([
                'ip' => 'required',
                'frequency' => 'required',
            ]) !!}

    @if ($scheduledReport)
        {!! Former::populate($scheduledReport) !!}
    @endif

    <span style="display:none">
        {!! Former::text('public_id') !!}
    </span>

    <div class="row">
        <div class="col-lg-10 col-lg-offset-1">
            <div class="panel panel-default">
                <div class="panel-body">
                    {!! Former::text('ip')->label('texts.ip') !!}
                    {!! Former::text('frequency')->label('texts.frequency') !!}
                    {!! Former::date('send_date')->label('texts.send_date') !!}
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
