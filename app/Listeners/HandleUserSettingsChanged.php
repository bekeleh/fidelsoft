<?php

namespace App\Listeners;

use App\Events\UserSettingsChanged;
use App\Ninja\Mailers\UserMailer;
use App\Ninja\Repositories\AccountRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

/**
 * Class HandleUserSettingsChanged.
 */
class HandleUserSettingsChanged
{
    /**
     * Create the event handler.
     *
     * @param AccountRepository $accountRepo
     * @param UserMailer $userMailer
     */
    public function __construct(AccountRepository $accountRepo, UserMailer $userMailer)
    {
        $this->accountRepo = $accountRepo;
        $this->userMailer = $userMailer;
    }

    public function handle(UserSettingsChanged $event)
    {
        if (!Auth::check()) {
            return;
        }

        $account = Auth::user()->account;
        $account->loadLocalizationSettings();

        $users = $this->accountRepo->loadAccounts(Auth::user()->id);
        Session::put(SESSION_USER_ACCOUNTS, $users);

        if ($event->user && $event->user->confirmed && $event->user->isEmailBeingChanged()) {
            $this->userMailer->sendConfirmation($event->user);
            $this->userMailer->sendEmailChanged($event->user);

            Session::flash('warning', trans('texts.verify_email'));
        }
    }
}
