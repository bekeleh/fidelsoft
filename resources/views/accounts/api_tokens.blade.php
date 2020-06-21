@extends('header')

@section('content')
    @parent

    @include('accounts.nav', ['selected' => ACCOUNT_API_TOKENS, 'advanced' => true])
    <div class="row">
        <div class="col-md-12">
            {!! Button::normal(trans('texts.documentation'))->asLinkTo(NINJA_WEB_URL.'/api-documentation/')->withAttributes(['target' => '_blank'])->appendIcon(Icon::create('info-sign')) !!}
            @if (!Utils::isReseller())
                {!! Button::normal(trans('texts.zapier'))->asLinkTo(ZAPIER_URL)->withAttributes(['target' => '_blank'])->appendIcon(Icon::create('globe')) !!}
            @endif
            @if (Utils::hasFeature(FEATURE_API))
                {!! Button::primary(trans('texts.add_token'))->asLinkTo(URL::to('/tokens/create'))->appendIcon(Icon::create('plus-sign')) !!}
            @endif
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            @include('partials.bulk_form', ['entityType' => ENTITY_TOKEN])
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            {!! Datatable::table()
                ->addColumn(
                  trans('texts.name'),
                  trans('texts.token'),
                  trans('texts.action'))
                ->setUrl(url('api/tokens/'))
                ->setOptions('sPaginationType', 'bootstrap')
                ->setOptions('bFilter', false)
                ->setOptions('bAutoWidth', false)
                ->setOptions('aoColumns', [[ "sWidth"=> "40%" ], [ "sWidth"=> "40%" ], ["sWidth"=> "20%"]])
                ->setOptions('aoColumnDefs', [['bSortable'=>false, 'aTargets'=>[2]]])
                ->render('datatable') !!}
        </div>
    </div>
    <h3 style="height:20px;"></h3>
    <div class="row">
        <div class="col-md-12">
            @if (Utils::hasFeature(FEATURE_API))
                {!! Button::primary(trans('texts.add_subscription'))->asLinkTo(URL::to('/subscriptions/create'))->appendIcon(Icon::create('plus-sign')) !!}
            @endif
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            @include('partials.bulk_form', ['entityType' => ENTITY_SUBSCRIPTION])
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            {!! Datatable::table()
                ->addColumn(
                  trans('texts.event'),
                  trans('texts.target_url'),
                  trans('texts.action'))
                ->setUrl(url('api/subscriptions/'))
                ->setOptions('sPaginationType', 'bootstrap')
                ->setOptions('bFilter', false)
                ->setOptions('bAutoWidth', false)
                ->setOptions('aoColumns', false)
                ->setOptions('aoColumnDefs', [['bSortable'=>false, 'aTargets'=>[2]]])
                ->render('datatable') !!}
        </div>
    </div>
    <script>
        window.onDatatableReady = actionListHandler;
    </script>
    <p>&nbsp;</p>

    @if (!Utils::isReseller())
        <p>&nbsp;</p>
        <script src="https://zapier.com/zapbook/embed/widget.js?guided_zaps=5627,6025,12216,8805,5628,6027&container=false&limit=6"></script>
    @endif

@stop
