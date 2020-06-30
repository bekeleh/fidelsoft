{{--@if ($errors->any())--}}
{{--    <div class="col-xs-12">--}}
{{--        <div class="alert alert-danger fade in">--}}
{{--            <button type="button" class="close" data-dismiss="alert">&times;</button>--}}
{{--            <i class="fa fa-exclamation-circle faa-pulse animated"></i>--}}
{{--            <strong>Error: </strong>--}}
{{--            Please check the form below for errors--}}
{{--        </div>--}}
{{--    </div>--}}
{{--@endif--}}

{{--@if ($message = Session::get('status'))--}}
{{--    <div class="col-xs-12">--}}
{{--        <div class="alert alert-success fade in">--}}
{{--            <button type="button" class="close" data-dismiss="alert">&times;</button>--}}
{{--            <i class="fa fa-check faa-pulse animated"></i>--}}
{{--            <strong>Success: </strong>--}}
{{--            {!! $message !!}--}}
{{--        </div>--}}
{{--    </div>--}}
{{--@endif--}}

{{--@if ($message = Session::get('message'))--}}
{{--    <div class="col-xs-12">--}}
{{--        <div class="alert alert-success fade in">--}}
{{--            <button type="button" class="close" data-dismiss="alert">&times;</button>--}}
{{--            <i class="fa fa-check faa-pulse animated"></i>--}}
{{--            <strong>Message: </strong>--}}
{{--            {!! $message !!}--}}
{{--        </div>--}}
{{--    </div>--}}
{{--@endif--}}

{{--@if ($message = Session::get('success'))--}}
{{--    <div class="col-xs-12">--}}
{{--        <div class="alert alert-success fade in">--}}
{{--            <button type="button" class="close" data-dismiss="alert">&times;</button>--}}
{{--            <i class="fa fa-check faa-pulse animated"></i>--}}
{{--            <strong>Success: </strong>--}}
{{--            {!! $message !!}--}}
{{--        </div>--}}
{{--    </div>--}}
{{--@endif--}}

{{--@if ($message = Session::get('error'))--}}
{{--    <div class="col-xs-12">--}}
{{--        <div class="alert alert alert-danger fade in">--}}
{{--            <button type="button" class="close" data-dismiss="alert">&times;</button>--}}
{{--            <i class="fa fa-exclamation-circle faa-pulse animated"></i>--}}
{{--            <strong>Error: </strong>--}}
{{--            {!! $message !!}--}}
{{--        </div>--}}
{{--    </div>--}}
{{--@endif--}}

{{--@if ($message = Session::get('warning'))--}}
{{--    <div class="col-xs-12">--}}
{{--        <div class="alert alert-warning fade in">--}}
{{--            <button type="button" class="close" data-dismiss="alert">&times;</button>--}}
{{--            <i class="fa fa-warning faa-pulse animated"></i>--}}
{{--            <strong>Warning: </strong>--}}
{{--            {!! $message !!}--}}
{{--        </div>--}}
{{--    </div>--}}
{{--@endif--}}

{{--@if ($message = Session::get('info'))--}}
{{--    <div class="col-xs-12">--}}
{{--        <div class="alert alert-info fade in">--}}
{{--            <button type="button" class="close" data-dismiss="alert">&times;</button>--}}
{{--            <i class="fa fa-info-circle faa-pulse animated"></i>--}}
{{--            <strong>Info: </strong>--}}
{{--            {!! $message !!}--}}
{{--        </div>--}}
{{--    </div>--}}
{{--@endif--}}


@include('partials.warn_session', ['redirectTo' => '/dashboard'])

@if (Session::has('warning'))
    <div class="alert alert-warning">{!! Session::get('warning') !!}</div>
@elseif (env('WARNING_MESSAGE'))
    <div class="alert alert-warning">{!! env('WARNING_MESSAGE') !!}</div>
@endif

@if (Session::has('message'))
    <div class="alert alert-info alert-hide" style="z-index:9999">
        {{ Session::get('message') }}
    </div>
@elseif (Session::has('news_feed_message'))
    <div class="alert alert-info">
        {!! Session::get('news_feed_message') !!}
        <a href="#" onclick="hideMessage()" class="pull-right">{{ trans('texts.hide') }}</a>
    </div>
@endif

@if (Session::has('error'))
    <div class="alert alert-danger">{!! Session::get('error') !!}</div>
@endif

