<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePaymentTermRequest;
use App\Http\Requests\PaymentTermRequest;
use App\Http\Requests\UpdatePaymentTermRequest;
use App\Libraries\Utils;
use App\Models\PaymentTerm;
use App\Services\PaymentTermService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Redirect;

class PaymentTermController extends BaseController
{
    /**
     * @var PaymentTermService
     */
    protected $paymentTermService;

    /**
     * PaymentTermController constructor.
     *
     * @param PaymentTermService $paymentTermService
     */
    public function __construct(PaymentTermService $paymentTermService)
    {
        //parent::__construct();

        $this->paymentTermService = $paymentTermService;
    }

    public function index()
    {
        return Redirect::to('settings/' . ACCOUNT_PAYMENT_TERMS);
    }

    public function getDatatable()
    {
        $accountId = Auth::user()->account_id;

        return $this->paymentTermService->getDatatable($accountId);
    }

    public function edit($publicId)
    {
        $data = [
            'paymentTerm' => PaymentTerm::scope($publicId)->firstOrFail(),
            'method' => 'PUT',
            'url' => 'payment_terms/' . $publicId,
            'title' => trans('texts.edit_payment_term'),
        ];

        return View::make('accounts.payment_term', $data);
    }

    public function create()
    {
        $data = [
            'paymentTerm' => null,
            'method' => 'POST',
            'url' => 'payment_terms',
            'title' => trans('texts.create_payment_term'),
        ];

        return View::make('accounts.payment_term', $data);
    }

    public function store(CreatePaymentTermRequest $request)
    {
        return $this->save();
    }

    public function update(UpdatePaymentTermRequest $request, $publicId)
    {
        return $this->save($publicId);
    }

    private function save($publicId = false)
    {
        if ($publicId) {
            $paymentTerm = PaymentTerm::scope($publicId)->firstOrFail();
        } else {
            $paymentTerm = PaymentTerm::createNew();
        }

        $paymentTerm->num_days = Utils::parseInt(Input::get('num_days'));
        $paymentTerm->name = 'Net ' . $paymentTerm->num_days;
        $paymentTerm->save();

        $message = $publicId ? trans('texts.updated_payment_term') : trans('texts.created_payment_term');
        Session::flash('message', $message);

        return Redirect::to('settings/' . ACCOUNT_PAYMENT_TERMS);
    }

    public function bulk()
    {
        $action = Input::get('bulk_action');
        $ids = Input::get('bulk_public_id');
        $count = $this->paymentTermService->bulk($ids, $action);

        Session::flash('message', trans('texts.archived_payment_term'));

        return Redirect::to('settings/' . ACCOUNT_PAYMENT_TERMS);
    }
}
