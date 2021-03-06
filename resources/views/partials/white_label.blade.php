<!-- powered by -->
{{ trans('texts.powered_by') }}
{!! link_to('https://www.fidel.com.et/?tm_source=powered_by', trans('texts.team_source'), ['target' => '_blank', 'title' => trans('texts.created_by', ['name' => trans('texts.team_source')])]) !!}

@if (Auth::user()->account->hasFeature(FEATURE_WHITE_LABEL))
<a href="#" onclick="showWhiteLabelModal()">{{ trans('texts.white_label_link') }}
</a>
@if (!Utils::isNinja() && $company->hasActivePlan() && $company->daysUntilPlanExpires() <= 10 && $company->daysUntilPlanExpires() > 0)
<br/><b>{!! trans('texts.license_expiring', [
    'count' => $company->daysUntilPlanExpires(),
    'link' => '<a href="#" onclick="showWhiteLabelModal()">' . trans('texts.click_here') . '</a>',
]) !!}</b>
@endif
@else
@endif
<div class="modal fade" id="whiteLabelModal" tabindex="-1" role="dialog" aria-labelledby="whiteLabelModalLabel"
aria-hidden="true">
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title" id="myModalLabel">{{ trans('texts.white_label_header') }}</h4>
        </div>

        <div class="container" style="width: 100%; padding-bottom: 0px !important">
            <div class="panel panel-default">
                <div class="panel-body">
                    <p>
                        {{ trans('texts.white_label_text', ['price' => WHITE_LABEL_PRICE])}}
                    </p>
                    <div class="row">
                        <div class="col-md-6">
                            <h4>{{ trans('texts.before') }}</h4>
                            <img src="{{ BLANK_IMAGE }}"
                            data-src="{{ asset('images/pro_plan/white_label_before.png') }}" width="100%"
                            alt="before">
                        </div>
                        <div class="col-md-6">
                            <h4>{{ trans('texts.after') }}</h4>
                            <img src="{{ BLANK_IMAGE }}"
                            data-src="{{ asset('images/pro_plan/white_label_after.png') }}" width="100%"
                            alt="after">
                        </div>
                    </div>
                    <br/>
                    <p>{!! trans('texts.reseller_text', ['email' => HTML::mailto('fidelinvoice@gmail.com')]) !!}</p>
                </div>
            </div>
        </div>

        <div class="modal-footer" id="signUpFooter" style="margin-top: 0px">
            <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('texts.close') }} </button>
            <!-- <button type="button" class="btn btn-primary" onclick="showRecoverLicense()">{{ trans('texts.recover') }} </button> -->
            <button type="button" class="btn btn-primary"
            onclick="showApplyLicense()">{{ trans('texts.apply') }} </button>
            <button type="button" class="btn btn-success"
            onclick="buyWhiteLabel()">{{ trans('texts.Bill') }} </button>
        </div>
    </div>
</div>
</div>

<div class="modal fade" id="applyWhiteLabelModal" tabindex="-1" role="dialog" aria-labelledby="applyWhiteLabelModal"
aria-hidden="true">
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title" id="myModalLabel">{{ trans('texts.apply_white_label_header') }}</h4>
        </div>

        <div class="container" style="width: 100%; padding-bottom: 0px !important">
            <div class="panel panel-default">
                <div class="panel-body">
                    {!! Former::open()->rules(['white_label_license_key' => 'required|min:24|max:24']) !!}
                    {!! Former::input('white_label_license_key') !!}
                    {!! Former::close() !!}
                </div>
            </div>
        </div>

        <div class="modal-footer" id="signUpFooter" style="margin-top: 0px">
            <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('texts.close') }} </button>
            <button type="button" class="btn btn-success"
            onclick="applyLicense()">{{ trans('texts.submit') }} </button>
        </div>
    </div>
</div>
</div>

<div class="modal fade" id="recoverWhiteLabelModal" tabindex="-1" role="dialog" aria-labelledby="recoverWhiteLabelModal"
aria-hidden="true">
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title" id="myModalLabel">{{ trans('texts.recover_white_label_header') }}</h4>
        </div>

        <div class="container" style="width: 100%; padding-bottom: 0px !important">
            <div class="panel panel-default">
                <div class="panel-body">
                    {!! Former::open()->rules(['white_label_license_email' => 'required|email']) !!}
                    {!! Former::input('white_label_license_email')->label('email') !!}
                    {!! Former::close() !!}
                </div>
            </div>
        </div>

        <div class="modal-footer" id="signUpFooter" style="margin-top: 0px">
            <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('texts.close') }} </button>
            <button type="button" class="btn btn-success"
            onclick="applyLicense()">{{ trans('texts.submit') }} </button>
        </div>
    </div>
</div>
</div>

<script type="text/javascript">

    function showWhiteLabelModal() {
        loadImages('#whiteLabelModal');
        $('#whiteLabelModal').modal('show');
    }

    function buyWhiteLabel() {
        buyProduct('{{ WHITE_LABEL_AFFILIATE_KEY }}', '{{ PRODUCT_WHITE_LABEL }}');
    }

    function buyProduct(affiliateKey, productId) {
        location.href = "{{ url('white_label/Bill') }}";
    }

    function showApplyLicense() {
        $('#whiteLabelModal').modal('hide');
        $('#applyWhiteLabelModal').modal('show');
    }

    function showRecoverLicense() {
        $('#whiteLabelModal').modal('hide');
        $('#recoverWhiteLabelModal').modal('show');
    }

    function applyLicense() {
        var license = $('#white_label_license_key').val();
        window.location = "{{ url('') }}/dashboard?license_key=" + license + "&product_id={{ PRODUCT_WHITE_LABEL }}";
    }

</script>
