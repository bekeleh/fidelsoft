@extends('master')

@section('head')
    @if (!empty($clientauth) && $fontsUrl = Utils::getAccountFontsUrl())
        <link href="{{ $fontsUrl }}" rel="stylesheet" type="text/css">
    @endif
    <link href="{{ asset('css/built.public.css') }}?no_cache={{ NINJA_VERSION }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('css/bootstrap.min.css') }}?no_cache={{ NINJA_VERSION }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('css/built.css') }}?no_cache={{ NINJA_VERSION }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('css/built.login.css') }}?no_cache={{ NINJA_VERSION }}" rel="stylesheet" type="text/css"/>

    @if (!empty($clientauth))
        <style type="text/css">{!! Utils::clientViewCSS() !!}</style>
    @endif
@endsection

@section('body')
    <div class="container-fluid">
        <div class="row header">
            <div class="col-md-6 col-xs-12 text-center">
                <a href="https://www.fidel.com.et/" target="_blank" style="color:white;">
                    <img width="193" height="25" src="{{ asset('images/fidel-logo.png') }}"/>
                </a>
            </div>
            <div class="col-md-6 text-right visible-lg">
                <p>{{trans('texts.tag_line')}}</p>
            </div>
        </div>
    </div>

    <!-- login form -->
    @yield('form')

@endsection
