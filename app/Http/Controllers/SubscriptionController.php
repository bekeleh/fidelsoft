<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubscriptionRequest;
use App\Http\Requests\CreateSubscriptionRequest;
use App\Http\Requests\UpdateSubscriptionRequest;
use App\Models\Subscription;
use App\Services\SubscriptionService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Redirect;

/**
 * Class SubscriptionController.
 */
class SubscriptionController extends BaseController
{
    /**
     * @var SubscriptionService
     */
    protected $subscriptionService;

    /**
     * SubscriptionController constructor.
     *
     * @param SubscriptionService $subscriptionService
     */
    public function __construct(SubscriptionService $subscriptionService)
    {
        //parent::__construct();

        $this->subscriptionService = $subscriptionService;
    }

    public function index()
    {
        $this->authorize('view', ENTITY_SUBSCRIPTION);
        return Redirect::to('settings/' . ACCOUNT_API_TOKENS);
    }

    public function getDatatable()
    {
        $accountId = Auth::user()->account_id;
        $search = Input::get('sSearch');

        return $this->subscriptionService->getDatatable($accountId, $search);
    }

    public function create(SubscriptionRequest $request)
    {
        $this->authorize('create', ENTITY_SUBSCRIPTION);
        $data = [
            'subscription' => null,
            'method' => 'POST',
            'url' => 'subscriptions',
            'title' => trans('texts.add_subscription'),
        ];

        return View::make('accounts.subscriptions', $data);
    }

    public function store(CreateSubscriptionRequest $request)
    {
        return $this->save();
    }

    public function edit(SubscriptionRequest $request, $publicId)
    {
        $this->authorize('edit', ENTITY_SUBSCRIPTION);
        $subscription = Subscription::scope($publicId)->firstOrFail();

        $data = [
            'subscription' => $subscription,
            'method' => 'PUT',
            'url' => 'subscriptions/' . $publicId,
            'title' => trans('texts.edit_subscription'),
        ];

        return View::make('accounts.subscriptions', $data);
    }

    public function update(UpdateSubscriptionRequest $request, $publicId)
    {
        return $this->save($publicId);
    }

    public function bulk()
    {
        $action = Input::get('bulk_action');
        $ids = Input::get('bulk_public_id');

        $count = $this->subscriptionService->bulk($ids, $action);

        Session::flash('message', trans('texts.archived_subscription'));

        return Redirect::to('settings/' . ACCOUNT_API_TOKENS);
    }

    public function save($subscriptionPublicId = false)
    {
        if (Auth::user()->account->hasFeature(FEATURE_API)) {
            $rules = [
                'event_id' => 'required',
                'target_url' => 'required|url',
            ];

            if ($subscriptionPublicId) {
                $subscription = Subscription::scope($subscriptionPublicId)->firstOrFail();
            } else {
                $subscription = Subscription::createNew();
                $subscriptionPublicId = $subscription->public_id;
            }

            $validator = Validator::make(Input::all(), $rules);

            if ($validator->fails()) {
                return Redirect::to($subscriptionPublicId ? 'subscriptions/edit' : 'subscriptions/create')->withInput()->withErrors($validator);
            }

            $subscription->fill(request()->all());
            $subscription->save();

            if ($subscriptionPublicId) {
                $message = trans('texts.updated_subscription');
            } else {
                $message = trans('texts.created_subscription');
            }

            Session::flash('message', $message);
        }

        return redirect('/settings/api_tokens');

        /*
        if ($subscriptionPublicId) {
            return Redirect::to('subscriptions/' . $subscriptionPublicId . '/edit');
        } else {
            return redirect('/settings/api_tokens');
        }
        */
    }
}
