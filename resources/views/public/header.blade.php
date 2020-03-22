@extends('master')
@section('head')
    @if (!empty($clientFontUrl))
        <link href="{{ $clientFontUrl }}" rel="stylesheet" type="text/css">
    @endif
    <link href="{{ asset('css/built.public.css') }}?no_cache={{ NINJA_VERSION }}" rel="stylesheet" type="text/css"/>
    <style type="text/css">{!! !empty($account)?$account->clientViewCSS():'' !!}</style>
@stop
@section('body')
    {!! Form::open(array('url' => 'get_started', 'id' => 'startForm')) !!}
    {!! Form::hidden('guest_key') !!}
    {!! Form::hidden('sign_up', Input::get('sign_up')) !!}
    {!! Form::hidden('redirect_to', Input::get('redirect_to')) !!}
    {!! Form::close() !!}
    <script>
        if (isStorageSupported()) {
            $('[name="guest_key"]').val(localStorage.getItem('guest_key'));
        }

        function isStorageSupported() {
            if ('localStorage' in window && window['localStorage'] !== null) {
                var storage = window.localStorage;
            } else {
                return false;
            }
            var testKey = 'test';
            try {
                storage.setItem(testKey, '1');
                storage.removeItem(testKey);
                return true;
            } catch (error) {
                return false;
            }
        }

        function getStarted() {
            $('#startForm').submit();
            return false;
        }

        $(function () {
            function positionFooter() {
                // check that the footer appears at the bottom of the screen
                var height = $(window).height() - ($('#header').height() + $('#footer').height());
                if ($('#mainContent').height() < height) {
                    $('#mainContent').css('min-height', height);
                }
            }

            if (inIframe()) {
                $('#footer').hide();
            } else {
                positionFooter();
                $(window).resize(positionFooter);
            }
        })
    </script>
    <div id="header">
        <nav class="navbar navbar-top navbar-inverse">
            <div class="container">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar"
                            aria-expanded="false" aria-controls="navbar">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    @if (empty($account) || !$account->hasFeature(FEATURE_WHITE_LABEL))
                        <a class="navbar-brand" href="{{ URL::to(NINJA_WEB_URL) }}" target="_blank">
                            {{--                            <img src="{{ asset('images/invoiceninja-logo.png') }}" style="height:27px">--}}
                        </a>
                    @endif
                </div>
                <div id="navbar" class="collapse navbar-collapse">
                    <ul class="nav navbar-nav navbar-right">
                        @if (! empty($account) && $account->enable_client_portal)
                            @if (isset($account) && $account->enable_client_portal_dashboard)
                                <li {!! Request::is('*client/dashboard*') ? 'class="active"' : '' !!}>
                                    {!! link_to('/client/dashboard', trans('texts.dashboard') ) !!}
                                </li>
                            @endif
                            @if (request()->contact && request()->contact->client->show_tasks_in_portal)
                                <li {!! Request::is('*client/tasks') ? 'class="active"' : '' !!}>
                                    {!! link_to('/client/tasks', trans('texts.tasks') ) !!}
                                </li>
                            @endif
                            @if (isset($hasQuotes) && $hasQuotes)
                                <li {!! Request::is('*client/quotes') ? 'class="active"' : '' !!}>
                                    {!! link_to('/client/quotes', trans('texts.quotes') ) !!}
                                </li>
                            @endif
                            <li {!! Request::is('*client/invoices') ? 'class="active"' : '' !!}>
                                {!! link_to('/client/invoices', trans('texts.invoices') ) !!}
                            </li>
                            @if (!empty($account)
                                && $account->hasFeature(FEATURE_DOCUMENTS)
                                && (isset($hasDocuments) && $hasDocuments))
                                <li {!! Request::is('*client/documents') ? 'class="active"' : '' !!}>
                                    {!! link_to('/client/documents', trans('texts.documents') ) !!}
                                </li>
                            @endif
                            <li {!! Request::is('*client/payments') ? 'class="active"' : '' !!}>
                                {!! link_to('/client/payments', trans('texts.payments') ) !!}
                            </li>
                            @if (isset($hasCredits) && $hasCredits)
                                <li {!! Request::is('*client/credits') ? 'class="active"' : '' !!}>
                                    {!! link_to('/client/credits', trans('texts.credits') ) !!}
                                </li>
                            @endif
                            @if (isset($hasPaymentMethods) && $hasPaymentMethods)
                                <li {!! Request::is('*client/payment_methods') ? 'class="active"' : '' !!}>
                                    {!! link_to('/client/payment_methods', trans('texts.payment_methods') ) !!}
                                </li>
                            @endif
                            @if ($account->enable_portal_password && request()->contact->password)
                                <li>
                                    {!! link_to('/client/logout', trans('texts.logout')) !!}
                                </li>
                            @endif
                        @elseif (! empty($account))
                            @if (isset($hasPaymentMethods) && $hasPaymentMethods)
                                <li {!! Request::is('*client/payment_methods') ? 'class="active"' : '' !!}>
                                    {!! link_to('/client/payment_methods', trans('texts.payment_methods') ) !!}
                                </li>
                            @endif
                        @endif
                    </ul>
                </div><!--/.nav-collapse -->
            </div>
        </nav>
        <div class="container">
            @include('partials.warn_session', ['redirectTo' => '/'])
            @if (Session::has('warning'))
                <div class="alert alert-warning">{!! Session::get('warning') !!}</div>
            @endif
            @if (Session::has('message'))
                <div class="alert alert-info">{!! Session::get('message') !!}</div>
            @endif

            @if (Session::has('error'))
                <div class="alert alert-danger">{!! Session::get('error') !!}</div>
            @endif
        </div>
    </div>
    <div id="mainContent" class="container">
        @yield('content')
    </div>
    <footer id="footer" role="contentinfo">
        <div class="bottom">
            <div class="wrap">
                @if (empty($account) || !$account->hasFeature(FEATURE_WHITE_LABEL))
                    <div class="copy">Copyright &copy;{{ date('Y') }}
                        <a href="{{ NINJA_WEB_URL }}" target="_blank">HARON ERP Solution PLC</a>. All rights reserved.
                    </div>
                @endif
            </div><!-- .wrap -->
        </div><!-- .bottom -->
    </footer><!-- #footer -->
@stop
