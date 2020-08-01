<?php

namespace App\Services;

use App;
use App\Libraries\Utils;
use App\Models\Activity;
use App\Models\PurchaseCredit;
use App\Models\PurchaseInvoice;
use App\Ninja\Datatables\PaymentDatatable;
use App\Ninja\Repositories\AccountRepository;
use App\Ninja\Repositories\PurchasePaymentRepository;
use DateTime;
use Exception;
use Illuminate\Support\Facades\Auth;

class PurchasePaymentService extends BaseService
{


    private $datatableService;
    private $paymentRepo;
    private $accountRepo;

    public function __construct(
        PurchasePaymentRepository $paymentRepo,
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

    public function autoBillPurchaseInvoice(PurchaseInvoice $purchaseInvoice)
    {
        if (!$purchaseInvoice->canBePaid()) {
            return false;
        }

        $vendor = $purchaseInvoice->client;

        $account = $vendor->account;

        $invitation = $purchaseInvoice->invitations->first();

        if (!$invitation) {
            return false;
        }

        $purchaseInvoice->markSentIfUnsent();

        if ($credits = $vendor->credits->sum('balance')) {
            $balance = $purchaseInvoice->balance;
            $amount = min($credits, $balance);
            $data = [
                'payment_type_id' => PAYMENT_TYPE_CREDIT,
                'invoice_id' => $purchaseInvoice->id,
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
            $purchaseInvoiceDate = DateTime::createFromFormat('Y-m-d', $purchaseInvoice->invoice_date);
            $minDueDate = clone $purchaseInvoiceDate;
            $minDueDate->modify('+10 days');

            if (date_create() < $minDueDate) {
                // Can't auto bill now
                return false;
            }

            if ($purchaseInvoice->partial > 0) {
                // The amount would be different than the amount in the email
                return false;
            }

            $firstUpdate = Activity::where('invoice_id', '=', $purchaseInvoice->id)
                ->where('activity_type_id', '=', ACTIVITY_TYPE_UPDATE_INVOICE)
                ->first();

            if ($firstUpdate) {
                $backup = json_decode($firstUpdate->json_backup);

                if ($backup->balance != $purchaseInvoice->balance || $backup->due_date != $purchaseInvoice->due_date) {
                    // It's changed since we sent the email can't bill now
                    return false;
                }
            }

            if ($purchaseInvoice->payments->count()) {
                // ACH requirements are strict; don't auto bill this
                return false;
            }
        }

        try {
            return $paymentDriver->completeOnsitePurchase(false, $paymentMethod);
        } catch (Exception $exception) {
            $subject = trans('texts.auto_bill_failed', ['invoice_number' => $purchaseInvoice->invoice_number]);
            $message = sprintf('%s: %s', ucwords($paymentDriver->providerName()), $exception->getMessage());
            //$message .= $exception->getTraceAsString();
            Utils::logError($message, 'PHP', true);
            if (App::runningInConsole()) {
                $mailer = app('App\Ninja\Mailers\UserMailer');
                $mailer->sendMessage($purchaseInvoice->user, $subject, $message, [
                    'invoice' => $purchaseInvoice
                ]);
            }

            return false;
        }
    }

    public function save($input, $payment = null, $purchaseInvoice = null)
    {
        // if the payment amount is more than the balance create a credit
        if ($purchaseInvoice && Utils::parseFloat($input['amount']) > $purchaseInvoice->balance) {
            $credit = PurchaseCredit::createNew();
            $credit->client_id = $purchaseInvoice->client_id;
            $credit->credit_date = date_create()->format('Y-m-d');
            $credit->amount = $credit->balance = $input['amount'] - $purchaseInvoice->balance;
            $credit->private_notes = trans('texts.credit_created_by', ['transaction_reference' => isset($input['transaction_reference']) ? $input['transaction_reference'] : '']);
            $credit->save();
            $input['amount'] = $purchaseInvoice->balance;
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
