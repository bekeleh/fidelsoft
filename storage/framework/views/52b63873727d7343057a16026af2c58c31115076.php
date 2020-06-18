<!-- template -->
<div role="tabpanel" class="tab-pane <?php use App\Models\Frequency;

echo e(isset($active) && $active ? 'active' : ''); ?>" id="<?php echo e($field); ?>">
    <div class="panel-body" style="padding-bottom: 0px">
        <?php if(isset($isReminder) && $isReminder): ?>
            <?php echo Former::populateField('enable_' . $field, intval($account->{'enable_' . $field})); ?>

            <?php if(floatval($fee = $account->account_email_settings->{"late_fee{$number}_amount"})): ?>
                <?php echo Former::populateField('late_fee' . $number . '_amount', $fee); ?>

            <?php endif; ?>
            <?php if(floatval($fee = $account->account_email_settings->{"late_fee{$number}_percent"})): ?>
                <?php echo Former::populateField('late_fee' . $number . '_percent', $fee); ?>

            <?php endif; ?>

            <div class="well" style="padding-bottom:20px">
                <div class="row">
                    <div class="col-md-6">
                        <?php if($field == 'reminder4'): ?>
                            <?php echo Former::populateField('frequency_id_reminder4', $account->account_email_settings->frequency_id_reminder4); ?>

                            <?php echo Former::plaintext('frequency')
                                    ->value(
                                        Former::select('frequency_id_reminder4')
                                            ->options(Frequency::selectOptions())
                                            ->style('float:left;')
                                            ->raw()
                                    ); ?>

                        <?php else: ?>
                            <?php echo Former::plaintext('schedule')
                                    ->value(
                                        Former::input('num_days_' . $field)
                                            ->style('float:left;width:20%')
                                            ->raw() .
                                        Former::select('direction_' . $field)
                                            ->addOption(trans('texts.days_before'), REMINDER_DIRECTION_BEFORE)
                                            ->addOption(trans('texts.days_after'), REMINDER_DIRECTION_AFTER)
                                            ->style('float:left;width:40%')
                                            ->raw() .
                                        '<div id="days_after_'. $field .'" style="float:left;width:40%;display:none;padding-top:8px;padding-left:16px;font-size:16px;">' . trans('texts.days_after') . '</div>' .
                                        Former::select('field_' . $field)
                                            ->addOption(trans('texts.field_due_date'), REMINDER_FIELD_DUE_DATE)
                                            ->addOption(trans('texts.field_invoice_date'), REMINDER_FIELD_INVOICE_DATE)
                                            ->style('float:left;width:40%')
                                            ->raw()
                                    ); ?>

                        <?php endif; ?>
                    </div>
                    <div class="col-md-6">

                        <?php echo Former::checkbox('enable_' . $field)
                                ->text('enable')
                                ->label('send_email')
                                ->value(1); ?>


                    </div>
                </div>
                <?php if($field != 'reminder4'): ?>
                    <div class="row" style="padding-top:30px">
                        <div class="col-md-6">
                            <?php echo Former::text('late_fee' . $number . '_amount')
                                            ->label('late_fee_amount')
                                            ->type('number')
                                            ->step('any'); ?>

                        </div>
                        <div class="col-md-6">
                            <?php echo Former::text('late_fee' . $number . '_percent')
                                            ->label('late_fee_percent')
                                            ->type('number')
                                            ->step('any')
                                            ->append('%'); ?>

                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <br/>
        <?php endif; ?>
        <div class="row">
            <div class="col-md-6">
                <div class="pull-right"><a href="#"
                                           onclick="return resetText('<?php echo e('subject'); ?>', '<?php echo e($field); ?>')"><?php echo e(trans("texts.reset")); ?></a>
                </div>
                <?php echo Former::text('email_subject_' . $field)
                        ->label(trans('texts.subject'))
                        ->appendIcon('question-sign')
                        ->addGroupClass('email-subject'); ?>

            </div>
            <div class="col-md-6">
                <p>&nbsp;
                <p/>
                <div id="<?php echo e($field); ?>_subject_preview"></div>
            </div>
        </div>
        <div class="row">
            <br/>
            <div class="col-md-6">
                <div class="pull-right"><a href="#"
                                           onclick="return resetText('<?php echo e('template'); ?>', '<?php echo e($field); ?>')"><?php echo e(trans("texts.reset")); ?></a>
                </div>
                <?php echo Former::textarea('email_template_' . $field)
                        ->label(trans('texts.body'))
                        ->style('display:none'); ?>

                <div id="<?php echo e($field); ?>Editor" class="form-control" style="min-height:160px">
                </div>
            </div>
            <div class="col-md-6">
                <p>&nbsp;
                <p/>
                <div id="<?php echo e($field); ?>_template_preview"></div>
            </div>
        </div>
        <p>&nbsp;
        <p/>
        <div class="row">
            <div class="pull-left show-when-ready" style="display:none">
                <?php echo $__env->make('partials/quill_toolbar', ['name' => $field], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            </div>
            <div class="pull-right" style="padding-top:13px;text-align:right">
                <?php echo Button::normal(trans('texts.raw'))->withAttributes(['onclick' => 'showRaw("'.$field.'")'])->small(); ?>

                <?php echo Button::primary(trans('texts.preview'))->withAttributes(['onclick' => 'serverPreview("'.$field.'")'])->small(); ?>

            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(function () {
        var editor = new Quill('#<?php echo e($field); ?>Editor', {
            modules: {
                'toolbar': {container: '#<?php echo e($field); ?>Toolbar'},
                'link-tooltip': true
            },
            theme: 'snow'
        });
        editor.setHTML($('#email_template_<?php echo e($field); ?>').val());
        editor.on('text-change', function (delta, source) {
            if (source == 'api') {
                return;
            }
            var html = editors['<?php echo e($field); ?>'].getHTML();
            $('#email_template_<?php echo e($field); ?>').val(html);
            refreshPreview();
            NINJA.formIsChanged = true;
        });
        editors['<?php echo e($field); ?>'] = editor;

        $('#field_<?php echo e($field); ?>').change(function () {
            setDirectionShown('<?php echo e($field); ?>');
        });
        setDirectionShown('<?php echo e($field); ?>');

        $('.email-subject .input-group-addon').click(function () {
            $('#templateHelpModal').modal('show');
        });
    });

</script>
