<?php echo Former::open('settings/change_plan')->addClass('upgrade-form'); ?>


<span style="display:none">
<?php echo Former::text('plan'); ?>

</span>

<div id="upgrade-modal" class="container" style="">
<div class="row">
<div class="col-md-10 text-right">
  <a href="#"><i class="fa fa-close" onclick="hideUpgradeModal()" title="<?php echo e(trans('texts.close')); ?>"></i></a>
</div>
</div>
<div class="row">
<div class="col-md-12 text-center">
  <h1><?php echo e(trans('texts.upgrade_for_features')); ?></h1>
  <h4 onclick="updateUpgradePrices()">
    <label for="plan_term_month" class="radio-inline">
      <input value="month" id="plan_term_month" type="radio" name="plan_term" checked>Monthly</label>
    <label for="plan_term_year" class="radio-inline">
      <input value="year" id="plan_term_year" type="radio" name="plan_term">Annually</label>
  </h4>
  <?php if(Auth::user()->account->company->hasActivePromo()): ?>
    <h4><?php echo e(Auth::user()->account->company->present()->promoMessage); ?></h4><br/>
  <?php endif; ?>
</div>
<div class="col-md-4 col-md-offset-2 text-center">
  <h2><?php echo e(trans('texts.pro_upgrade_title')); ?></h2>
  <p class="subhead"><?php echo e(trans('texts.pay_annually_discount')); ?></p>
  <img width="65" src="<?php echo e(asset('images/pro_plan/border.png')); ?>"/>
  <h3>$<span id="upgrade_pro_price"><?php echo e(PLAN_PRICE_PRO_MONTHLY); ?></span> <span class="upgrade_frequency">/ <?php echo e(trans('texts.plan_term_month')); ?></span></h3>
  <select style="visibility:hidden">
  </select>
  <p>&nbsp;</p>
  <ul>
    <li><?php echo e(trans('texts.pro_upgrade_feature1')); ?></li>
    <li><?php echo e(trans('texts.pro_upgrade_feature2')); ?></li>
    <li><?php echo e(trans('texts.much_more')); ?></li>
  </ul>
  <?php echo Button::success(trans('texts.go_ninja_pro'))->withAttributes(['onclick' => 'submitUpgradeForm("pro")'])->large(); ?>

</div>
<div class="col-md-4 columns text-center">
  <h2><?php echo e(trans('texts.plan_enterprise')); ?></h2>
  <p class="subhead"><?php echo e(trans('texts.pay_annually_discount')); ?></p>
  <img width="65" src="<?php echo e(asset('images/pro_plan/border.png')); ?>"/>
  <h3>$<span id="upgrade_enterprise_price"><?php echo e(PLAN_PRICE_ENTERPRISE_MONTHLY_2); ?></span> <span class="upgrade_frequency">/ <?php echo e(trans('texts.plan_term_month')); ?></span></h3>
  <select name="num_users" id="upgrade_num_users" onchange="updateUpgradePrices()">
      <option value="2">1 to 2 <?php echo e(trans('texts.users')); ?></option>
      <option value="5">3 to 5 <?php echo e(trans('texts.users')); ?></option>
      <option value="10">6 to 10 <?php echo e(trans('texts.users')); ?></option>
      <option value="20">11 to 20 <?php echo e(trans('texts.users')); ?></option>
  </select>
  <p>&nbsp;</p>
  <ul>
    <li><?php echo e(trans('texts.enterprise_upgrade_feature1')); ?></li>
    <li><?php echo e(trans('texts.enterprise_upgrade_feature2')); ?></li>
    <li><?php echo e(trans('texts.all_pro_fetaures')); ?></li>
  </ul>
  <?php echo Button::success(trans('texts.go_enterprise'))->withAttributes(['onclick' => 'submitUpgradeForm("enterprise")'])->large(); ?>

</div>
</div>
</div>

<?php echo Former::close(); ?>


<script type="text/javascript">

  function showUpgradeModal() {
    <?php if( ! Auth::check() || ! Auth::user()->registered): ?>
        swal(<?php echo json_encode(trans('texts.please_register')); ?>);
        return;
    <?php elseif( ! Auth::check() || ! Auth::user()->confirmed): ?>
        swal(<?php echo json_encode(trans('texts.confirmation_required', ['link' => link_to('/resend_confirmation', trans('texts.click_here'))])); ?>);
        return;
    <?php endif; ?>

    $(window).scrollTop(0);
    $('#upgrade-modal').fadeIn();
  }

  function hideUpgradeModal() {
    $('#upgrade-modal').fadeOut();
  }

  function updateUpgradePrices() {
    var planTerm = $('input[name=plan_term]:checked').val();
    var numUsers = $('#upgrade_num_users').val();
    if (planTerm == 'month') {
      var proPrice = <?php echo e(PLAN_PRICE_PRO_MONTHLY); ?>;
      if (numUsers == 2) {
          var enterprisePrice = <?php echo e(PLAN_PRICE_ENTERPRISE_MONTHLY_2); ?>;
      } else if (numUsers == 5) {
          var enterprisePrice = <?php echo e(PLAN_PRICE_ENTERPRISE_MONTHLY_5); ?>;
      } else if (numUsers == 10) {
          var enterprisePrice = <?php echo e(PLAN_PRICE_ENTERPRISE_MONTHLY_10); ?>;
      } else if (numUsers == 20) {
          var enterprisePrice = <?php echo e(PLAN_PRICE_ENTERPRISE_MONTHLY_20); ?>;
      }
      var label = "<?php echo e(trans('texts.freq_monthly')); ?>";
    } else {
      var proPrice = <?php echo e(PLAN_PRICE_PRO_MONTHLY * 10); ?>;
      if (numUsers == 2) {
          var enterprisePrice = <?php echo e(PLAN_PRICE_ENTERPRISE_MONTHLY_2 * 10); ?>;
      } else if (numUsers == 5) {
          var enterprisePrice = <?php echo e(PLAN_PRICE_ENTERPRISE_MONTHLY_5 * 10); ?>;
      } else if (numUsers == 10) {
          var enterprisePrice = <?php echo e(PLAN_PRICE_ENTERPRISE_MONTHLY_10 * 10); ?>;
      } else if (numUsers == 20) {
          var enterprisePrice = <?php echo e(PLAN_PRICE_ENTERPRISE_MONTHLY_20 * 10); ?>;
      }
      var label = "<?php echo e(trans('texts.freq_annually')); ?>";
    }
    <?php if(Auth::user()->account->company->hasActivePromo()): ?>
        proPrice = proPrice - (proPrice * <?php echo e(Auth::user()->account->company->discount); ?>);
        enterprisePrice = enterprisePrice - (enterprisePrice * <?php echo e(Auth::user()->account->company->discount); ?>);
    <?php endif; ?>
    if (proPrice % 1) {
        proPrice = proPrice.toFixed(2);
    }
    if (enterprisePrice % 1) {
        enterprisePrice = enterprisePrice.toFixed(2);
    }
    $('#upgrade_pro_price').text(proPrice);
    $('#upgrade_enterprise_price').text(enterprisePrice);
    $('span.upgrade_frequency').text(label);
  }

  function submitUpgradeForm(plan) {
    $('#plan').val(plan);
    $('.upgrade-form').submit();
  }

  $(function() {

    <?php if(Auth::user()->account->company->hasActivePromo()): ?>
        updateUpgradePrices();
    <?php endif; ?>

    $(document).keyup(function(e) {
         if (e.keyCode == 27) { // escape key maps to keycode `27`
            hideUpgradeModal();
        }
    });
  })

</script>
