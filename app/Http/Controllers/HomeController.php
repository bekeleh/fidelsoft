<?php

namespace App\Http\Controllers;

use App\Libraries\Utils;
use App\Models\Common\Account;
use App\Ninja\Mailers\Mailer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Redirect;

/**
 * Class HomeController.
 */
class HomeController extends BaseController
{

    protected $mailer;

    /**
     * HomeController constructor.
     *
     * @param Mailer $mailer
     */
    public function __construct(Mailer $mailer)
    {
        //parent::__construct();

        $this->mailer = $mailer;
    }


    public function showIndex()
    {
        Session::reflash();

        if (!Utils::isNinja() && (!Utils::isDatabaseSetup() || Account::count() == 0)) {
            return Redirect::to('/setup');
        } elseif (Auth::check()) {
            return Redirect::to('/');
        } else {
            return Redirect::to('/login');
        }
    }

    public function home()
    {
        // return view('home');
        return view('index');
    }

    public function viewLogo()
    {
        return View::make('public.logo');
    }

    public function invoiceNow()
    {
        // Track the referral/campaign code
        if (Input::has('rc')) {
            session([SESSION_REFERRAL_CODE => Input::get('rc')]);
        }

        if (Auth::check()) {
            $redirectTo = Input::get('redirect_to') ? SITE_URL . '/' . ltrim(Input::get('redirect_to'), '/') : 'invoices/create';
            return Redirect::to($redirectTo)->with('sign_up', Input::get('sign_up'));
        } else {
            return View::make('public.invoice_now');
        }
    }

    public function newsFeed($userType, $version)
    {
        $response = Utils::getNewsFeedResponse($userType);

        return Response::json($response);
    }


    public function hideMessage()
    {
        if (Auth::check() && Session::has('news_feed_id')) {
            $newsFeedId = Session::get('news_feed_id');
            if ($newsFeedId != NEW_VERSION_AVAILABLE && $newsFeedId > Auth::user()->news_feed_id) {
                $user = Auth::user();
                $user->news_feed_id = $newsFeedId;
                $user->save();
            }
        }

        Session::forget('news_feed_message');

        return 'success';
    }

    public function logError()
    {
        return Utils::logError(Input::get('error'), 'JavaScript');
    }


    public function keepAlive()
    {
        return RESULT_SUCCESS;
    }


    public function loggedIn()
    {
        return RESULT_SUCCESS;
    }

    public function contactUs()
    {
        if (Auth::check() && !Auth::user()->email) {
            return RESULT_FAILURE;
        }
        $message = request()->contact_us_message;

        if (request()->include_errors) {
            $message .= "\n\n" . join("\n", Utils::getErrors());
        }

        Mail::raw($message, function ($message) {
            $subject = 'Customer Message [';
            if (Utils::isNinjaProd()) {
                $subject .= str_replace('db-ninja-', '', config('database.default'));
                $subject .= Auth::user()->present()->statusCode . '] ';
            } else {
                $subject .= 'Self-Host] | ';
            }
            $subject .= date('M jS, g:ia');
            $message->to(env('CONTACT_EMAIL', 'fidelinvoice@gmail.com'))
            ->from(CONTACT_EMAIL, Auth::user()->present()->fullName)
            ->replyTo(Auth::user()->email, Auth::user()->present()->fullName)
            ->subject($subject);
        });

        return RESULT_SUCCESS;
    }
}
