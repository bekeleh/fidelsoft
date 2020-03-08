@extends('header')

@section('content')
    @parent
    @include('accounts.nav', ['selected' => ACCOUNT_LOCATIONS])

    {!! Button::primary(trans('texts.create_location'))
          ->asLinkTo(URL::to('/locations/create'))
          ->withAttributes(['class' => 'pull-right'])
          ->appendIcon(Icon::create('plus-sign')) !!}

    @include('partials.bulk_form', ['entityType' => ENTITY_LOCATION])

    {!! Datatable::table()
        ->addColumn('',
          trans('texts.name'),
          trans('texts.code'),
          trans('texts.notes'),
          trans('texts.created_by'),
          trans('texts.updated_by'),
          trans('texts.created_at'),
          trans('texts.updated_at'),
          trans('texts.action'))
        ->setUrl(url('api/locations/'))
        ->setOptions('sPaginationType', 'bootstrap')
        ->setOptions('bFilter', true)
        ->setOptions('bAutoWidth', true)
        ->setOptions('aoColumnDefs', [['bSortable'=>false, 'aTargets'=>[2]]])
        ->render('datatable') !!}
    <script>
        window.onDatatableReady = actionListHandler;
    </script>
    <script type="text/javascript">
        $(function () {
        })
    </script>

@stop
