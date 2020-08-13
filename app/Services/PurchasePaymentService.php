<?php

namespace App\Services;

use App;
use App\Libraries\Utils;
use App\Models\Activity;
use App\Models\BillCredit;
use App\Models\Bill;
use App\Ninja\Datatables\PaymentDatatable;
use App\Ninja\Repositories\AccountRepository;
use App\Ninja\Repositories\BillPaymentRepository;
use DateTime;
use Exception;
use Illuminate\Support\Facades\Auth;

class PurchasePaymentService extends BaseService
{


    private $datatableService;
    private $paymentRepo;
    private $accountRepo;

    public function __construct(
        BillPaymentRepository $paymentRepo,
        AccountRepository $accountRepo,
        DatatableService $datatableService)
    {
        $this->datatableService = $datatableService;
        $this->paymentRepo = $paymentRepo;
        $this->accountRepo = $accountRepo;
    }

    protected function getRepo()
    {
        return $this->paymentRepo;
    }

    public function autoBillBill(Bill $Bill)
    {
        if (!$Bill->canBePaid()) {
            return false;
        }

        $vendor = $Bill->client;

        $account = $vendor->account;

        $invitation = $Bill->invitations->first();

        if (!$invitation) {
            return false;
        }

        $Bill->markSentIfUnsent();

        if ($credits = $vendor->credits->sum('balance')) {
            $balance = $Bill->balance;
            $amount = min($credits, $balance);
            $data = [
                'payment_type_id' => PAYMENT_TYPE_CREDIT,
                'invoice_id' => $Bill->id,
                'client_id' => $vendor->id,
                'amount' => $amount,
            ];
            $payment = $this->paymentRepo->save($data);
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
            $BillDate = DateTime::createFromFormat('Y-m-d', $Bill->invoice_date);
            $minDueDate = clone $BillDate;
            $minDueDate->modify('+10 days');

            if (date_create() < $minDueDate) {
                // Can't auto bill now
                return false;
            }

            if ($Bill->partial > 0) {
                // The amount would be different than the amount in the email
                return false;
            }

            $firstUpdate = Activity::where('invoice_id', '=', $Bill->id)
                ->where('activity_type_id', '=', ACTIVITY_TYPE_UPDATE_INVOICE)
                ->first();

            if ($firstUpdate) {
                $backup = json_decode($firstUpdate->json_backup);

                if ($backup->balance != $Bill->balance || $backup->due_date != $Bill->due_date) {
                    // It's changed since we sent the email can't bill now
                    return false;
                }
            }

            if ($Bill->payments->count()) {
                // ACH requirements are strict; don't auto bill this
                return false;
            }
        }

        try {
            return $paymentDriver->completeOnsitePurchase(false, $paymentMethod);
        } catch (Exception $exception) {
            $subject = trans('texts.auto_bill_failed', ['invoice_number' => $Bill->invoice_number]);
            $message = sprintf('%s: %s', ucwords($paymentDriver->providerName()), $exception->getMessage());
            //$message .= $exception->getTraceAsString();
            Utils::logError($message, 'PHP', true);
            if (App::runningInConsole()) {
                $mailer = app('App\Ninja\Mailers\UserMailer');
                $mailer->sendMessage($Bill->user, $subject, $message, [
                    'invoice' => $Bill
                ]);
            }

            return false;
        }
    }

    public function save($input, $payment = null, $Bill = null)
    {
        // if the payment amount is more than the balance create a credit
        if ($Bill && Utils::parseFloat($input['amount']) > $Bill->balance) {
            $credit = BillCredit::createNew();
            $credit->client_id = $Bill->client_id;
            $credit->credit_date = date_create()->format('Y-m-d');
            $credit->amount = $credit->balance = $input['amount'] - $Bill->balance;
            $credit->private_notes = trans('texts.credit_created_by', ['transaction_reference' => isset($input['transaction_reference']) ? $input['transaction_reference'] : '']);
            $credit->save();
            $input['amount'] = $Bill->balance;
        }

        return $this->paymentRepo->save($input, $payment);
    }

    public function getDatatable($vendorPublicId, $search)
    {
        $datatable = new PaymentDatatable(true, $vendorPublicId);
        $query = $this->paymentRepo->find($vendorPublicId, $search);

        if (!Utils::hasPermission('view_payments')) {
            $query->where('payments.user_id', '=', Auth::user()->id);
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
                        $mailer = app('App\Ninja\Mailers\ContactMailer');
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
