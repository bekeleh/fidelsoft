<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateBankAccountRequest;
use App\Libraries\Utils;
use App\Models\BankAccount;
use App\Ninja\Repositories\BankAccountRepository;
use App\Services\BankAccountService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Redirect;

class BankAccountController extends BaseController
{
    protected $bankAccountService;
    protected $bankAccountRepo;

    public function __construct(BankAccountService $bankAccountService, BankAccountRepository $bankAccountRepo)
    {
        //parent::__construct();

        $this->bankAccountService = $bankAccountService;
        $this->bankAccountRepo = $bankAccountRepo;
    }

    public function index()
    {
        $this->authorize('view', ENTITY_BANK_ACCOUNT);
        return Redirect::to('settings/' . ACCOUNT_BANKS);
    }

    public function getDatatable()
    {
        $accountId = Auth::user()->account_id;
        $search = Input::get('sSearch');

        return $this->bankAccountService->getDatatable($accountId, $search);
    }

    public function edit($publicId)
    {
        $this->authorize('edit', ENTITY_BANK_ACCOUNT);
        $bankAccount = BankAccount::scope($publicId)->firstOrFail();

        $data = [
            'title' => trans('texts.edit_bank_account'),
            'banks' => Cache::get('banks'),
            'bankAccount' => $bankAccount,
        ];

        return View::make('accounts.bank_account', $data);
    }

    public function update($publicId)
    {
        return $this->save($publicId);
    }

    /**
     * Displays the form for account creation.
     */
    public function create()
    {
        $this->authorize('create', ENTITY_BANK_ACCOUNT);
        $data = [
            'banks' => Cache::get('banks'),
            'bankAccount' => null,
        ];

        return View::make('accounts.bank_account', $data);
    }

    public function bulk()
    {
        $action = Input::get('bulk_action');
        $ids = Input::get('bulk_public_id');
        $count = $this->bankAccountService->bulk($ids, $action);

        Session::flash('message', trans('texts.archived_bank_account'));

        return Redirect::to('settings/' . ACCOUNT_BANKS);
    }

    public function validateAccount()
    {
        $publicId = Input::get('public_id');
        $username = trim(Input::get('bank_username'));
        $password = trim(Input::get('bank_password'));

        if ($publicId) {
            $bankAccount = BankAccount::scope($publicId)->firstOrFail();
            if ($username != $bankAccount->username) {
                $bankAccount->setUsername($username);
                $bankAccount->save();
            } else {
                $username = Crypt::decrypt($username);
            }
            $bankId = $bankAccount->bank_id;
        } else {
            $bankAccount = new BankAccount;
            $bankAccount->bank_id = Input::get('bank_id');
        }

        $bankAccount->app_version = Input::get('app_version');
        $bankAccount->ofx_version = Input::get('ofx_version');

        if ($publicId) {
            $bankAccount->save();
        }

        return json_encode($this->bankAccountService->loadBankAccounts($bankAccount, $username, $password, $publicId));
    }

    public function store(CreateBankAccountRequest $request)
    {
        $bankAccount = $this->bankAccountRepo->save(Input::all());

        $bankId = Input::get('bank_id');
        $username = trim(Input::get('bank_username'));
        $password = trim(Input::get('bank_password'));

        return json_encode($this->bankAccountService->loadBankAccounts($bankAccount, $username, $password, true));
    }

    public function importExpenses($bankId)
    {
        return $this->bankAccountService->importExpenses($bankId, Input::all());
    }

    public function showImportOFX()
    {
        return view('accounts.import_ofx');
    }

    public function doImportOFX(Request $request)
    {
        $file = File::get($request->file('ofx_file'));

        try {
            $data = $this->bankAccountService->parseOFX($file);
        } catch (Exception $e) {
            Session::now('error', trans('texts.ofx_parse_failed'));
            Utils::logError($e);

            return view('accounts.import_ofx');
        }

        $data = [
            'banks' => null,
            'bankAccount' => null,
            'transactions' => json_encode([$data]),
        ];

        return View::make('accounts.bank_account', $data);
    }
}
