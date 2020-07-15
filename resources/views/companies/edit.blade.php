@extends('header')

@section('content')
@parent
{!! Former::open($url)
    ->method($method)
    ->autocomplete('off')
    ->rules([
    'plan' => 'required|max:255','
    ])
    ->addClass('col-lg-10 col-lg-offset-1 main-form warn-on-exit') !!}
    @if ($company)
    {{ Former::populate($company) }}
    <div style="display:none">
        {!! Former::text('public_id') !!}
    </div>
    @endif

    <span style="display:none">
        {!! Former::text('action') !!}
    </span>

    <div class="row">
        <div class="col-lg-10 col-lg-offset-1">
            <div class="panel panel-default">
                <div class="panel-body form-padding-right">
                    <!-- company name -->
                    {!! Former::text('plan')->label('texts.plan') !!}

                    
                    {!! Former::textarea('notes')->rows(6) !!}
                </div>
            </div>
        </div>
    </div>

    @if (Auth::user()->canCreateOrEdit(ENTITY_COMPANY, $company))
    <center class="buttons">
        {!! Button::normal(trans('texts.cancel'))->large()->asLinkTo(HTMLUtils::previousUrl('/companies'))->appendIcon(Icon::create('remove-circle')) !!}
        {!! Button::success(trans('texts.save'))->submit()->large()->appendIcon(Icon::create('floppy-disk')) !!}
        @if ($company)
        {!! DropdownButton::normal(trans('texts.more_actions'))
        ->withContents($company->present()->moreActions())
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
    <script type="text/javascript">

    </script>
    @stop
