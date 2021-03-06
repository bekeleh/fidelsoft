@extends('header')
@section('content')
    @parent
    {!! Former::open()->addClass('warn-on-exit')->rules([
        'first_name' => 'required',
        'last_name' => 'required',
        'username' => 'required',
        'email' => 'required|email',
    ]) !!}

    @if ($user)
        {{ Former::populate($user) }}
        <div style="display:none">
            {!! Former::text('public_id') !!}
        </div>
    @endif
    <span style="display:none">
    {!! Former::text('public_id') !!}
        {!! Former::text('action') !!}
    </span>
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading" style="background-color:#777 !important">
                    <h3 class="panel-title in-bold-white">{!! trans('texts.user_details') !!}</h3>
                </div>
                <div class="panel-body form-padding-right">
                    {!! Former::text('first_name')->readonly() !!}
                    {!! Former::text('last_name')->readonly() !!}
                    {!! Former::text('username')->readonly() !!}
                    {!! Former::text('email')->readonly() !!}
                    {!! Former::text('phone')->readonly() !!}
                    <br/>
                </div>
            </div>
        </div>
    </div>
    @if ( ! Utils::isAdmin())
        @include('accounts.partials.notifications')
    @endif
    <center class="buttons">
        {!! Button::normal(trans('texts.cancel'))->large()->asLinkTo(HTMLUtils::previousUrl('/'))->appendIcon(Icon::create('remove-circle')) !!}
        @if(Utils::isAdmin())
            {!! Button::primary(trans('texts.change_password'))
                    ->appendIcon(Icon::create('lock'))
                    ->large()->withAttributes(['onclick'=>'showChangePassword()']) !!}
            {!! Button::primary(trans('texts.resend_confirmation'))
                    ->appendIcon(Icon::create('send'))
                    ->asLinkTo(URL::to('/resend_confirmation'))->large() !!}
        @endif
        {{--        {!! Button::success(trans('texts.save'))--}}
        {{--                ->submit()->large()--}}
        {{--                ->appendIcon(Icon::create('floppy-disk')) !!}--}}
    </center>
    <div class="modal fade" id="passwordModal" tabindex="-1" role="dialog" aria-labelledby="passwordModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="passwordModalLabel">{{ trans('texts.change_password') }}</h4>
                </div>
                <div class="container" style="width: 100%; padding-bottom: 0px !important">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <div style="background-color: #fff" id="changePasswordDiv"
                                 onkeyup="validateChangePassword()" onclick="validateChangePassword()"
                                 onkeydown="checkForEnter(event)">
                                {!! Former::password('newer_password')->style('width:300px')->label(trans('texts.new_password')) !!}
                                {!! Former::password('confirm_password')->style('width:300px')->help('<span id="passwordStrength">&nbsp;</span>') !!}
                                &nbsp;<br/>
                                <center>
                                    <div id="changePasswordError"></div>
                                </center>
                                <br/>
                            </div>
                            <div style="padding-left:40px;padding-right:40px;display:none;min-height:130px"
                                 id="working">
                                <h3>{{ trans('texts.working') }}...</h3>
                                <div class="progress progress-striped active">
                                    <div class="progress-bar" role="progressbar" aria-valuenow="100" aria-valuemin="0"
                                         aria-valuemax="100" style="width: 100%"></div>
                                </div>
                            </div>
                            <div style="background-color: #fff; padding-right:20px;padding-left:20px; display:none"
                                 id="successDiv">
                                <br/>
                                <h3>{{ trans('texts.success') }}</h3>
                                {{ trans('texts.updated_password') }}
                                <br/>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" id="changePasswordFooter">
                    <button type="button" class="btn btn-default" id="cancelChangePasswordButton" data-dismiss="modal">
                        {{ trans('texts.cancel') }}
                        <i class="glyphicon glyphicon-remove-circle"></i>
                    </button>
                    <button type="button" class="btn btn-success" onclick="submitChangePassword()"
                            id="changePasswordButton" disabled>
                        {{ trans('texts.save') }}
                        <i class="glyphicon glyphicon-floppy-disk"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
    {!! Former::close() !!}
    <script type="text/javascript">
        $(function () {
            $('#passwordModal').on('hidden.bs.modal', function () {
                $(['newer_password', 'confirm_password']).each(function (i, field) {
                    var $input = $('form #' + field);
                    $input.val('');
                    $input.closest('div.form-group').removeClass('has-success');
                });
                $('#changePasswordButton').prop('disabled', true);
            })

            // $('#passwordModal').on('shown.bs.modal', function () {
            //     $('#current_password').focus();
            // })
        });

        function showChangePassword() {
            $('#passwordModal').modal('show');
        }

        function validateChangePassword(showError) {
            var isFormValid = true;
            $(['newer_password', 'confirm_password']).each(function (i, field) {
                var $input = $('form #' + field),
                    val = $.trim($input.val());
                var isValid = val;

                if (field) {
                    isValid = val.length >= 6;
                }

                if (isValid && field == 'confirm_password') {
                    isValid = val == $.trim($('#newer_password').val());
                }

                if (isValid) {
                    $input.closest('div.form-group').removeClass('has-error').addClass('has-success');
                } else {
                    isFormValid = false;
                    $input.closest('div.form-group').removeClass('has-success');
                    if (showError) {
                        $input.closest('div.form-group').addClass('has-error');
                    }
                }

                if (field == 'newer_password') {
                    var score = scorePassword(val);
                    if (isValid) {
                        isValid = score > 50;
                    }

                    showPasswordStrength(val, score);
                }
            });

            $('#changePasswordButton').prop('disabled', !isFormValid);

            return isFormValid;
        }

        function submitChangePassword() {
            if (!validateChangePassword(true)) {
                return;
            }
            $('#changePasswordDiv, #changePasswordFooter').hide();
            $('#working').show();

            $.ajax({
                type: 'POST',
                url: '{{ URL::to('/force_reset_password/force_reset_password') }}',
                data: 'new_password=' + encodeURIComponent($('form #newer_password').val()) +
                    '&confirm_password=' + encodeURIComponent($('form #confirm_password').val()) +
                    '&public_id=' + {{$user->public_id}},
                success: function (result) {
                    if (result == 'success') {
                        NINJA.formIsChanged = false;
                        $('#changePasswordButton').hide();
                        $('#successDiv').show();
                        $('#cancelChangePasswordButton').html('{{ trans('texts.close') }}');
                    } else {
                        $('#changePasswordError').html(result);
                        $('#changePasswordDiv').show();
                    }
                    $('#changePasswordFooter').show();
                    $('#working').hide();
                }
            });
        }

        function disableSocialLogin() {
            sweetConfirm(function () {
                window.location = '{{ URL::to('/auth_unlink') }}';
            });
        }
    </script>
@stop
@section('onReady')
    $('#first_name').focus();
@stop
