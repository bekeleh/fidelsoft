<?php

namespace App\Http\Controllers;

use App\Models\Common\AccountToken;
use App\Services\TokenService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Redirect;

/**
 * Class TokenController.
 */
class TokenController extends BaseController
{
    /**
     * @var TokenService
     */
    protected $tokenService;

    /**
     * TokenController constructor.
     *
     * @param TokenService $tokenService
     */
    public function __construct(TokenService $tokenService)
    {
        //parent::__construct();

        $this->tokenService = $tokenService;
    }

    public function index()
    {
        $this->authorize('view', auth::user(), $this->entityType);
        return Redirect::to('settings/' . ACCOUNT_API_TOKENS);
    }

    public function getDatatable()
    {
        $accountId = Auth::user()->id;
        $search = Input::get('sSearch');

        return $this->tokenService->getDatatable($accountId, $search);
    }

    public function create()
    {
        $data = [
            'token' => null,
            'method' => 'POST',
            'url' => 'tokens',
            'title' => trans('texts.add_token'),
        ];

        return View::make('accounts.token', $data);
    }

    public function store()
    {
        return $this->save();
    }

    public function edit($publicId)
    {
        $token = AccountToken::where('account_id', '=', Auth::user()->account_id)
            ->where('public_id', '=', $publicId)->firstOrFail();

        $data = [
            'token' => $token,
            'method' => 'PUT',
            'url' => 'tokens/' . $publicId,
            'title' => trans('texts.edit_token'),
        ];

        return View::make('accounts.token', $data);
    }

    public function update($publicId)
    {
        return $this->save($publicId);
    }

    public function bulk()
    {
        $action = Input::get('bulk_action');
        $ids = Input::get('bulk_public_id');
        $count = $this->tokenService->bulk($ids, $action);

        Session::flash('message', trans('texts.archived_token'));

        return Redirect::to('settings/' . ACCOUNT_API_TOKENS);
    }

    public function save($tokenPublicId = false)
    {
        if (Auth::user()->account->hasFeature(FEATURE_API)) {
            $rules = [
                'name' => 'required',
            ];

            if ($tokenPublicId) {
                $token = AccountToken::where('account_id', '=', Auth::user()->account_id)
                    ->where('public_id', '=', $tokenPublicId)->firstOrFail();
            }

            $validator = Validator::make(Input::all(), $rules);

            if ($validator->fails()) {
                return Redirect::to($tokenPublicId ? 'tokens/edit' : 'tokens/create')->withInput()->withErrors($validator);
            }

            if ($tokenPublicId) {
                $token->name = trim(Input::get('name'));
            } else {
                $token = AccountToken::createNew();
                $token->name = trim(Input::get('name'));
                $token->token = strtolower(str_random(RANDOM_KEY_LENGTH));
            }

            $token->save();

            if ($tokenPublicId) {
                $message = trans('texts.updated_token');
            } else {
                $message = trans('texts.created_token');
            }

            Session::flash('message', $message);
        }

        return Redirect::to('settings/' . ACCOUNT_API_TOKENS);
    }
}
