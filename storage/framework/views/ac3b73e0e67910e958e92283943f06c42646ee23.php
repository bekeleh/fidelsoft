<?php $__env->startSection('content'); ?>
    ##parent-placeholder-040f06fd774092478d450774f5ba30c5da78acc8##

<?php echo $__env->make('accounts.nav', ['selected' => ACCOUNT_PRODUCTS], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

<?php echo Former::open()->addClass('warn-on-exit'); ?>


<?php echo e(Former::populateField('show_product_notes', intval($account->show_product_notes))); ?>

<?php echo e(Former::populateField('fill_products', intval($account->fill_products))); ?>

<?php echo e(Former::populateField('update_products', intval($account->update_products))); ?>

<?php echo e(Former::populateField('convert_products', intval($account->convert_products))); ?>


    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><?php echo trans('texts.product_settings'); ?></h3>
        </div>
        <div class="panel-body">
            <?php echo Former::checkbox('show_product_notes')->text(trans('texts.show_product_notes_help'))->value(1); ?>

            <?php echo Former::checkbox('fill_products')->text(trans('texts.fill_products_help'))->value(1); ?>

            <?php echo Former::checkbox('update_products')->text(trans('texts.update_products_help'))->value(1); ?>

            &nbsp;
            <?php echo Former::checkbox('convert_products')->text(trans('texts.convert_products_help'))
                ->help(trans('texts.convert_products_tip', [
                    'link' => link_to('/settings/invoice_settings#invoice_fields', trans('texts.custom_field'), ['target' => '_blank']),
                    'name' => trans('texts.exchange_rate')
                ]))->value(1); ?>

            &nbsp;
            <?php echo Former::actions(Button::success(trans('texts.save'))->submit()->appendIcon(Icon::create('floppy-disk'))); ?>

            <?php echo Former::close(); ?>

        </div>
    </div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('header', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>