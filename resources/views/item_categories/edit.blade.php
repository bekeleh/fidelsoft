@extends('header')

@section('content')
    @parent
    {!! Former::open($url)
    ->method($method)
    ->autocomplete('off')
    ->rules(['name' => 'required|max:255','notes' => 'required'])
    ->addClass('col-lg-10 col-lg-offset-1 main-form warn-on-exit') !!}
    @if ($itemCategory)
        {{ Former::populate($itemCategory) }}
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
                    {!! Former::text('name')->label('texts.item_category_name') !!}
                    {!! Former::textarea('notes')->rows(6) !!}
                </div>
            </div>
        </div>
    </div>
    @if (Auth::user()->canCreateOrEdit(ENTITY_ITEM_CATEGORY, $itemCategory))
        <center class="buttons">
            {!! Button::normal(trans('texts.cancel'))->large()->asLinkTo(HTMLUtils::previousUrl('/item_categories'))->appendIcon(Icon::create('remove-circle')) !!}
            {!! Button::success(trans('texts.save'))->submit()->large()->appendIcon(Icon::create('floppy-disk')) !!}
            @if ($itemCategory)
                {!! DropdownButton::normal(trans('texts.more_actions'))
                ->withContents($itemCategory->present()->moreActions())
                ->large()
                ->dropup() !!}
            @endif
        </center>
    @endif
    {!! Former::close() !!}
@stop
