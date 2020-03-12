@extends('header')

@section('content')
    @parent
    @include('accounts.nav', ['selected' => ACCOUNT_ITEM_STORES])

    {!! Button::primary(trans('texts.create_item_store'))
          ->asLinkTo(URL::to('/item_stores/create'))
          ->withAttributes(['class' => 'pull-right'])
          ->appendIcon(Icon::create('plus-sign')) !!}

    @include('partials.bulk_form', ['entityType' => ENTITY_ITEM_STORE])

    {!! Datatable::table()
        ->addColumn('',
          trans('texts.product'),
          trans('texts.store'),
          trans('texts.bin'),
          trans('texts.qty'),
          trans('texts.notes'),
          trans('texts.created_by'),
          trans('texts.updated_by'),
          trans('texts.created_at'),
          trans('texts.updated_at'),
          trans('texts.action'))
        ->setUrl(url('api/item_stores/'))
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
