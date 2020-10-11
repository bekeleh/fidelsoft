@extends('header')

@section('content')
    @parent

    @include('accounts.nav', ['selected' => ACCOUNT_API_TOKENS, 'advanced' => true])

    <div class="row">
        <div class="col-md-12">
            {{--            {!! Button::normal(trans('texts.documentation'))->asLinkTo(NINJA_WEB_URL.'/api-documentation/')->withAttributes(['target' => '_blank'])->appendIcon(Icon::create('info-sign')) !!}--}}
            {{--            @if (!Utils::isReseller())--}}
            {{--                {!! Button::normal(trans('texts.zapier'))->asLinkTo(ZAPIER_URL)->withAttributes(['target' => '_blank'])->appendIcon(Icon::create('globe')) !!}--}}
            {{--            @endif--}}
            @if (Utils::hasFeature(FEATURE_API))
                {!! Button::primary(trans('texts.add_token'))->asLinkTo(URL::to('/tokens/create'))->appendIcon(Icon::create('plus-sign')) !!}
            @endif
        </div>
    </div>
    <br>
    <div class="row">
        @if (Utils::hasFeature(FEATURE_API))
            <div class="col-md-12">
                @include('list',[
                 'entityType' => ENTITY_TOKEN,
                'datatable' => new \App\Ninja\Datatables\TokenDatatable(true, true),
                'url' => url('api/tokens/'),
                ])
            </div>
        @endif
    </div>

    <script>
        window.onDatatableReady = actionListHandler;
    </script>
    <p>&nbsp;</p>

    {{--    @if (!Utils::isReseller())--}}
    {{--        <p>&nbsp;</p>--}}
    {{--        <script src="https://zapier.com/zapbook/embed/widget.js?guided_zaps=5627,6025,12216,8805,5628,6027&container=false&limit=6"></script>--}}
    {{--    @endif--}}

@stop
