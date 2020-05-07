@extends('header')

@section('content')
    @parent
    @include('accounts.nav', ['selected' => ACCOUNT_BANKS])

    @if (isset($warnPaymentGateway) && $warnPaymentGateway)
        <div class="alert alert-warning">{!! trans('texts.warn_payment_gateway', ['link' => link_to('/gateways/create', trans('texts.click_here'))]) !!}</div>
    @endif

    @if (Auth::user()->hasFeature(FEATURE_EXPENSES))
        <div class="pull-right">
            {!! Button::normal(trans('texts.import_ofx'))
                ->asLinkTo(URL::to('/bank_accounts/import_ofx'))
                ->appendIcon(Icon::create('open')) !!}
            {!! Button::primary(trans('texts.add_bank_account'))
                ->asLinkTo(URL::to('/bank_accounts/create'))
                ->appendIcon(Icon::create('plus-sign')) !!}
        </div>
    @endif

    @include('partials.bulk_form', ['entityType' => ENTITY_BANK_ACCOUNT])

    {!! Datatable::table()
        ->addColumn(
            trans('texts.bank_name'),
            trans('texts.integration_type'),
            trans('texts.action'))
        ->setUrl(url('api/bank_accounts/'))
        ->setOptions('sPaginationType', 'bootstrap')
        ->setOptions('bFilter', true)
        ->setOptions('bAutoWidth', true)
        ->setOptions('aoColumnDefs', [['bSortable'=>true, 'aTargets'=>[1]]])
        ->render('datatable') !!}

    <script>
        window.onDatatableReady = actionListHandler;
    </script>

@stop
