<div class="btn-group">
    <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown">
        <div id="myAccountButton" class="ellipsis" style="width:60px;max-width:80px;">
            {{ trans('texts.utility') }}<span class="caret"></span>
        </div>
    </button>
    <ul class="dropdown-menu">
        <li><a href="#"><span class="fa fa-home"></span> Home</a></li>
        <li><a href="#"><span class="fa fa-caret-square-o-up"></span> Activities</a></li>
        <li><a href="#"><span class="fa fa-inbox"></span> Notifications</a></li>
        <li class="divider"></li>
        <li>
            <a href="javascript:showKeyboardShortcuts()" title="{{ trans('texts.help') }}">
                <i class="fa fa-question-circle"> {{ trans('texts.help') }}</i>
            </a>
            @if (Auth::check())
                <a href="javascript:showContactUs()" title="{{ trans('texts.contact_us') }}">
                    <i class="fa fa-envelope"> {{ trans('texts.contact_us') }}</i>
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