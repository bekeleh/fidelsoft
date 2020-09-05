{{ Former::populateField('notify_bill_sent', intval(Auth::user()->notify_bill_sent)) }}
{{ Former::populateField('notify_bill_viewed', intval(Auth::user()->notify_bill_viewed)) }}
{{ Former::populateField('notify_bill_paid', intval(Auth::user()->notify_bill_paid)) }}
{{ Former::populateField('notify_bill_approved', intval(Auth::user()->notify_bill_approved)) }}
{{ Former::populateField('only_notify_bill_owned', intval(Auth::user()->only_notify_bill_owned)) }}

<div class="panel panel-default">
    <div class="panel-heading" style="color:white;background-color: #777 !important;">
        <h3 class="panel-title in-bold-white">{!! trans('texts.bill_email_notifications') !!}</h3>
    </div>
    <div class="panel-body">
        {!! Former::checkbox('notify_bill_sent')->label('&nbsp;')->text(trans('texts.bill_email_sent'))->value(1) !!}
        {!! Former::checkbox('notify_bill_viewed')->label('&nbsp;')->text(trans('texts.bill_email_viewed'))->value(1) !!}
        {!! Former::checkbox('notify_bill_paid')->label('&nbsp;')->text(trans('texts.bill_email_paid'))->value(1) !!}
        {!! Former::checkbox('notify_bill_approved')->label('&nbsp;')->text(trans('texts.bill_email_approved'))->value(1) !!}

        @if (Auth()->user()->account->hasMultipleUsers())
            <br/>
            {!! Former::radios('only_notify_bill_owned')->radios([
                    trans('texts.all_invoices') => array('name' => 'only_notify_bill_owned', 'value' => 0),
                    trans('texts.my_invoices') => array('name' => 'only_notify_bill_owned', 'value' => 1),
                ])->inline()
                    ->label('send_notifications_for') !!}
        @endif
    </div>
</div>
