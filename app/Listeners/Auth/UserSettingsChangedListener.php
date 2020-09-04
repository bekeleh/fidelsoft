<?php

namespace App\Listeners\Auth;

use App\Events\Auth\UserSettingsChangedEvent;
use App\Ninja\Mailers\InvoiceMailer;
use App\Ninja\Repositories\AccountRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

/**
 * Class UserSettingsChangedListener.
 */
class UserSettingsChangedListener
{
    /**
     * Create the event handler.
     *
     * @param AccountRepository $accountRepo
     * @param InvoiceMailer $userMailer
     */
    public function __construct(AccountRepository $accountRepo, InvoiceMailer $userMailer)
    {
        $this->accountRepo = $accountRepo;
        $this->userMailer = $userMailer;
    }

    public function handle(UserSettingsChangedEvent $event)
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
