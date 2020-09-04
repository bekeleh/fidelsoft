<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateBillPaymentRequest;
use App\Http\Requests\BillPaymentRequest;
use App\Http\Requests\UpdateBillPaymentRequest;
use App\Libraries\Utils;
use App\Models\Vendor;
use App\Models\Bill;
use App\Models\BillPayment;
use App\Ninja\Datatables\BillPaymentDatatable;
use App\Ninja\Mailers\VendorMailer;
use App\Ninja\Repositories\BillPaymentRepository;
use App\Services\BillPaymentService;
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
    protected $paymentRepo;
    protected $contactMailer;
    protected $paymentService;

    /**
     * PaymentController constructor.
     *
     * @param BillPaymentRepository $paymentRepo
     * @param BillPaymentService $paymentService
     * @param VendorMailer $contactMailer
     */
    public function __construct(BillPaymentRepository $paymentRepo, BillPaymentService $paymentService, VendorMailer $contactMailer)
    {
        $this->paymentRepo = $paymentRepo;
        $this->paymentService = $paymentService;
        $this->contactMailer = $contactMailer;
    }

    public function index()
    {
        $this->authorize('view', ENTITY_BILL_PAYMENT);
        return View::make('list_wrapper', [
            'entityType' => ENTITY_BILL_PAYMENT,
            'datatable' => new BillPaymentDatatable(),
            'title' => trans('texts.bill_payments'),
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
     * @param BillPaymentRequest $request
     * @return mixed
     * @throws AuthorizationException
     */
    public function create(BillPaymentRequest $request)
    {
        $this->authorize('create', ENTITY_BILL_PAYMENT);
        $user = auth()->user();
        $account = $user->account;

        $bills = Bill::scope()->bills()
            ->where('bills.bill_status_id', '!=', BILL_STATUS_PAID)
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
            'payment' => null,
            'method' => 'POST',
            'url' => 'bill_payments',
            'title' => trans('texts.new_payment'),
            'paymentTypeId' => Input::get('paymentTypeId'),
            'bills' => $bills,
            'totalCredit' => $totalCredit,
            'paymentStatuses' => Cache::get('paymentStatuses'),
            'paymentTypes' => Cache::get('paymentTypes'),
            'currencies' => Cache::get('currencies'),
            'vendors' => Vendor::scope()->with('contacts')->orderBy('name')->get(),
        ];

        return View::make('bill_payments.edit', $data);
    }

    /**
     * @param $publicId
     * @return RedirectResponse
     */
    public function show($publicId)
    {
        Session::reflash();

        return redirect()->to("bill_payments/{$publicId}/edit");
    }

    /**
     * @param BillPaymentRequest $request
     * @return mixed
     * @throws AuthorizationException
     */
    public function edit(BillPaymentRequest $request)
    {
        $this->authorize('edit', ENTITY_BILL_PAYMENT);
        $payment = $request->entity();
        $payment->payment_date = Utils::fromSqlDate($payment->payment_date);

        $actions = [];
        if ($payment->billJsonBackup()) {
            $actions[] = ['url' => url("/bills/bill_history/{$payment->bill->public_id}?payment_id={$payment->public_id}"), 'label' => trans('texts.view_bill')];
        }

        $actions[] = ['url' => url("/bills/{$payment->bill->public_id}/edit"), 'label' => trans('texts.edit_bill')];
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
            'vendor' => null,
            'bill' => null,
            'payment' => $payment,
            'entity' => $payment,
            'method' => 'PUT',
            'url' => 'bill_payments/' . $payment->public_id,
            'title' => trans('texts.edit_payment'),
            'actions' => $actions,
        ];

        $data = array_merge($data, self::getViewModel());

        return View::make('bill_payments.edit', $data);
    }

    /**
     * @param CreateBillPaymentRequest $request
     * @return RedirectResponse
     */
    public function store(CreateBillPaymentRequest $request)
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

        $payment = $this->paymentService->save($input, null, $request->bill);

//       if vendor requires receipt via email
        if (Input::get('email_receipt')) {
            $this->contactMailer->sendPaymentConfirmation($payment);
            $message = trans($credit ? 'texts.created_payment_and_credit_emailed_vendor' : 'texts.created_payment_emailed_vendor');
        } else {
            $message = trans($credit ? 'texts.created_payment_and_credit' : 'texts.created_payment');
        }

        Session::flash('message', $message);

        $url = url($payment->getRoute());

        return redirect()->to($url);
    }


    /**
     * @param UpdateBillPaymentRequest $request
     * @return RedirectResponse
     */
    public function update(UpdateBillPaymentRequest $request)
    {
        if (in_array($request->action, ['archive', 'delete', 'restore', 'refund', 'email'])) {
            return self::bulk();
        }
        $input = $request->input();
        $payment = $this->paymentRepo->save($input, $request->entity());

        Session::flash('message', trans('texts.updated_payment'));

        return redirect()->to($payment->getRoute());
    }

    /**
     * @return RedirectResponse
     */
    public function bulk()
    {
        $action = Input::get('action');
        $ids = Input::get('public_id') ? Input::get('public_id') : Input::get('ids');

        if ($action === 'email') {
            $payment = BillPayment::scope($ids)->withArchived()->first();
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
            'paymentStatuses' => Cache::get('paymentStatuses'),
            'paymentTypes' => Cache::get('paymentTypes'),
            'currencies' => Cache::get('currencies'),
            'vendors' => Vendor::scope()->with('contacts')->orderBy('name')->get(),
            'bills' => Bill::scope()->bills()->where('is_public', true)->with('vendor', 'bill_status')->orderBy('bill_number')->get(),
        ];
    }

}
