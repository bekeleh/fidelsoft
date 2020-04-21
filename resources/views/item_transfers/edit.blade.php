@extends('header')

@section('content')
    @parent
    {!! Former::open($url)
    ->method($method)
    ->autocomplete('off')
    ->rules(['product_id' => 'required' ,'previous_previous_id' => 'required' ,'current_previous_id' => 'required','qty' => 'required|numeric','notes' => 'required' ])
    ->addClass('col-lg-10 col-lg-offset-1 main-form warn-on-exit') !!}
    @if ($itemTransfer)
        {{ Former::populate($itemTransfer) }}
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
                    {!! Former::select('product_id')->addOption('', '')
                    ->label(trans('texts.product'))
                    ->addGroupClass('product-select')
                    ->help(trans('texts.item_help') . ' | ' . link_to('/products/', trans('texts.customize_options')))
                    !!}
                    {!! Former::select('previous_previous_id')->addOption('', '')
                    ->label(trans('texts.from_store_name'))->addGroupClass('previous-select')
                    ->help(trans('texts.item_store_help') . ' | ' . link_to('/item_stores/', trans('texts.customize_options')))
                    !!}
                    {!! Former::select('current_previous_id')->addOption('', '')
                    ->label(trans('texts.to_store_name'))->addGroupClass('current-select')
                    !!}
                    {!! Former::text('qty')->label('texts.qty') !!}
                    {!! Former::textarea('notes')->rows(6) !!}
                </div>
            </div>
        </div>
    </div>

    @foreach(Module::getOrdered() as $module)
        @if(View::exists($module->alias . '::item_transfers.edit'))
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
                            @includeIf($module->alias . '::item_transfers.edit')
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endforeach
    @if (Auth::user()->canCreateOrEdit(ENTITY_ITEM_STORE, $itemTransfer))
        <center class="buttons">
            {!! Button::normal(trans('texts.cancel'))->large()->asLinkTo(HTMLUtils::previousUrl('/item_transfers'))->appendIcon(Icon::create('remove-circle')) !!}
            {!! Button::success(trans('texts.save'))->submit()->large()->appendIcon(Icon::create('floppy-disk')) !!}
            @if ($itemTransfer)
                {!! DropdownButton::normal(trans('texts.more_actions'))
                ->withContents($itemTransfer->present()->moreActions())
                ->large()
                ->dropup() !!}
            @endif
        </center>
    @endif
    {!! Former::close() !!}
    <script type="text/javascript">
        var products = {!! $products !!};
        var previousStores = {!! $previousStores !!};
        var currentStores = {!! $currentStores !!};

        var productMap = {};
        var previousMap = {};
        var currentMap = {};
        $(function () {
            $('#qty').focus();
        });

        $(function () {
            var productId = {{ $productPublicId ?: 0 }};
            var $productSelect = $('select#product_id');
            @if (Auth::user()->can('create', ENTITY_PRODUCT))
            $productSelect.append(new Option("{{ trans('texts.create_product')}}: $name", '-1'));
                    @endif
            for (var i = 0; i < products.length; i++) {
                var product = products[i];
                productMap[product.public_id] = product;
                $productSelect.append(new Option(getClientDisplayName(product), product.public_id));
            }
            @include('partials/entity_combobox', ['entityType' => ENTITY_PRODUCT])
            if (productId) {
                var product = productMap[productId];
                setComboboxValue($('.product-select'), product.public_id, product.name);
            }
//          previous store
            var previousId = {{ $previousStorePublicId ?: 0 }};
            var $previousSelect = $('select#previous_store_id');
            @if (Auth::user()->can('create', ENTITY_STORE))
            $previousSelect.append(new Option("{{ trans('texts.create_store')}}: $name", '-1'));
                    @endif
            for (var i = 0; i < previousStores.length; i++) {
                var previous = previousStores[i];
                previousMap[previous.public_id] = previous;
                $previousSelect.append(new Option(getClientDisplayName(previous), previous.public_id));
            }
            @include('partials/entity_combobox', ['entityType' => ENTITY_STORE])
            if (previousId) {
                var previous = previousMap[previousId];
                setComboboxValue($('.previous-select'), previous.public_id, previous.name);
            }
            // current store
            var currentId = {{ $currentStorePublicId ?: 0 }};
            var $currentSelect = $('select#current_store_id');
            @if (Auth::user()->can('create', ENTITY_STORE))
            $currentSelect.append(new Option("{{ trans('texts.create_store')}}: $name", '-1'));
                    @endif
            for (var i = 0; i < currentStores.length; i++) {
                var current = currentStores[i];
                currentMap[current.public_id] = current;
                $currentSelect.append(new Option(getClientDisplayName(current), current.public_id));
            }
            @include('partials/entity_combobox', ['entityType' => ENTITY_STORE])
            if (currentId) {
                var current = currentMap[currentId];
                setComboboxValue($('.current-select'), current.public_id, current.name);
            }
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
