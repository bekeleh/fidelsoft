<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePaymentRequest;
use App\Http\Requests\PaymentRequest;
use App\Http\Requests\UpdatePaymentRequest;
use App\Libraries\Utils;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\Payment;
use App\Ninja\Datatables\PaymentDatatable;
use App\Ninja\Mailers\ContactMailer;
use App\Ninja\Repositories\PaymentRepository;
use App\Services\PaymentService;
use DropdownButton;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;

class PaymentController extends BaseController
{

    protected $entityType = ENTITY_PAYMENT;
    protected $paymentRepo;
    protected $contactMailer;
    protected $paymentService;

    /**
     * PaymentController constructor.
     *
     * @param PaymentRepository $paymentRepo
     * @param ContactMailer $contactMailer
     * @param PaymentService $paymentService
     */
    public function __construct(PaymentRepository $paymentRepo, ContactMailer $contactMailer, PaymentService $paymentService)
    {
        $this->paymentRepo = $paymentRepo;
        $this->contactMailer = $contactMailer;
        $this->paymentService = $paymentService;
    }

    public function index()
    {
        $this->authorize('index', auth::user(), $this->entityType);
        return View::make('list_wrapper', [
            'entityType' => ENTITY_PAYMENT,
            'datatable' => new PaymentDatatable(),
            'title' => trans('texts.payments'),
        ]);
    }

    public function getDatatable($clientPublicId = null)
    {
        return $this->paymentService->getDatatable($clientPublicId, Input::get('sSearch'));
    }

    public function create(PaymentRequest $request)
    {
        $this->authorize('create', auth::user(), $this->entityType);
        $user = auth()->user();
        $account = $user->account;

        $invoices = Invoice::scope()->invoices()
            ->where('invoices.invoice_status_id', '!=', INVOICE_STATUS_PAID)
            ->with('client', 'invoice_status')
            ->orderBy('invoice_number')->get();

        $clientPublicId = Input::old('client') ? Input::old('client') : ($request->client_id ?: 0);
        $invoicePublicId = Input::old('invoice') ? Input::old('invoice') : ($request->invoice_id ?: 0);

        $totalCredit = false;
        if ($clientPublicId && $client = Client::scope($clientPublicId)->first()) {
            $totalCredit = $account->formatMoney($client->getTotalCredit(), $client);
        } elseif ($invoicePublicId && $invoice = Invoice::scope($invoicePublicId)->first()) {
            $totalCredit = $account->formatMoney($invoice->client->getTotalCredit(), $client);
        }

        $data = [
            'account' => Auth::user()->account,
            'clientPublicId' => $clientPublicId,
            'invoicePublicId' => $invoicePublicId,
            'invoice' => null,
            'invoices' => $invoices,
            'payment' => null,
            'method' => 'POST',
            'url' => 'payments',
            'title' => trans('texts.new_payment'),
            'paymentTypeId' => Input::get('paymentTypeId'),
            'clients' => Client::scope()->with('contacts')->orderBy('name')->get(),
            'totalCredit' => $totalCredit,
        ];

        return View::make('payments.edit', $data);
    }

    public function show($publicId)
    {
        Session::reflash();

        return redirect()->to("payments/{$publicId}/edit");
    }

    public function edit(PaymentRequest $request)
    {
        $this->authorize('edit', auth::user(), $this->entityType);
        $payment = $request->entity();
        $payment->payment_date = Utils::fromSqlDate($payment->payment_date);

        $actions = [];
        if ($payment->invoiceJsonBackup()) {
            $actions[] = ['url' => url("/invoices/invoice_history/{$payment->invoice->public_id}?payment_id={$payment->public_id}"), 'label' => trans('texts.view_invoice')];
        }

        $actions[] = ['url' => url("/invoices/{$payment->invoice->public_id}/edit"), 'label' => trans('texts.edit_invoice')];
        $actions[] = DropdownButton::DIVIDER;
        $actions[] = ['url' => 'javascript:submitAction("email")', 'label' => trans('texts.email_payment')];

        if ($payment->canBeRefunded()) {
            $actions[] = ['url' => "javascript:showRefundModal({$payment->public_id}, \"{$payment->getCompletedAmount()}\", \"{$payment->present()->completedAmount}\", \"{$payment->present()->currencySymbol}\")", 'label' => trans('texts.refund_payment')];
        }

        $actions[] = DropdownButton::DIVIDER;
        if (!$payment->trashed()) {
            $actions[] = ['url' => 'javascript:submitAction("archive")', 'label' => trans('texts.archive_payment')];
            $actions[] = ['url' => 'javascript:onDeleteClick()', 'label' => trans('texts.delete_payment')];
        } else {
            $actions[] = ['url' => 'javascript:submitAction("restore")', 'label' => trans('texts.restore_expense')];
        }

        $data = [
            'account' => Auth::user()->account,
            'client' => null,
            'invoice' => null,
            'invoices' => Invoice::scope()->invoices()
                ->whereIsPublic(true)
                ->with('client', 'invoice_status')
                ->orderBy('invoice_number')->get(),
            'payment' => $payment,
            'entity' => $payment,
            'method' => 'PUT',
            'url' => 'payments/' . $payment->public_id,
            'title' => trans('texts.edit_payment'),
            'actions' => $actions,
            'paymentTypes' => Cache::get('paymentTypes'),
            'clients' => Client::scope()->with('contacts')->orderBy('name')->get(),
        ];

        return View::make('payments.edit', $data);
    }

    public function store(CreatePaymentRequest $request)
    {
        // check payment has been marked sent
        $request->invoice->markSentIfUnsent();
        $input = $request->input();
        $amount = Utils::parseFloat($input['amount']);
        $credit = false;
        // if the payment amount is more than the balance create a credit
        if ($amount > $request->invoice->balance) {
            $credit = true;
        }

        $payment = $this->paymentService->save($input, null, $request->invoice);

        if (Input::get('email_receipt')) {
            $this->contactMailer->sendPaymentConfirmation($payment);
            $message = trans($credit ? 'texts.created_payment_and_credit_emailed_client' : 'texts.created_payment_emailed_client');
        } else {
            $message = trans($credit ? 'texts.created_payment_and_credit' : 'texts.created_payment');
        }

        $url = url($payment->client->getRoute());
        return redirect()->to($url)->with('success', $message);
    }


    public function update(UpdatePaymentRequest $request)
    {
        if (in_array($request->action, ['archive', 'delete', 'restore', 'refund', 'email'])) {
            return self::bulk();
        }

        $payment = $this->paymentRepo->save($request->input(), $request->entity());

        return redirect()->to($payment->getRoute())->with('success', trans('texts.updated_payment'));
    }

    public function bulk()
    {
        $action = Input::get('action');
        $ids = Input::get('public_id') ? Input::get('public_id') : Input::get('ids');

        if ($action === 'email') {
            $payment = Payment::scope($ids)->withArchived()->first();
            $this->contactMailer->sendPaymentConfirmation($payment);
            $message = trans('texts.emailed_payment');
//            Session::flash('message', trans('texts.emailed_payment'));
        } else {
            $count = $this->paymentService->bulk($ids, $action, [
                'refund_amount' => Input::get('refund_amount'),
                'refund_email' => Input::get('refund_email'),
            ]);
            if ($count > 0) {
                $message = Utils::pluralize($action == 'refund' ? 'refunded_payment' : $action . 'd_payment', $count);
//                Session::flash('message', $message);
            }
        }

        return $this->returnBulk(ENTITY_PAYMENT, $action, $ids)->with('success', $message);
    }
}
