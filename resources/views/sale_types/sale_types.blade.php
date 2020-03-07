@extends('header')

@section('content')
    @parent
    @include('accounts.nav', ['selected' => ACCOUNT_SALE_TYPES])

    {!! Button::primary(trans('texts.create_sale_type'))
          ->asLinkTo(URL::to('/sale_types/create'))
          ->withAttributes(['class' => 'pull-right'])
          ->appendIcon(Icon::create('plus-sign')) !!}

    @include('partials.bulk_form', ['entityType' => ENTITY_SALE_TYPE])

    {!! Datatable::table()
        ->addColumn('',
          trans('texts.name'),
          trans('texts.notes'),
          trans('texts.created_at'),
          trans('texts.updated_at'),
          trans('texts.action'))
        ->setUrl(url('api/sale_types/'))
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
