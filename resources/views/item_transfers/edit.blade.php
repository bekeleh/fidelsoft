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
                    {!! Former::select('previous_store_id')->addOption('', '')
                    ->label(trans('texts.from_store_name'))->addGroupClass('store-select')
                    ->help(trans('texts.item_store_help') . ' | ' . link_to('/item_stores/', trans('texts.customize_options')))
                     !!}
                    {!! Former::select('current_store_id')->addOption('', '')
                    ->label(trans('texts.to_store_name'))->addGroupClass('store-select')
                     !!}
                    {!! Former::text('qty')->label('texts.qty') !!}

                    {!! Former::label('allQty', trans('texts.allQty')) !!}
                    {{ Form::checkbox('allQty' , 1, false ),['class'=>'square'] }}
                    <br/>
                    {!! Former::label('item_list', trans('texts.item_id')) !!}
                    {!! Form::select('product_id[]', ['1'=>'12'], null, ['class' => 'form-control padding-right', 'multiple' => 'multiple',])
                    !!}
                    @if($errors->has('product_id') )
                        <div class="alert alert-danger" role="alert">
                            One or more of the products you selected are empty/invalid. Please try again.
                        </div>
                    @endif
                    <br/>
                    {!! Former::textarea('notes')->rows(2) !!}
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
//        previous store (from)
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

        // item list
        function itemList(action) {
            var productSelect = $('select#product_id');
            var sourceStoreId = $('select#previous_store_id option:selected').val();
            if (sourceStoreId != '') {
                productSelect.append("<option value='" + 1 + "' selected>" + 'test' + "</option>");
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
