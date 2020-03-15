@extends('header')

@section('content')
    @parent
    {!! Former::open($url)
            ->method($method)
            ->autocomplete('off')
            ->rules(['name' => 'required|max:255'])
            ->addClass('col-lg-10 col-lg-offset-1 main-form warn-on-exit') !!}

    @if ($unit)
        {{ Former::populate($unit) }}
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
                    {!! Former::text('name')->label('texts.name') !!}
                    {!! Former::textarea('notes')->rows(6) !!}
                </div>
            </div>
        </div>
    </div>

    @foreach(Module::getOrdered() as $module)
        @if(View::exists($module->alias . '::units.edit'))
            <div class="row">
                <div class="col-lg-10 col-lg-offset-1">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title in-white">
                                <i class="fa fa-{{ $module->icon }}"></i>
                                {{ $module->name}}
                            </h3>
                        </div>
                        <div class="panel-body form-padding-right">
                            @includeIf($module->alias . '::units.edit')
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endforeach
    @if (Auth::user()->canCreateOrEdit(ENTITY_units, $unit))
        <center class="buttons">
            {!! Button::normal(trans('texts.cancel'))->large()->asLinkTo(HTMLUtils::previousUrl('/units'))->appendIcon(Icon::create('remove-circle')) !!}
            {!! Button::success(trans('texts.save'))->submit()->large()->appendIcon(Icon::create('floppy-disk')) !!}
            @if ($unit)
                {!! DropdownButton::normal(trans('texts.more_actions'))
                        ->withContents($unit->present()->moreActions())
                        ->large()
                        ->dropup() !!}
            @endif
        </center>
    @endif
    {!! Former::close() !!}
@stop
