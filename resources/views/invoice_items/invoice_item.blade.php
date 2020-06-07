@extends('header')

@section('content')
    @parent
    {!! Former::open($url)
    ->method($method)
    ->autocomplete('off')
    ->rules(['name' => 'required|max:90','item_category_id' => 'required','notes' => 'required'])
    ->addClass('col-lg-10 col-lg-offset-1 main-form warn-on-exit') !!}
    @if ($invoiceItem)
        {{ Former::populate($invoiceItem) }}
        {{ Former::populateField('qty','0.00') }}
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
                    {!! Former::text('name')->label('texts.item_brand') !!}
                    {!! Former::select('item_category_id')->addOption('', '')
                    ->label(trans('texts.item_category'))
                    ->addGroupClass('item-category-select')
                    ->help(trans('texts.item_category_help') . ' | ' . link_to('/item_categories/', trans('texts.customize_options')))
                    !!}
                    {!! Former::textarea('notes')->rows(6) !!}
                </div>
            </div>
        </div>
    </div>

    @if (Auth::user()->canCreateOrEdit(ENTITY_ITEM_CATEGORY, $invoiceItem))
        <center class="buttons">
            {!! Button::normal(trans('texts.cancel'))->large()->asLinkTo(HTMLUtils::previousUrl('/item_brands'))->appendIcon(Icon::create('remove-circle')) !!}
            {!! Button::success(trans('texts.save'))->submit()->large()->appendIcon(Icon::create('floppy-disk')) !!}
            @if ($invoiceItem)
                {!! DropdownButton::normal(trans('texts.more_actions'))
                ->withContents($invoiceItem->present()->moreActions())
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
        var categories = {!! $itemCategories !!};
        var categoryMap = {};

        $(function () {
            var categoryId = {{ $itemCategoryPublicId ?: 0 }};
            var $item_categorySelect = $('select#item_category_id');
            @if (Auth::user()->can('create', ENTITY_ITEM_CATEGORY))
            $item_categorySelect.append(new Option("{{ trans('texts.create_item_category')}}: $name", '-1'));
                    @endif
            for (var i = 0; i < categories.length; i++) {
                var category = categories[i];
                categoryMap[category.public_id] = category;
                $item_categorySelect.append(new Option(getClientDisplayName(category), category.public_id));
            }
            @include('partials/entity_combobox', ['entityType' => ENTITY_ITEM_CATEGORY])
            if (categoryId) {
                var category = categoryMap[categoryId];
                setComboboxValue($('.item-category-select'), category.public_id, category.name);
            }
        });

    </script>
@stop
