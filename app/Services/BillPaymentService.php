<?php

namespace App\Services;

use App;
use App\Libraries\Utils;
use App\Models\Activity;
use App\Models\VendorCredit;
use App\Models\Bill;
use App\Ninja\Datatables\BillPaymentDatatable;
use App\Ninja\Repositories\AccountRepository;
use App\Ninja\Repositories\BillPaymentRepository;
use DateTime;
use Exception;
use Illuminate\Support\Facades\Auth;

class BillPaymentService extends BaseService
{


    private $datatableService;
    private $billPaymentRepo;
    private $accountRepo;

    public function __construct(BillPaymentRepository $billPaymentRepo, AccountRepository $accountRepo, DatatableService $datatableService)
    {
        $this->datatableService = $datatableService;
        $this->billPaymentRepo = $billPaymentRepo;
        $this->accountRepo = $accountRepo;
    }

    protected function getRepo()
    {
        return $this->billPaymentRepo;
    }

    public function autoBill(Bill $bill)
    {
        if (!$bill->canBePaid()) {
            return false;
        }

        $vendor = $bill->vendor;

        $account = $vendor->account;

        $invitation = $bill->bill_invitations->first();

        if (!$invitation) {
            return false;
        }

        $bill->markSentIfUnsent();

        if ($credits = $vendor->credits->sum('balance')) {
            $balance = $bill->balance;
            $amount = min($credits, $balance);
            $data = [
                'payment_type_id' => PAYMENT_TYPE_CREDIT,
                'bill_id' => $bill->id,
                'vendor_id' => $vendor->id,
                'amount' => $amount,
            ];

            $payment = $this->billPaymentRepo->save($data);

            if ($amount == $balance) {
                return $payment;
            }
        }

        $paymentDriver = $account->paymentDriver($invitation, GATEWAY_TYPE_TOKEN);

        if (!$paymentDriver) {
            return false;
        }

        $customer = $paymentDriver->customer();

        if (!$customer) {
            return false;
        }

        $paymentMethod = $customer->default_payment_method;

        if (!$paymentMethod) {
            return false;
        }

        if ($paymentMethod->requiresDelayedAutoBill()) {
            $billDate = DateTime::createFromFormat('Y-m-d', $bill->bill_date);
            $minDueDate = clone $billDate;
            $minDueDate->modify('+10 days');

            if (date_create() < $minDueDate) {
                // Can't auto bill now
                return false;
            }

            if ($bill->partial > 0) {
                // The amount would be different than the amount in the email
                return false;
            }

            $firstUpdate = Activity::where('bill_id', $bill->id)
                ->where('activity_type_id', ACTIVITY_TYPE_UPDATE_INVOICE)
                ->first();

            if ($firstUpdate) {
                $backup = json_decode($firstUpdate->json_backup);

                if ($backup->balance != $bill->balance || $backup->due_date != $bill->due_date) {
                    // It's changed since we sent the email can't bill now
                    return false;
                }
            }

            if ($bill->payments->count()) {
                // ACH requirements are strict; don't auto bill this
                return false;
            }
        }

        try {
            return $paymentDriver->completeOnsiteBill(false, $paymentMethod);
        } catch (Exception $exception) {
            $subject = trans('texts.auto_bill_failed', ['invoice_number' => $bill->invoice_number]);
            $message = sprintf('%s: %s', ucwords($paymentDriver->providerName()), $exception->getMessage());
            //$message .= $exception->getTraceAsString();
            Utils::logError($message, 'PHP', true);
            if (App::runningInConsole()) {
                $mailer = app('App\Ninja\Mailers\BillMailer');
                $mailer->sendMessage($bill->user, $subject, $message, [
                    'bill' => $bill
                ]);
            }

            return false;
        }
    }

    public function save($input, $payment = null, $bill = null)
    {
        // if the payment amount is more than the balance create a credit
        if ($bill && Utils::parseFloat($input['amount']) > $bill->balance) {
            $credit = VendorCredit::createNew();
            $credit->vendor_id = $bill->vendor_id;
            $credit->credit_date = date_create()->format('Y-m-d');
            $credit->amount = $credit->balance = $input['amount'] - $bill->balance;
            $credit->private_notes = trans('texts.credit_created_by', ['transaction_reference' => isset($input['transaction_reference']) ? $input['transaction_reference'] : '']);
            $credit->created_by = auth()->user()->username;
            $credit->save();
            $input['amount'] = $bill->balance;
        }

        return $this->billPaymentRepo->save($input, $payment);
    }

    public function getDatatable($vendorPublicId, $search)
    {
        $datatable = new BillPaymentDatatable(true, $vendorPublicId);

        $query = $this->billPaymentRepo->find($vendorPublicId, $search);

        if (!Utils::hasPermission('view_bill_payment')) {
            $query->where('bill_payments.user_id', Auth::user()->id);
        }

        return $this->datatableService->createDatatable($datatable, $query);
    }

    public function bulk($ids, $action, $params = [])
    {
        if ($action == 'refund') {
            if (!$ids) {
                return 0;
            }

            $payments = $this->getRepo()->findByPublicIdsWithTrashed($ids);
            $successful = 0;

            foreach ($payments as $payment) {
                if (Auth::user()->can('edit', $payment)) {
                    $amount = !empty($params['refund_amount']) ? floatval($params['refund_amount']) : null;
                    $sendEmail = !empty($params['refund_email']) ? boolval($params['refund_email']) : false;
                    $paymentDriver = false;
                    $refunded = false;

                    if ($accountGateway = $payment->account_gateway) {
                        $paymentDriver = $accountGateway->paymentDriver();
                    }

                    if ($paymentDriver && $paymentDriver->canRefundPayments) {
                        if ($paymentDriver->refundPayment($payment, $amount)) {
                            $successful++;
                            $refunded = true;
                        }
                    } else {
                        $payment->recordRefund($amount);
                        $successful++;
                        $refunded = true;
                    }

                    if ($refunded && $sendEmail) {
                        $mailer = app('App\Ninja\Mailers\VendorMailer');
                        $mailer->sendPaymentConfirmation($payment, $amount);
                    }
                }
            }

            return $successful;
        } else {
            return parent::bulk($ids, $action);
        }
    }
}
