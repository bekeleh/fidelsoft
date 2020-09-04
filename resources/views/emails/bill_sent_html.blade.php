@extends('emails.master_user')

@section('markup')
    @if ($account->emailMarkupEnabled())
        @include('emails.partials.user_view_action')
    @endif
@stop

@section('body')
    <div>
        {{ trans('texts.email_salutation', ['name' => $userName]) }}
    </div>
    &nbsp;
    <div>
        {{ trans("texts.notification_{$entityType}_sent", ['amount' => $billAmount, 'vendor' => $vendorName, 'bill' => $billNumber]) }}
    </div>
    &nbsp;
    <div>
        <center>
            @include('partials.email_button', [
                'link' => $billLink,
                'field' => "view_{$entityType}",
                'color' => '#777',
            ])
        </center>
    </div>
    &nbsp;
    <div>
        {{ trans('texts.email_signature') }} <br/>
        {{ trans('texts.email_from') }}
    </div>
@stop