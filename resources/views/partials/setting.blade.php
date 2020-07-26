<div class="btn-group user-dropdown">
    <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown">
        <div id="myAccountButton" class="ellipsis" style="width:60px;;max-width:80px;">
            {{ trans('texts.utility') }}<span class="caret"></span>
        </div>
    </button>
    <ul class="dropdown-menu user-accounts">
        <li class="divider"></li>
        <li>
            <a href="javascript:showKeyboardShortcuts()" title="{{ trans('texts.help') }}">
                {{ trans('texts.help') }} <i class="fa fa-question-circle"></i>
            </a>
            @if (Auth::check())
                <a href="javascript:showContactUs()" title="{{ trans('texts.contact_us') }}">
                    {{ trans('texts.contact_us') }} <i class="fa fa-envelope"></i>
                </a>
            @endif
            @if (Auth::check() && !Auth::user()->registered)
                {!! Button::success(trans('texts.sign_up'))->withAttributes(array('id' => 'signUpButton', 'onclick' => 'showSignUp()', 'style' => 'max-width:100px;;overflow:hidden'))->small() !!}
            @endif
            @if (Auth::check() && Utils::isNinjaProd() && (!Auth::user()->isPro() || Auth::user()->isTrial()))
                @if (Auth::user()->account->company->hasActivePromo())
                    {!! Button::warning(trans('texts.plan_upgrade'))->withAttributes(array('onclick' => 'showUpgradeModal()', 'style' => 'max-width:100px;overflow:hidden'))->small() !!}
                @else
                    {!! Button::success(trans('texts.plan_upgrade'))->withAttributes(array('onclick' => 'showUpgradeModal()', 'style' => 'max-width:100px;overflow:hidden'))->small() !!}
                @endif
            @endif
        </li>
    </ul>
</div>