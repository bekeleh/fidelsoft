<?php

namespace App\Listeners\Auth;

use App\Events\Auth\UserSignedUpEvent;
use App\Libraries\Utils;
use App\Ninja\Mailers\UserMailer;
use App\Ninja\Repositories\AccountRepository;
use Illuminate\Support\Facades\Auth;

/**
 * Class UserSignedUpListener.
 */
class UserSignedUpListener
{

    protected $accountRepo;
    protected $userMailer;

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

    /**
     * Handle the event.
     *
     * @param UserSignedUpEvent $event
     *
     * @return void
     */
    public function handle(UserSignedUpEvent $event)
    {
        $user = Auth::user();

        if (Utils::isNinjaProd() && !$user->confirmed) {
            $this->userMailer->sendConfirmation($user);
        } elseif (Utils::isNinjaDev()) {
            // do nothing
        } else {
            $this->accountRepo->registerNinjaUser($user);
        }

        session([SESSION_COUNTER => -1]);
        session([SESSION_DB_SERVER => config('database.default')]);
    }
}
