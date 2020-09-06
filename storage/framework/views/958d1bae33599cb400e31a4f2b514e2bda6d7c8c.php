<?php $__env->startSection('head'); ?>
	##parent-placeholder-1a954628a960aaef81d7b2d4521929579f3541e6##

	<script src="<?php echo e(asset('js/Chart.min.js')); ?>" type="text/javascript"></script>
	<script src="<?php echo e(asset('js/daterangepicker.min.js')); ?>?no_cache=<?php echo e(NINJA_VERSION); ?>" type="text/javascript"></script>
	<link href="<?php echo e(asset('css/daterangepicker.css')); ?>?no_cache=<?php echo e(NINJA_VERSION); ?>" rel="stylesheet" type="text/css"/>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('top-right'); ?>
	<div class="pull-right">
		<div id="reportrange" class="pull-right" style="background: #fff; cursor: pointer; padding: 9px 14px; border: 1px solid #ccc; margin-top: 0px; margin-left:18px">
			<i class="glyphicon glyphicon-calendar fa fa-calendar"></i>&nbsp;
			<span></span> <b class="caret"></b>
		</div>
	</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>

	<?php if(!Utils::isPro()): ?>
		<div class="alert alert-warning" style="font-size:larger;">
			<center>
				<?php echo trans('texts.pro_plan_reports', ['link'=>'<a href="javascript:showUpgradeModal()">' . trans('texts.pro_plan_remove_logo_link') . '</a>']); ?>

			</center>
		</div>
	<?php endif; ?>


	<script type="text/javascript">

	<?php if(Auth::user()->hasPermission('view_reports')): ?>
	function loadChart(data) {
		var ctx = document.getElementById('chart-canvas').getContext('2d');
		if (window.myChart) {
			window.myChart.config.data = data;
			window.myChart.config.options.scales.xAxes[0].time.unit = 'day';
			window.myChart.config.options.scales.xAxes[0].time.round = 'day';
			window.myChart.update();
		} else {
			$('#progress-div').hide();
			$('#chart-canvas').fadeIn();
			window.myChart = new Chart(ctx, {
				type: 'line',
				data: data,
				options: {
					tooltips: {
						mode: 'x-axis',
						titleFontSize: 15,
						titleMarginBottom: 12,
						bodyFontSize: 15,
						bodySpacing: 10,
						callbacks: {
							title: function(item) {
								return moment(item[0].xLabel).format("<?php echo e($account->getMomentDateFormat()); ?>");
							},
							label: function(item, data) {
								if (item.datasetIndex == 0) {
									var label = " <?php echo trans('texts.sent'); ?>: ";
								} else if (item.datasetIndex == 1) {
									var label = " <?php echo trans('texts.opened'); ?>: ";
								}

								return label + ' ' + item.yLabel;
							}
						}
					},
					scales: {
						xAxes: [{
							type: 'time',
							time: {
								unit: 'day',
								round: 'day',
							},
							gridLines: {
								display: false,
							},
						}],
						yAxes: [{
							ticks: {
								beginAtZero: true,
								callback: function(label, index, labels) {
									return roundSignificant(label);
								}
							},
						}]
					}
				}
			});
		}
	}

	var account = <?php echo $account; ?>;
	var dateRanges = <?php echo $account->present()->dateRangeOptions; ?>;
	var chartStartDate;
	var chartEndDate;

	$(function() {

		// Initialize date range selector
		chartStartDate = moment().subtract(29, 'days');
		chartEndDate = moment();
		lastRange = false;

		if (isStorageSupported()) {
			lastRange = localStorage.getItem('last:dashboard_range');
			dateRange = dateRanges[lastRange];

			if (dateRange) {
				chartStartDate = dateRange[0];
				chartEndDate = dateRange[1];
			}
		}

		function cb(start, end, label) {
			$('#reportrange span').html(start.format('<?php echo e($account->getMomentDateFormat()); ?>') + ' - ' + end.format('<?php echo e($account->getMomentDateFormat()); ?>'));
			chartStartDate = start;
			chartEndDate = end;
			$('.range-label-div').show();
			if (label) {
				$('.range-label-div').text(label);
			}
			loadData();

			if (isStorageSupported() && label && label != "<?php echo e(trans('texts.custom_range')); ?>") {
				localStorage.setItem('last:dashboard_range', label);
			}
		}

		$('#reportrange').daterangepicker({
			locale: {
				format: "<?php echo e($account->getMomentDateFormat()); ?>",
				customRangeLabel: "<?php echo e(trans('texts.custom_range')); ?>",
				applyLabel: "<?php echo e(trans('texts.apply')); ?>",
				cancelLabel: "<?php echo e(trans('texts.cancel')); ?>",
			},
			startDate: chartStartDate,
			endDate: chartEndDate,
			linkedCalendars: false,
			ranges: dateRanges,
		}, cb);

		cb(chartStartDate, chartEndDate, lastRange);

		$("#currency-btn-group > .btn").click(function(){
			$(this).addClass("active").siblings().removeClass("active");
			loadData();
			if (isStorageSupported()) {
				localStorage.setItem('last:dashboard_currency_id', $(this).attr('data-button'));
			}
		});

		$("#group-btn-group > .btn").click(function(){
			$(this).addClass("active").siblings().removeClass("active");
			loadData();
		});

		function loadData() {
			var url = '<?php echo e(url('/reports/emails_report')); ?>/' + chartStartDate.format('YYYY-MM-DD') + '/' + chartEndDate.format('YYYY-MM-DD');
			$.get(url, function(response) {
				loadChart(response.data);
				$('#totalSentDiv').html(response.totals['sent']);
				$('#totalOpenedDiv').html(response.totals['opened']);
				$('#totalBouncedDiv').html(response.totals['bounced']);
				//$('#totalSpamDiv').html(response.totals['spam']);

				$('#platformsTable').html(response.platforms);
				$('#emailClientsTable').html(response.emailClients);
			})
		}

	});
	<?php endif; ?>

	</script>


	<div class="row">
	    <div class="col-md-4">
	        <div class="panel panel-default">
	            <div class="panel-body">
	                <div style="overflow:hidden">
	                    <div class="in-thin">
	                        <?php echo e(trans('texts.total_sent')); ?>

	                    </div>
	                    <div class="in-bold" id="totalSentDiv">
							&nbsp;
						</div>
	                </div>
	            </div>
	        </div>
	    </div>
	    <div class="col-md-4">
	        <div class="panel panel-default">
				<div class="panel-body">
	                <div style="overflow:hidden">
	                    <div class="in-thin">
	                        <?php echo e(trans('texts.total_opened')); ?>

	                    </div>
	                    <div class="in-bold" id="totalOpenedDiv">
							&nbsp;
						</div>
	                </div>
	            </div>
	        </div>
	    </div>
		<div class="col-md-4">
	        <div class="panel panel-default">
	            <div class="panel-body">
	                <div style="overflow:hidden">
	                    <div class="in-thin">
	                        <?php echo e(trans('texts.total_bounced')); ?>

	                    </div>
	                    <div class="in-bold" id="totalBouncedDiv">
							&nbsp;
						</div>
	                </div>
	            </div>
	        </div>
	    </div>
		<!--
		<div class="col-md-3">
	        <div class="panel panel-default">
	            <div class="panel-body outstanding-panel">
	                <div style="overflow:hidden">
	                    <div class="in-thin">
	                        <?php echo e(trans('texts.total_spam')); ?>

	                    </div>
	                    <div class="in-bold" id="totalSpamDiv">
							&nbsp;
						</div>
	                </div>
	            </div>
	        </div>
	    </div>
		-->
	</div>

	<div class="row">
	    <div class="col-md-12">
			<?php if(Auth::user()->hasPermission('view_reports')): ?>
	        <div id="progress-div" class="progress">
	            <div class="progress-bar progress-bar-striped active" role="progressbar"
	                aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%"></div>
	        </div>
			<?php endif; ?>
	        <canvas id="chart-canvas" height="70px" style="background-color:white;padding:20px;display:none"></canvas>
	    </div>
	</div>

	<p>&nbsp;</p>

	<div class="row">
	    <div class="col-md-6">
	        <div class="panel panel-default">
				<div class="panel-heading" style="background-color:#777 !important">
	                <h3 class="panel-title in-bold-white">
	                    <i class="glyphicon glyphicon-phone"></i> <?php echo e(trans('texts.platforms')); ?>

	                </h3>
	            </div>
				<div class="panel-body" style="height:280px;overflow-y:auto;">
	                <table class="table table-striped" id="platformsTable">
					</table>
				</div>
	        </div>
	    </div>
	    <div class="col-md-6">
	        <div class="panel panel-default">
				<div class="panel-heading" style="margin:0; background-color: #f5f5f5 !important;">
	                <h3 class="panel-title" style="color: black !important">
	                    <i class="glyphicon glyphicon-inbox"></i> <?php echo e(trans('texts.email_clients')); ?>

	                </h3>
	            </div>
				<div class="panel-body" style="height:280px;overflow-y:auto;">
	                <table class="table table-striped" id="emailClientsTable">
					</table>
				</div>
	        </div>
	    </div>
	</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('header', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>