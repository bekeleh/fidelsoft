<?php $__env->startSection('content'); ?>
    ##parent-placeholder-040f06fd774092478d450774f5ba30c5da78acc8##
    <?php echo Former::open($url)
    ->method($method)
    ->autocomplete('off')
    ->rules(['product_id' => 'required' ,'previous_store_id' => 'required' ,'current_store_id' => 'required','notes' => 'required' ])
    ->addClass('col-lg-10 col-lg-offset-1 main-form warn-on-exit'); ?>

    <?php if($itemTransfer): ?>
        <?php echo e(Former::populate($itemTransfer)); ?>

        <div style="display:none">
            <?php echo Former::text('public_id'); ?>

        </div>
    <?php endif; ?>
    <span style="display:none">
<?php echo Former::text('public_id'); ?>

        <?php echo Former::text('action'); ?>

</span>
    <div class="row">
        <div class="col-lg-10 col-lg-offset-1">
            <div class="panel panel-default">
                <div class="panel-body form-padding-right">
                    <!-- from store -->
                <?php echo Former::select('previous_store_id')->addOption('', '')
                ->onchange('selectProductAction()')
                ->label(trans('texts.from_store_name'))->addGroupClass('store-select')
                ->help(trans('texts.item_store_help') . ' | ' . link_to('/item_stores/', trans('texts.customize_options'))); ?>

                <!-- to store -->
                <?php echo Former::select('current_store_id')->addOption('', '')
                ->label(trans('texts.to_store_name'))->addGroupClass('store-select'); ?>

                <?php echo $__env->make('partials.select_product', ['label'=>'product_id','field_name'=>'product_id','check_item_name'=>'transfer_all_item_checked','required'=>'true' ], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                <!-- qty -->
                <?php echo Former::text('qty')->label('texts.qty'); ?>

                <!-- NOTES -->
                    <?php echo Former::textarea('notes')->rows(2); ?>

                </div>
            </div>
        </div>
    </div>

    <?php if(Auth::user()->canCreateOrEdit(ENTITY_ITEM_STORE, $itemTransfer)): ?>
        <center class="buttons">
            <?php echo Button::normal(trans('texts.cancel'))->large()->asLinkTo(HTMLUtils::previousUrl('/item_transfers'))->appendIcon(Icon::create('remove-circle')); ?>

            <?php echo Button::success(trans('texts.save'))->submit()->large()->appendIcon(Icon::create('floppy-disk')); ?>

            <?php if($itemTransfer): ?>
                <?php echo DropdownButton::normal(trans('texts.more_actions'))
                ->withContents($itemTransfer->present()->moreActions())
                ->large()
                ->dropup(); ?>

            <?php endif; ?>
        </center>
    <?php endif; ?>

    <?php echo Former::close(); ?>


    <script type="text/javascript">
        var products = <?php echo $products; ?>;
        var previousStores = <?php echo $previousStores; ?>;
        var currentStores = <?php echo $currentStores; ?>;

        var productMap = {};
        var previousMap = {};
        var currentMap = {};
        $(function () {
            $('#qty').focus();
        });
        $(function () {
//        previous store (from)
            var storeFromId = <?php echo e($previousStorePublicId ?: 0); ?>;
            var $storeSelect = $('select#previous_store_id');
            <?php if(Auth::user()->can('create', ENTITY_STORE)): ?>
            $storeSelect.append(new Option("<?php echo e(trans('texts.create_store')); ?>: $name", '-1'));
                    <?php endif; ?>
            for (var i = 0; i < previousStores.length; i++) {
                var storeFrom = previousStores[i];
                previousMap[storeFrom.public_id] = storeFrom;
                $storeSelect.append(new Option(getClientDisplayName(storeFrom), storeFrom.public_id));
            }
            <?php echo $__env->make('partials/entity_combobox', ['entityType' => ENTITY_STORE], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            if (storeFromId) {
                var storeFrom = previousMap[storeFromId];
                setComboboxValue($('.store-select'), storeFrom.public_id, storeFrom.name);
            }

//        current store (to)
            var storeToId = <?php echo e($currentStorePublicId ?: 0); ?>;
            var $store_toSelect = $('select#current_store_id');
            <?php if(Auth::user()->can('create', ENTITY_STORE)): ?>
            $store_toSelect.append(new Option("<?php echo e(trans('texts.create_store_to')); ?>: $name", '-1'));
                    <?php endif; ?>
            for (var i = 0; i < currentStores.length; i++) {
                var storeTo = currentStores[i];
                currentMap[storeTo.public_id] = storeTo;
                $store_toSelect.append(new Option(getClientDisplayName(storeTo), storeTo.public_id));
            }
            <?php echo $__env->make('partials/entity_combobox', ['entityType' => ENTITY_STORE_TO], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            if (storeToId) {
                var storeTo = currentMap[storeToId];
                setComboboxValue($('.store-to-select'), storeTo.public_id, storeTo.name);
            }
        });

        function selectProductAction() {
            var $productModel = $('#product_id');
            var $sourceStoreId = $('select#previous_store_id').val();
            if ($sourceStoreId != '') {
                $productModel.empty();
                getItems($productModel, $sourceStoreId);
            }
        }

        // find items in the selected store.
        function getItems($productModel, $sourceStoreId, $item_checked = null) {
            if ($sourceStoreId != null) {
                $.ajax({
                    type: 'GET',
                    url: '<?php echo e(URL::to('item_stores')); ?>',
                    data: 'store_id=' + $sourceStoreId,
                    headers: {
                        "X-Requested-With": 'XMLHttpRequest',
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        console.log(response.data);
                    },
                });
            }
        }

        function transferAllQtyAction() {

            alert('checked.');
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
<?php $__env->stopSection(); ?>

<?php echo $__env->make('header', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>