<?php $__env->startSection('content'); ?>
	##parent-placeholder-040f06fd774092478d450774f5ba30c5da78acc8##
    <?php echo $__env->make('accounts.nav', ['selected' => ACCOUNT_PAYMENTS], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
	<?php echo $__env->make('money_script', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

    <?php echo Former::open()->addClass('warn-on-exit'); ?>

    <?php echo Former::populateField('token_billing_type_id', $account->token_billing_type_id); ?>

    <?php echo Former::populateField('auto_bill_on_due_date', $account->auto_bill_on_due_date); ?>

	<?php echo Former::populateField('gateway_fee_enabled', $account->gateway_fee_enabled); ?>

	<?php echo Former::populateField('send_item_details', $account->send_item_details); ?>


    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><?php echo trans('texts.payment_settings'); ?></h3>
        </div>
        <div class="panel-body">
            <?php echo Former::select('token_billing_type_id')
                        ->options($tokenBillingOptions)
                        ->help(trans('texts.token_billing_help')); ?>


            <?php echo Former::inline_radios('auto_bill_on_due_date')
                        ->label(trans('texts.auto_bill'))
                        ->radios([
                            trans('texts.on_send_date') => ['value'=>0, 'name'=>'auto_bill_on_due_date'],
                            trans('texts.on_due_date') => ['value'=>1, 'name'=>'auto_bill_on_due_date'],
                        ])->help(trans('texts.auto_bill_ach_date_help')); ?>


			<?php echo Former::checkbox('gateway_fee_enabled')
						->help('gateway_fees_help')
						->label('gateway_fees')
						->text('enable')
			 			->value(1); ?>


			<?php echo Former::checkbox('send_item_details')
						->help('send_item_details_help')
						->label('item_details')
						->text('enable')
			 			->value(1); ?>


			<br/>
            <?php echo Former::actions( Button::success(trans('texts.save'))->withAttributes(['id' => 'formSave'])->submit()->appendIcon(Icon::create('floppy-disk')) ); ?>

        </div>
    </div>

    <?php echo Former::close(); ?>


  <?php if($showAdd): ?>
      <?php echo Button::primary(trans('texts.add_gateway'))
            ->asLinkTo(URL::to('/gateways/create'))
            ->withAttributes(['class' => 'pull-right'])
            ->appendIcon(Icon::create('plus-sign')); ?>

  <?php endif; ?>

  <?php echo $__env->make('partials.bulk_form', ['entityType' => ENTITY_ACCOUNT_GATEWAY], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

  <?php echo Datatable::table()
      ->addColumn(
        trans('texts.gateway'),
        trans('texts.limits'),
		trans('texts.fees'),
        trans('texts.action'))
      ->setUrl(url('api/gateways/'))
      ->setOptions('sPaginationType', 'bootstrap')
      ->setOptions('bFilter', false)
      ->setOptions('bAutoWidth', false)
      ->setOptions('aoColumns', [[ "sWidth"=> "20%" ], ["sWidth"=> "20%"], ["sWidth"=> "30%"], ["sWidth"=> "20%"]])
      ->setOptions('aoColumnDefs', [['bSortable'=>false, 'aTargets'=>[1, 2, 3]]])
      ->render('datatable'); ?>


    <?php echo Former::open( 'settings/payment_gateway_limits'); ?>


    <div class="modal fade" id="paymentLimitsModal" tabindex="-1" role="dialog"
         aria-labelledby="paymentLimitsModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" style="min-width:150px">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="paymentLimitsModalLabel"></h4>
                </div>

				<div class="container" style="width: 100%; padding-bottom: 2px !important">
	            <div class="panel panel-default">
	            <div class="panel-body">
					<div role="tabpanel">
		                <ul class="nav nav-tabs" role="tablist" style="border: none">
		                    <li role="presentation" class="active">
		                        <a href="#limits" aria-controls="limits" role="tab" data-toggle="tab"><?php echo e(trans('texts.limits')); ?></a>
		                    </li>
		                    <li role="presentation">
		                        <a href="#fees" aria-controls="fees" role="tab" data-toggle="tab"><?php echo e(trans('texts.fees')); ?></a>
		                    </li>
		                </ul>
		            </div>
		            <div class="tab-content">
		                <div role="tabpanel" class="tab-pane active" id="limits">
		                    <div class="panel-body"><br/>
								<div class="row" style="text-align:center">
			                        <div class="col-xs-12">
			                            <div id="payment-limits-slider"></div>
			                        </div>
			                    </div><br/>
			                    <div class="row">
			                        <div class="col-md-6">
			                            <div id="payment-limit-min-container">
			                                <label for="payment-limit-min"><?php echo e(trans('texts.min')); ?></label><br>
			                                <div class="input-group" style="padding-bottom:8px">
			                                    <span class="input-group-addon"><?php echo e($currency->symbol); ?></span>
			                                    <input type="number" class="form-control" min="0" id="payment-limit-min"
			                                           name="limit_min">
			                                </div>
			                                <label><input type="checkbox" id="payment-limit-min-enable"
			                                              name="limit_min_enable"> <?php echo e(trans('texts.enable_min')); ?></label>
			                            </div>
			                        </div>
			                        <div class="col-md-6">
			                            <div id="payment-limit-max-container">
			                                <label for="payment-limit-max"><?php echo e(trans('texts.max')); ?></label><br>

			                                <div class="input-group" style="padding-bottom:8px">
			                                    <span class="input-group-addon"><?php echo e($currency->symbol); ?></span>
			                                    <input type="number" class="form-control" min="0" id="payment-limit-max"
			                                           name="limit_max">
			                                </div>
			                                <label><input type="checkbox" id="payment-limit-max-enable"
			                                              name="limit_max_enable"> <?php echo e(trans('texts.enable_max')); ?></label>
			                            </div>
			                        </div>
			                    </div>

		                    </div>
		                </div>
						<div role="tabpanel" class="tab-pane" id="fees">
							<div id="feesDisabled" class="panel-body" style="display:none">
								<center style="font-size:16px;color:#888888;">
									<?php echo e(trans('texts.fees_disabled')); ?>

								</center>
							</div>
		                    <div id="feesEnabled" class="panel-body">

								<?php echo Former::text('fee_amount')
										->label('amount')
										->onchange('updateFeeSample()')
										->type('number')
										->step('any'); ?>


								<?php echo Former::text('fee_percent')
										->label('percent')
										->onchange('updateFeeSample()')
										->type('number')
										->step('any')
										->append('%'); ?>


								<?php if($account->invoice_item_taxes): ?>
							        <?php echo Former::select('tax_rate1')
										  ->onchange('onTaxRateChange(1)')
							              ->addOption('', '')
							              ->label(trans('texts.tax_rate'))
							              ->fromQuery($taxRates, function($model) { return $model->name . ': ' . $model->rate . '%'; }, 'public_id'); ?>


									<?php if($account->enable_second_tax_rate): ?>
									<?php echo Former::select('tax_rate2')
										  ->onchange('onTaxRateChange(2)')
							              ->addOption('', '')
							              ->label(trans('texts.tax_rate'))
							              ->fromQuery($taxRates, function($model) { return $model->name . ': ' . $model->rate . '%'; }, 'public_id'); ?>

									<?php endif; ?>

								<?php endif; ?>

								<div style="display:none">
									<?php echo Former::text('fee_tax_name1'); ?>

									<?php echo Former::text('fee_tax_rate1'); ?>

									<?php echo Former::text('fee_tax_name2'); ?>

									<?php echo Former::text('fee_tax_rate2'); ?>

								</div><br/>

								<div class="help-block">
									<span id="feeSample"></span>
									<?php if($account->gateway_fee_enabled && !$account->invoice_item_taxes && $account->invoice_taxes && count($taxRates)): ?>
										<br/><?php echo e(trans('texts.fees_tax_help')); ?>

								    <?php endif; ?>
								</div>

								<br/><b><?php echo e(trans('texts.gateway_fees_disclaimer')); ?></b>

		                    </div>
		                </div>
					</div>

                    <input type="hidden" name="gateway_type_id" id="payment-limit-gateway-type">
                </div>
                </div>
				</div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default"
                            data-dismiss="modal"><?php echo e(trans('texts.cancel')); ?></button>
                    <button type="submit" class="btn btn-primary" id="modalSave"><?php echo e(trans('texts.save')); ?></button>
                </div>
            </div>
        </div>
    </div>
    <?php echo Former::close(); ?>


  <script>
    window.onDatatableReady = actionListHandler;

	var taxRates = <?php echo $taxRates; ?>;
	var taxRatesMap = {};
	for (var i=0; i<taxRates.length; i++) {
		var taxRate = taxRates[i];
		taxRatesMap[taxRate.public_id] = taxRate;
	}
	var gatewaySettings = {};

	<?php $__currentLoopData = $account->account_gateway_settings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $setting): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
		gatewaySettings[<?php echo e($setting->gateway_type_id); ?>] = <?php echo $setting; ?>;
	<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    //function showLimitsModal(gateway_type, gateway_type_id, min_limit, max_limit, fee_amount, fee_percent, fee_tax_name1, fee_tax_rate1, fee_tax_name2, fee_tax_rate2) {
	function showLimitsModal(gateway_type, gateway_type_id) {
		var settings = gatewaySettings[gateway_type_id];
        var modalLabel = <?php echo json_encode(trans('texts.set_limits_fees')); ?>;
        $('#paymentLimitsModalLabel').text(modalLabel.replace(':gateway_type', gateway_type));

		var min_limit = settings ? settings.min_limit : null;
		var max_limit = settings ? settings.max_limit : null

		limitsSlider.noUiSlider.set([min_limit !== null ? min_limit : 0, max_limit !== null ? max_limit : 100000]);

        if (min_limit !== null) {
            $('#payment-limit-min').removeAttr('disabled');
            $('#payment-limit-min-enable').prop('checked', true);
        } else {
            $('#payment-limit-min').attr('disabled', 'disabled');
            $('#payment-limit-min-enable').prop('checked', false);
        }

        if (max_limit !== null) {
            $('#payment-limit-max').removeAttr('disabled');
            $('#payment-limit-max-enable').prop('checked', true);
        } else {
            $('#payment-limit-max').attr('disabled', 'disabled');
            $('#payment-limit-max-enable').prop('checked', false);
        }

        $('#payment-limit-gateway-type').val(gateway_type_id);

		if (settings) {
			$('#fee_amount').val(settings.fee_amount);
			$('#fee_percent').val(settings.fee_percent);
			setTaxRate(1, settings.fee_tax_name1, settings.fee_tax_rate1);
			setTaxRate(2, settings.fee_tax_name2, settings.fee_tax_rate2);
		} else {
			$('#fee_amount').val('');
			$('#fee_percent').val('');
			setTaxRate(1, '', '');
			setTaxRate(2, '', '');
		}

		updateFeeSample();

		if (gateway_type_id == <?php echo e(GATEWAY_TYPE_CUSTOM1); ?> ||
				gateway_type_id == <?php echo e(GATEWAY_TYPE_CUSTOM2); ?> ||
				gateway_type_id == <?php echo e(GATEWAY_TYPE_CUSTOM3); ?> || 
				<?php echo e($account->gateway_fee_enabled ? '0' : '1'); ?>) {
			$('#feesEnabled').hide();
			$('#feesDisabled').show();
		} else {
			$('#feesDisabled').hide();
			$('#feesEnabled').show();
		}

		$('#paymentLimitsModal').modal('show');
    }

    var limitsSlider = document.getElementById('payment-limits-slider');
    noUiSlider.create(limitsSlider, {
        start: [0, 100000],
        connect: true,
        range: {
            'min': [0, 1],
            '30%': [500, 1],
            '70%': [5000, 1],
            'max': [100000, 1]
        }
    });

    limitsSlider.noUiSlider.on('update', function (values, handle) {
        var value = Math.round(values[handle]);
        if (handle == 1) {
            $('#payment-limit-max').val(value).removeAttr('disabled');
            $('#payment-limit-max-enable').prop('checked', true);
        } else {
            $('#payment-limit-min').val(value).removeAttr('disabled');
            $('#payment-limit-min-enable').prop('checked', true);
        }
    });

    $('#payment-limit-min').on('change input', function () {
        setTimeout(function () {
            limitsSlider.noUiSlider.set([$('#payment-limit-min').val(), null]);
        }, 100);
        $('#payment-limit-min-enable').attr('checked', 'checked');
    });

    $('#payment-limit-max').on('change input', function () {
        setTimeout(function () {
            limitsSlider.noUiSlider.set([null, $('#payment-limit-max').val()]);
        }, 100);
        $('#payment-limit-max-enable').attr('checked', 'checked');
    });

    $('#payment-limit-min-enable').change(function () {
        if ($(this).is(':checked')) {
            $('#payment-limit-min').removeAttr('disabled');
        } else {
            $('#payment-limit-min').attr('disabled', 'disabled');
        }
    });

    $('#payment-limit-max-enable').change(function () {
        if ($(this).is(':checked')) {
            $('#payment-limit-max').removeAttr('disabled');
        } else {
            $('#payment-limit-max').attr('disabled', 'disabled');
        }
    });

	function updateFeeSample() {
		var feeAmount = NINJA.parseFloat($('#fee_amount').val()) || 0;
		var feePercent = NINJA.parseFloat($('#fee_percent').val()) || 0;
		var total = feeAmount + (feePercent * 100 / 100);
		var subtotal = total;

		var taxRate1 = $('#tax_rate1').val();
		if (taxRate1) {
			taxRate1 = NINJA.parseFloat(taxRatesMap[taxRate1].rate);
			total += subtotal * taxRate1 / 100;
		}

		var taxRate2 = NINJA.parseFloat($('#tax_rate2').val());
		if (taxRate2) {
			taxRate2 = NINJA.parseFloat(taxRatesMap[taxRate2].rate);
			total += subtotal * taxRate2 / 100;
		}

		if (total >= 0) {
			var str = "<?php echo e(trans('texts.fees_sample')); ?>";
		} else {
			var str = "<?php echo e(trans('texts.discount_sample')); ?>";
		}
		str = str.replace(':amount', formatMoney(100));
		str = str.replace(':total', formatMoney(total));
		$('#feeSample').text(str);
	}

	function onTaxRateChange(instance) {
		var taxRate = $('#tax_rate' + instance).val();
		if (taxRate) {
			taxRate = taxRatesMap[taxRate];
		}

		$('#fee_tax_name' + instance).val(taxRate ? taxRate.name : '');
		$('#fee_tax_rate' + instance).val(taxRate ? taxRate.rate : '');

		updateFeeSample();
	}

	function setTaxRate(instance, name, rate) {
		if (!name || !rate) {
			return;
		}
		var found = false;
		for (var i=0; i<taxRates.length; i++) {
			var taxRate = taxRates[i];
			if (taxRate.name == name && taxRate.rate == rate) {
				$('#tax_rate' + instance).val(taxRate.public_id);
				found = true;
			}
		}
		if (!found) {
			taxRatesMap[0] = {name:name, rate:rate, public_id:0};
			$('#tax_rate' + instance).append(new Option(name + ' ' + rate + '%', 0)).val(0);
		}

		onTaxRateChange(instance);
	}

  </script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('header', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>