<!-- Multiple item selection  -->
<div class="form-group <?php use App\Models\Product;

echo e($errors->has($field_name) ? 'has-error' : ''); ?>">
    <label class="col-md-3 control-label" for="<?php echo e($label); ?>"><?php echo e(trans('texts.item_list')); ?></label>
    <div class="col-xs-12">
        <div class="controls">
            <select name="<?php echo e($field_name); ?>[]"
                    id="<?php echo e($field_name); ?>" autofocus="autofocus" minlength="1" multiple="multiple"
                    size="12" required="required" class="form-control">
                <?php if($items = Input::old($field_name, (isset($object->$field_name)) ? $object->$field_name : '')): ?>
                    <?php if(is_array($items)): ?>
                        <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item_id): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($item_id); ?>" selected="selected">
                                <?php echo e((Product::find($item_id)) ? (Product::find($item_id)->name) : ''); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php else: ?>
                        <option value="<?php echo e($items); ?>" selected="selected">
                            <?php echo e((Product::find($items)) ? (Product::find($items)->name) : ''); ?>

                        </option>
                    <?php endif; ?>
                <?php else: ?>
                    <option value=""></option>
                <?php endif; ?>
            </select>
            <?php echo $errors->first($field_name, '<span class="alert-msg"><i class="fa fa-times"></i> :message</span>'); ?>

        </div>
    </div>
</div>
