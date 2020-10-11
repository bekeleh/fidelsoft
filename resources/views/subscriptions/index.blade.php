@extends('header')

@section('content')
    @parent

    @include('accounts.nav', ['selected' => ACCOUNT_API_TOKENS, 'advanced' => true])
    <h3 style="height:20px;"></h3>
    <br/>
    <div class="row">
        <div class="col-md-12">
            @include('list',
            [
            'entityType' => ENTITY_SUBSCRIPTION,
            'datatable' => new \App\Ninja\Datatables\SubscriptionDatatable(true, true),
            'url' => url('api/subscriptions/'),
            ])

        </div>
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
