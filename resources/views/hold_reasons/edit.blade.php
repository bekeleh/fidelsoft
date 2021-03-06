@extends('header')

@section('content')
    @parent
    {!! Former::open($url)
    ->method($method)
    ->autocomplete('off')
    ->rules(['name' => 'required|max:255','allow_invoice' => 'required','notes' => 'required|max:255'])
    ->addClass('col-lg-10 col-lg-offset-1 main-form warn-on-exit') !!}
    @if ($holdReason)
        {{ Former::populate($holdReason) }}
        <div style="display:none">
            {!! Former::text('public_id') !!}
        </div>
    @endif

    <span style="display:none">
{!! Former::text('public_id') !!}
        {!! Former::text('action') !!}
</span>

    <div class="row">
        <div class="col-lg-10 col-lg-offset-1">
            <div class="panel panel-default">
                <div class="panel-body form-padding-right">
                    {!! Former::text('name')->label('texts.hold_reason') !!}
                    {!! Former::select('allow_invoice')
                             ->placeholder(trans('texts.select_hold_reason'))
                             ->fromQuery(\App\Models\HoldReason::allowInvoice(), 'name', 'id') !!}
                    {!! Former::textarea('notes')->rows(6) !!}
                </div>
            </div>
        </div>
    </div>

    @if (Auth::user()->canCreateOrEdit(ENTITY_HOLD_REASON, $holdReason))
        <center class="buttons">
            {!! Button::normal(trans('texts.cancel'))->large()->asLinkTo(HTMLUtils::previousUrl('/hold_reasons'))->appendIcon(Icon::create('remove-circle')) !!}
            {!! Button::success(trans('texts.save'))->submit()->large()->appendIcon(Icon::create('floppy-disk')) !!}
            @if ($holdReason)
                {!! DropdownButton::normal(trans('texts.more_actions'))
                ->withContents($holdReason->present()->moreActions())
                ->large()
                ->dropup() !!}
            @endif
        </center>
    @endif
    {!! Former::close() !!}
@stop
