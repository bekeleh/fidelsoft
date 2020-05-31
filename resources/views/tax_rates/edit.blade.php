@extends('header')

@section('content')
    @parent
    {!! Former::open($url)
    ->method($method)
    ->autocomplete('off')
    ->rules(['name' => 'required|max:255','rate' => 'required','notes' => 'required' ])
    ->addClass('col-lg-10 col-lg-offset-1 main-form warn-on-exit') !!}
    @if ($taxRate)
        {{ Former::populate($taxRate) }}
        {{ Former::populateField('is_inclusive', intval($taxRate->is_inclusive)) }}
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
                    {!! Former::text('name')->label('texts.tax_rate_name') !!}
                    {!! Former::text('rate')->label('texts.rate')->append('%') !!}
                    {!! Former::radios('is_inclusive')->radios([
                    trans('texts.exclusive') . ': 100 + 10% = 100 + 10' => array('name' => 'is_inclusive', 'value' => 0),
                    trans('texts.inclusive') . ':&nbsp; 100 + 10% = 90.91 + 9.09' => array('name' => 'is_inclusive', 'value' => 1),
                    ])->check(0)
                    ->label('type')
                    ->help('tax_rate_type_help') !!}
                    {!! Former::textarea('notes')->rows(6) !!}
                </div>
            </div>
        </div>
    </div>
    @if (Auth::user()->canCreateOrEdit(ENTITY_TAX_RATE, $taxRate))
        <center class="buttons">
            {!! Button::normal(trans('texts.cancel'))->large()->asLinkTo(HTMLUtils::previousUrl('/tax_rates'))->appendIcon(Icon::create('remove-circle')) !!}
            {!! Button::success(trans('texts.save'))->submit()->large()->appendIcon(Icon::create('floppy-disk')) !!}
            @if ($taxRate)
                {!! DropdownButton::normal(trans('texts.more_actions'))
                ->withContents($taxRate->present()->moreActions())
                ->large()
                ->dropup() !!}
            @endif
        </center>
    @endif
    {!! Former::close() !!}
    <script type="text/javascript">
        $(function () {
            $('#name').focus();
        });

        function submitAction(action) {
            $('#action').val(action);
            $('.main-form').submit();
        }

        function onDeleteClick() {
            sweetConfirm(function () {
                submitAction('delete');
            });
        }
    </script>
@stop
