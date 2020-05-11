@extends('login')
@section('form')
    @include('partials.warn_session', ['redirectTo' => '/logout?reason=inactive'])
    <div class="container">
        {!! Former::open('login')->addClass('form-signin')->rules([
            'username' => 'required',
            'password' => 'required'
            ]) !!}
        <h2 class="form-signin-heading">
            @if (strstr(session('url.intended'), 'time_tracker'))
                {{ trans('texts.time_tracker_login') }}
            @else
                {{ trans('texts.account_login') }}
            @endif
        </h2>
        <hr class="green">
        @if (count($errors->all()))
            <div class="alert alert-danger">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </div>
        @endif
        @if (Session::has('success'))
            <div class="alert alert-warning">{!! Session::get('success') !!}</div>
        @endif
        @if (Session::has('warning'))
            <div class="alert alert-warning">{!! Session::get('warning') !!}</div>
        @endif
        @if (Session::has('message'))
            <div class="alert alert-info">{!! Session::get('message') !!}</div>
        @endif

        @if (Session::has('error'))
            <div class="alert alert-danger">
                <li>{!! Session::get('error') !!}</li>
            </div>
        @endif

        @if (env('REMEMBER_ME_ENABLED'))
            {{ Former::populateField('remember', 'true') }}
            {!! Former::hidden('remember')->raw() !!}
        @endif

        <div>
            {!! Former::text('username')->placeholder(trans('texts.username'))->raw() !!}
            {!! Former::password('password')->placeholder(trans('texts.password'))->raw() !!}
        </div>

        {!! Button::success(trans('texts.login'))
                    ->withAttributes(['id' => 'loginButton', 'class' => 'green'])
                    ->large()->submit()->block() !!}
    <!-- social network key -->
        @if (Utils::isOAuthEnabled())
            <div class="row existing-accounts">
                <p>{{ trans('texts.login_or_existing') }}</p>
                @foreach (App\Services\AuthService::$providers as $provider)
                    <div class="col-md-3 col-xs-6">
                        <a href="{{ URL::to('auth/' . $provider) }}" class="btn btn-primary btn-lg"
                           title="{{ $provider }}"
                           id="{{ strtolower($provider) }}LoginButton">
                            @if($provider == SOCIAL_GITHUB_123)
                                <i class="fa fa-github-alt"></i>
                            @else
                                <i class="fa fa-{{ strtolower($provider) }}"></i>
                            @endif
                        </a>
                    </div>
                @endforeach
            </div>
        @endif
        <div class="row meta">
            @if (Utils::isWhiteLabel())
                <center>
                    <br/>{!! link_to('/recover_password', trans('texts.recover_password')) !!}
                </center>
            @else
                <div class="col-md-7 col-sm-12">
                    {!! link_to('/recover_password', trans('texts.recover_password')) !!}
                </div>
                <div class="col-md-5 col-sm-12">
                    @if (Utils::isTimeTracker())
                        {!! link_to('#', trans('texts.self_host_login'), ['onclick' => 'setSelfHostUrl()']) !!}
                    @else
                        {!! link_to(NINJA_WEB_URL.'/', trans('texts.knowledge_base'), ['target' => '_blank']) !!}
                    @endif
                </div>
            @endif
        </div>
        {!! Former::close() !!}

        @if (Utils::allowNewAccounts() && ! strstr(session('url.intended'), 'time_tracker'))
            <div class="row sign-up">
                <div class="col-md-3 col-md-offset-3 col-xs-12">
                    <h3>{{trans('texts.not_a_member_yet')}}</h3>
                    <p>{{trans('texts.login_create_an_account')}}</p>
                </div>
                <div class="col-md-3 col-xs-12">
                    {!! Button::primary(trans('texts.sign_up_now'))->asLinkTo(URL::to('/invoice_now?sign_up=true'))
                        ->withAttributes(['class' => 'blue'])
                        ->large()->submit()->block() !!}
                </div>
            </div>
        @endif
    </div>
    <script type="text/javascript">
        $(function () {
            if ($('#username').val()) {
                $('#password').focus();
            } else {
                $('#username').focus();
            }

            @if (Utils::isTimeTracker())
            if (isStorageSupported()) {
                var selfHostUrl = localStorage.getItem('last:time_tracker:url');
                if (selfHostUrl) {
                    location.href = selfHostUrl;
                    return;
                }
                $('#username').change(function () {
                    localStorage.setItem('last:time_tracker:username', $('#username').val());
                })
                var username = localStorage.getItem('last:time_tracker:username');
                if (username) {
                    $('#username').val(username);
                    $('#password').focus();
                }
            }
            @endif
        })

        @if (Utils::isTimeTracker())
        function setSelfHostUrl() {
            if (!isStorageSupported()) {
                swal("{{ trans('texts.local_storage_required') }}");
                return;
            }
            swal({
                title: "{{ trans('texts.set_self_hoat_url') }}",
                input: 'text',
                showCancelButton: true,
                confirmButtonText: 'Save',
            }).then(function (value) {
                if (!value || value.indexOf('http') !== 0) {
                    swal("{{ trans('texts.invalid_url') }}")
                    return;
                }
                value = value.replace(/\/+$/, '') + '/time_tracker';
                localStorage.setItem('last:time_tracker:url', value);
                location.reload();
            }).catch(swal.noop);
        }
        @endif

    </script>

@endsection
