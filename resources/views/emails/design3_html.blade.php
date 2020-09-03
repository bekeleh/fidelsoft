@extends('emails.master')

@section('markup')
    @if ($account->emailMarkupEnabled())
        @include('emails.partials.client_view_action')
    @endif
@stop

@section('content')
    <tr>
        <td bgcolor="#F4F5F5" style="border-collapse: collapse;">&nbsp;</td>
    </tr>
    <tr>
        <td style="border-collapse: collapse;">
            @if ($entityType == ENTITY_INVOICE)
                @include('emails.partials.invoice_body')
            @else
                @include('emails.partials.bill_body')
            @endif
        </td>
    </tr>
    <tr>
        <td class="content" style="border-collapse: collapse;">
            <div style="font-size: 18px; margin: 42px 40px 42px; padding: 0; max-width: 520px;">{!! $body !!}</div>
        </td>
    </tr>
@stop

@section('footer')
    <p style="color: #A7A6A6; font-size: 13px; line-height: 18px; margin: 0 0 7px; padding: 0;">
        @if (! $account->isPaid())
            {!! trans('texts.ninja_email_footer', ['site' => link_to(NINJA_WEB_URL . '?utm_source=email_footer', APP_NAME)]) !!}
        @else
            {{ $account->present()->address }}
            <br/>
            @if ($account->website)
                <strong><a href="{{ $account->present()->website }}"
                           style="color: #A7A6A6; text-decoration: none; font-weight: bold; font-size: 10px;">{{ $account->website }}</a></strong>
            @endif
        @endif
    </p>
@stop
