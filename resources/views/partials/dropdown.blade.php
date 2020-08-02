<div class="btn-group user-dropdown">
    <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown">
        <div id="myAccountButton" class="ellipsis"
             style="max-width:{{ Utils::hasFeature(FEATURE_USERS) ? '1100' : '100' }}px;">
            @if (session(SESSION_USER_ACCOUNTS) && count(session(SESSION_USER_ACCOUNTS)))
                {{ Auth::user()->account->getDisplayName() }}
            @else
                {{ Auth::user()->getDisplayName() }}
            @endif
            <span class="caret"></span>
        </div>
    </button>
    <ul class="dropdown-menu user-accounts">
        @if (session(SESSION_USER_ACCOUNTS))
            @foreach (session(SESSION_USER_ACCOUNTS) as $item)
                @if ($item->user_id == Auth::user()->id)
                    @include('user_account', [
                    'user_account_id' => $item->id,
                    'user_id' => $item->user_id,
                    'account_name' => $item->account_name,
                    'user_name' => $item->user_name,
                    'logo_url' => isset($item->logo_url) ? $item->logo_url : "",
                    'selected' => true,
                    ])
                @endif
            @endforeach
            {{--            @if (Utils::isSuperUser())--}}
            {{--                @foreach (session(SESSION_USER_ACCOUNTS) as $item)--}}
            {{--                    @if ($item->user_id != Auth::user()->id)--}}
            {{--                        @include('user_account', [--}}
            {{--                        'user_account_id' => $item->id,--}}
            {{--                        'user_id' => $item->user_id,--}}
            {{--                        'account_name' => $item->account_name,--}}
            {{--                        'user_name' => $item->user_name,--}}
            {{--                        'logo_url' => isset($item->logo_url) ? $item->logo_url : "",--}}
            {{--                        'selected' => false,--}}
            {{--                        ])--}}
            {{--                    @endif--}}
            {{--                @endforeach--}}
            {{--            @endif--}}
        @else
            @include('user_account', [
            'account_name' => Auth::user()->account->name ?: trans('texts.untitled'),
            'user_name' => Auth::user()->getDisplayName(),
            'logo_url' => Auth::user()->account->getLogoURL(),
            'selected' => true,
            ])
        @endif
        <li class="divider"></li>
        @if (Utils::isSuperUser() && Auth::user()->confirmed && Utils::getResllerType() != RESELLER_ACCOUNT_COUNT)
            @if (!session(SESSION_USER_ACCOUNTS) || count(session(SESSION_USER_ACCOUNTS)) < 5)
                <li>{!! link_to('#', trans('texts.add_company'), ['onclick' => 'showSignUp()']) !!}</li>
            @endif
        @endif
        <li>
            {!! link_to('#', trans('texts.logout'), array('onclick'=>'logout()')) !!}
        </li>
    </ul>
</div>