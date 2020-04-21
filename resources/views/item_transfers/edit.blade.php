@extends('header')

@section('content')
    @parent
    {!! Former::open($url)
    ->method($method)
    ->autocomplete('off')
    ->rules(['product_id' => 'required' ,'previous_store_id' => 'required' ,'current_store_id' => 'required','qty' => 'required|numeric','notes' => 'required' ])
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
                    {!! Former::select('previous_store_id')->addOption('', '')
                    ->label(trans('texts.previousStore'))->addGroupClass('previous-select')
                    ->help(trans('texts.store_help') . ' | ' . link_to('/stores/', trans('texts.customize_options')))
                    !!}
                    {!! Former::select('current_store_id')->addOption('', '')
                    ->label(trans('texts.currentStore'))->addGroupClass('current-select')
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

        var productMap = {};
        var storeMap = {};
        $(function () {
            $('#bin').focus();
        });

        $(function () {
// product
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
// store
            var storeId = {{ $storePublicId ?: 0 }};
            var $storeSelect = $('select#previous_store_id');
            @if (Auth::user()->can('create', ENTITY_STORE))
            $storeSelect.append(new Option("{{ trans('texts.create_store')}}: $name", '-1'));
                    @endif
            for (var i = 0; i < stores.length; i++) {
                var store = stores[i];
                storeMap[store.public_id] = store;
                $storeSelect.append(new Option(getClientDisplayName(store), store.public_id));
            }
            @include('partials/entity_combobox', ['entityType' => ENTITY_STORE])
            if (storeId) {
                var store = storeMap[storeId];
                setComboboxValue($('.store-select'), store.public_id, store.name);
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
