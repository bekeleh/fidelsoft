@extends('header')

@section('content')
    @parent
    {!! Former::open($url)
    ->method($method)
    ->autocomplete('off')
    ->rules(['product_id' => 'required' ,'previous_store_id' => 'required' ,'current_store_id' => 'required','notes' => 'required' ])
    ->addClass('col-lg-10 col-lg-offset-1 main-form warn-on-exit') !!}
    @if ($itemTransfer)
        {{ Former::populate($itemTransfer) }}
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
                    <!-- from store -->
                {!! Former::select('previous_store_id')->addOption('', '')
                ->onchange('selectProductAction()')
                ->label(trans('texts.from_store_name'))->addGroupClass('store-select')
                ->help(trans('texts.store_help') . ' | ' . link_to('/stores/', trans('texts.customize_options')))
                !!}
                <!-- to store -->
                {!! Former::select('current_store_id')->addOption('', '')
                ->label(trans('texts.to_store_name'))->addGroupClass('store-select')
                !!}
                @include ('partials.select_product', ['label'=>'product_id','field_name'=>'product_id','check_item_name'=>'transfer_all_item'])
                <!-- qty -->
                {!! Former::text('qty')->label('texts.qty')->help('texts.item_qty_help') !!}
                <!-- NOTES -->
                    {!! Former::textarea('notes')->rows(2) !!}
                </div>
            </div>
        </div>
    </div>

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
        var $productModel = $('#product_id');
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
            var storeFromId = {{ $previousStorePublicId ?: 0 }};
            var $storeSelect = $('select#previous_store_id');
            @if (Auth::user()->can('create', ENTITY_STORE))
            $storeSelect.append(new Option("{{ trans('texts.create_store')}}: $name", '-1'));
                    @endif
            for (var i = 0; i < previousStores.length; i++) {
                var storeFrom = previousStores[i];
                previousMap[storeFrom.public_id] = storeFrom;
                $storeSelect.append(new Option(getClientDisplayName(storeFrom), storeFrom.public_id));
            }
            @include('partials/entity_combobox', ['entityType' => ENTITY_STORE])
            if (storeFromId) {
                var storeFrom = previousMap[storeFromId];
                setComboboxValue($('.store-select'), storeFrom.public_id, storeFrom.name);
            }

//        current store (to)
            var storeToId = {{ $currentStorePublicId ?: 0 }};
            var $store_toSelect = $('select#current_store_id');
            @if (Auth::user()->can('create', ENTITY_STORE))
            $store_toSelect.append(new Option("{{ trans('texts.create_store_to')}}: $name", '-1'));
                    @endif
            for (var i = 0; i < currentStores.length; i++) {
                var storeTo = currentStores[i];
                currentMap[storeTo.public_id] = storeTo;
                $store_toSelect.append(new Option(getClientDisplayName(storeTo), storeTo.public_id));
            }
            @include('partials/entity_combobox', ['entityType' => ENTITY_STORE_TO])
            if (storeToId) {
                var storeTo = currentMap[storeToId];
                setComboboxValue($('.store-to-select'), storeTo.public_id, storeTo.name);
            }
        });

        function selectProductAction() {
            var $sourceStoreId = $('select#previous_store_id').val();
            if ($sourceStoreId != '' && $productModel != null) {
                $productModel.empty();
                onSourceStoreChange($productModel, $sourceStoreId);
            }
        }

        // find items in the selected store.
        function onSourceStoreChange($productModel, $sourceStoreId, $item_checked = null) {
            if ($sourceStoreId != null && $sourceStoreId != '') {
                $.ajax({
                    url: '{{ URL::to('item_stores/item_list') }}',
                    type: 'POST',
                    dataType: "json",
                    data: 'store_id=' + $sourceStoreId,
                    success: function (result) {
                        if (result.success) {
                            appendItems($productModel, result.data);
                        } else {
                            swal({!! json_encode(trans('texts.item_does_not_exist')) !!});
                        }
                    },
                    error: function () {
                        swal({!! json_encode(trans('texts.item_does_not_exist')) !!});
                    },
                });
            }
        }

        function appendItems($productModel, $data) {
            if ($productModel != '' && $data != '') {
                if ($data.length > 0) {
                    $productModel.empty();
                    for (var i in $data) {
                        var row = $data[i];
                        $productModel.append("<option value='" + row.id + "' selected>" + row.name + "</option>");
                    }
                }
            }
        }

        function transferAllQtyChecked() {
            var $transferAllQty = $('#transfer_all_item').val();

            if (document.getElementById('transfer_all_item').checked) {
                document.getElementById('qty').value = '';
                document.getElementById('qty').disabled = true;
            } else {
                document.getElementById('qty').disabled = false;
            }
        }

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
