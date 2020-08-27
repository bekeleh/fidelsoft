<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePaymentRequest;
use App\Http\Requests\PaymentRequest;
use App\Http\Requests\UpdatePaymentRequest;
use App\Libraries\Utils;
use App\Models\Vendor;
use App\Models\Bill;
use App\Models\BillPayment;
use App\Ninja\Datatables\BillPaymentDatatable;
use App\Ninja\Mailers\ContactMailer;
use App\Ninja\Repositories\PaymentRepository;
use App\Services\PaymentService;
use DropdownButton;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;

class BillPaymentController extends BaseController
{

    protected $entityType = ENTITY_BILL_PAYMENT;
    protected $billPaymentRepo;
    protected $contactMailer;
    protected $billPaymentService;

    /**
     * PaymentController constructor.
     *
     * @param PaymentRepository $billPaymentRepo
     * @param PaymentService $billPaymentService
     * @param ContactMailer $contactMailer
     */
    public function __construct(PaymentRepository $billPaymentRepo, PaymentService $billPaymentService, ContactMailer $contactMailer)
    {
        $this->billPaymentRepo = $billPaymentRepo;
        $this->paymentService = $billPaymentService;
        $this->contactMailer = $contactMailer;
    }

    /**
     * @return mixed
     * @throws AuthorizationException
     */
    public function index()
    {
        $this->authorize('view', ENTITY_BILL_PAYMENT);
        return View::make('list_wrapper', [
            'entityType' => ENTITY_BILL_PAYMENT,
            'datatable' => new BillPaymentDatatable(),
            'title' => trans('texts.payments'),
        ]);
    }

    /**
     * @param null $vendorPublicId
     * @return bool
     */
    public function getDatatable($vendorPublicId = null)
    {
        return $this->paymentService->getDatatable($vendorPublicId, Input::get('sSearch'));
    }

    /**
     * @param PaymentRequest $request
     * @return mixed
     * @throws AuthorizationException
     */
    public function create(PaymentRequest $request)
    {
        $this->authorize('create', ENTITY_BILL_PAYMENT);
        $user = auth()->user();
        $account = $user->account;

        $bills = Bill::scope()->bills()
            ->where('bills.bill_status_id', '!=', INVOICE_STATUS_PAID)
            ->with('vendor', 'bill_status')
            ->orderBy('bill_number')->get();

        $vendorPublicId = Input::old('vendor') ? Input::old('vendor') : ($request->vendor_id ?: 0);
        $billPublicId = Input::old('bill') ? Input::old('bill') : ($request->bill_id ?: 0);

        $totalCredit = false;
        if ($vendorPublicId && $vendor = Vendor::scope($vendorPublicId)->first()) {
            $totalCredit = $account->formatMoney($vendor->getTotalCredit(), $vendor);
        } elseif ($billPublicId && $bill = Bill::scope($billPublicId)->first()) {
            $totalCredit = $account->formatMoney($bill->vendor->getTotalCredit(), $vendor);
        }

        $data = [
            'account' => Auth::user()->account,
            'vendorPublicId' => $vendorPublicId,
            'billPublicId' => $billPublicId,
            'bill' => null,
            'billPayment' => null,
            'method' => 'POST',
            'url' => 'payments',
            'title' => trans('texts.new_payment'),
            'paymentTypeId' => Input::get('paymentTypeId'),
            'bills' => $bills,
            'vendors' => Vendor::scope()->with('contacts')->orderBy('name')->get(),
            'totalCredit' => $totalCredit,
        ];

        return View::make('payments.edit', $data);
    }

    /**
     * @param $publicId
     * @return RedirectResponse
     */
    public function show($publicId)
    {
        Session::reflash();

        return redirect()->to("payments/{$publicId}/edit");
    }

    /**
     * @param PaymentRequest $request
     * @return mixed
     * @throws AuthorizationException
     */
    public function edit(PaymentRequest $request)
    {
        $this->authorize('edit', ENTITY_BILL_PAYMENT);
        $billPayment = $request->entity();
        $billPayment->payment_date = Utils::fromSqlDate($billPayment->payment_date);

        $actions = [];
        if ($billPayment->billJsonBackup()) {
            $actions[] = ['url' => url("/bills/bill_history/{$billPayment->bill->public_id}?payment_id={$billPayment->public_id}"), 'label' => trans('texts.view_bill')];
        }

        $actions[] = ['url' => url("/bills/{$billPayment->bill->public_id}/edit"), 'label' => trans('texts.edit_bill')];
        $actions[] = DropdownButton::DIVIDER;
        $actions[] = ['url' => 'javascript:submitAction("email")', 'label' => trans('texts.email_payment')];

        if ($billPayment->canBeRefunded()) {
            $actions[] = ['url' => "javascript:showRefundModal({$billPayment->public_id}, \"{$billPayment->getCompletedAmount()}\", \"{$billPayment->present()->completedAmount}\", \"{$billPayment->present()->currencySymbol}\")", 'label' => trans('texts.refund_payment')];
        }

        $actions[] = DropdownButton::DIVIDER;
        if (!$billPayment->trashed()) {
            $actions[] = ['url' => 'javascript:submitAction("archive")', 'label' => trans('texts.archive_payment')];
            $actions[] = ['url' => 'javascript:onDeleteClick()', 'label' => trans('texts.delete_payment')];
        } else {
            $actions[] = ['url' => 'javascript:submitAction("restore")', 'label' => trans('texts.restore_expense')];
        }

        $data = [
            'vendor' => null,
            'bill' => null,
            'payment' => $billPayment,
            'entity' => $billPayment,
            'method' => 'PUT',
            'url' => 'payments/' . $billPayment->public_id,
            'title' => trans('texts.edit_payment'),
            'actions' => $actions,
        ];

        array_merge($data, self::getViewModel());

        return View::make('payments.edit', $data);
    }

    /**
     * @param CreatePaymentRequest $request
     * @return RedirectResponse
     */
    public function store(CreatePaymentRequest $request)
    {
        // check payment has been marked sent
        $request->bill->markSentIfUnsent();
        $input = $request->input();
        $amount = Utils::parseFloat($input['amount']);
        $credit = false;
        // if the payment amount is more than the balance create a credit
        if ($amount > $request->bill->balance) {
            $credit = true;
        }

        $billPayment = $this->paymentService->save($input, null, $request->bill);
//       if vendor requires receipt via email
        if (Input::get('email_receipt')) {
            $this->contactMailer->sendPaymentConfirmation($billPayment);
            $message = trans($credit ? 'texts.created_payment_and_credit_emailed_vendor' : 'texts.created_payment_emailed_vendor');
        } else {
            $message = trans($credit ? 'texts.created_payment_and_credit' : 'texts.created_payment');
        }

        $url = url($billPayment->vendor->getRoute());

        return redirect()->to($url)->with('success', $message);
    }


    /**
     * @param UpdatePaymentRequest $request
     * @return RedirectResponse
     */
    public function update(UpdatePaymentRequest $request)
    {
        if (in_array($request->action, ['archive', 'delete', 'restore', 'refund', 'email'])) {
            return self::bulk();
        }

        $billPayment = $this->billPaymentRepo->save($request->input(), $request->entity());

        return redirect()->to($billPayment->getRoute())->with('success', trans('texts.updated_payment'));
    }

    /**
     * @return RedirectResponse
     */
    public function bulk()
    {
        $action = Input::get('action');
        $ids = Input::get('public_id') ? Input::get('public_id') : Input::get('ids');

        if ($action === 'email') {
            $billPayment = BillPayment::scope($ids)->withArchived()->first();
            $this->contactMailer->sendPaymentConfirmation($billPayment);
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

        return $this->returnBulk(ENTITY_BILL_PAYMENT, $action, $ids)->with('success', $message);
    }

    /**
     * @return array
     */
    private static function getViewModel()
    {
        return [
            'data' => Input::old('data'),
            'account' => Auth::user()->account,
            'paymentTypes' => Cache::get('paymentTypes'),
            'vendors' => Vendor::scope()->with('contacts')->orderBy('name')->get(),
            'bills' => Bill::scope()->bills()->where('is_public', true)->with('vendor', 'bill_status')->orderBy('bill_number')->get(),
        ];
    }

}
