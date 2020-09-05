<?php

namespace App\Http\Controllers;

use App\Events\Vendor\VendorWasUpdatedEvent;
use App\Events\Sale\InvoiceInvitationWasViewedEvent;
use App\Events\Sale\QuoteInvitationWasViewedEvent;
use App\Jobs\Vendor\GenerateBillStatementData;
use App\Libraries\Utils;
use App\Models\Contact;
use App\Models\Document;
use App\Models\PaymentMethod;
use App\Ninja\Repositories\ActivityRepository;
use App\Ninja\Repositories\CreditRepository;
use App\Ninja\Repositories\DocumentRepository;
use App\Ninja\Repositories\InvoiceRepository;
use App\Ninja\Repositories\PaymentRepository;
use App\Ninja\Repositories\TaskRepository;
use App\Services\PaymentService;
use Barracuda\ArchiveStream\ZipArchive;
use Datatable;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Redirect;

class VendorPortalController extends BaseController
{
    private $billRepo;
    private $paymentRepo;
    private $documentRepo;
    private $activityRepo;
    private $paymentService;
    private $creditRepo;
    private $taskRepo;

    /**
     * VendorPortalController constructor.
     * @param InvoiceRepository $billRepo
     * @param PaymentRepository $paymentRepo
     * @param ActivityRepository $activityRepo
     * @param DocumentRepository $documentRepo
     * @param PaymentService $paymentService
     * @param CreditRepository $creditRepo
     * @param TaskRepository $taskRepo
     */
    public function __construct(
        InvoiceRepository $billRepo,
        PaymentRepository $paymentRepo,
        ActivityRepository $activityRepo,
        DocumentRepository $documentRepo,
        PaymentService $paymentService,
        CreditRepository $creditRepo,
        TaskRepository $taskRepo)
    {
        $this->billRepo = $billRepo;
        $this->paymentRepo = $paymentRepo;
        $this->activityRepo = $activityRepo;
        $this->documentRepo = $documentRepo;
        $this->paymentService = $paymentService;
        $this->creditRepo = $creditRepo;
        $this->taskRepo = $taskRepo;
    }

    /**
     * @param $invitationKey
     * @return RedirectResponse|\Illuminate\Http\Response|Redirector
     */
    public function viewInvoice($invitationKey)
    {
        if (!$invitation = $this->billRepo->findInvoiceByInvitation($invitationKey)) {
            return $this->returnError();
        }

        $bill = $invitation->bill;
        $vendor = $bill->vendor;
        $account = $bill->account;

        if (request()->silent) {
            session(['silent:' . $vendor->id => true]);
            return redirect(request()->url() . (request()->borderless ? '?borderless=true' : ''));
        }

        if (!$account->checkSubdomain(Request::server('HTTP_HOST'))) {
            return response()->view('error', [
                'error' => trans('texts.bill_not_found'),
            ]);
        }

        if (!Input::has('phantomjs') && !session('silent:' . $vendor->id) && !Session::has($invitation->invitation_key)
            && (!Auth::check() || Auth::user()->account_id != $bill->account_id)) {
            if ($bill->isType(INVOICE_TYPE_QUOTE)) {
                event(new QuoteInvitationWasViewedEvent($bill, $invitation));
            } else {
                event(new InvoiceInvitationWasViewedEvent($bill, $invitation));
            }
        }

        Session::put($invitation->invitation_key, true); // track this invitation has been seen
        Session::put('contact_key', $invitation->contact->contact_key); // track current contact

        $bill->bill_date = Utils::fromSqlDate($bill->bill_date);
        $bill->due_date = Utils::fromSqlDate($bill->due_date);
        $bill->partial_due_date = Utils::fromSqlDate($bill->partial_due_date);
        $bill->features = [
            'customize_bill_design' => $account->hasFeature(FEATURE_CUSTOMIZE_INVOICE_DESIGN),
            'remove_created_by' => $account->hasFeature(FEATURE_REMOVE_CREATED_BY),
            'bill_settings' => $account->hasFeature(FEATURE_INVOICE_SETTINGS),
        ];
        $bill->bill_fonts = $account->getFontsData();

        if ($design = $account->getCustomDesign($bill->bill_design_id)) {
            $bill->bill_design->javascript = $design;
        } else {
            $bill->bill_design->javascript = $bill->bill_design->pdfmake;
        }
        $contact = $invitation->contact;

        $contact->setVisible([
            'first_name',
            'last_name',
            'email',
            'phone',
            'custom_value1',
            'custom_value2',
        ]);
        $account->load(['date_format', 'datetime_format']);

        // translate the country names
        if ($bill->vendor->country) {
            $bill->vendor->country->name = $bill->vendor->country->getName();
        }
        if ($bill->account->country) {
            $bill->account->country->name = $bill->account->country->getName();
        }

        $data = [];
        $paymentTypes = $this->getPaymentTypes($account, $vendor, $invitation);
        $paymentURL = '';

        if (count($paymentTypes) == 1) {
            $paymentURL = $paymentTypes[0]['url'];
            if (in_array($paymentTypes[0]['gatewayTypeId'], [GATEWAY_TYPE_CUSTOM1, GATEWAY_TYPE_CUSTOM2, GATEWAY_TYPE_CUSTOM3])) {
                // do nothing
            } elseif (!$account->isGatewayConfigured(GATEWAY_PAYPAL_EXPRESS)) {

                $paymentURL = URL::to($paymentURL);
            }
        }

        if ($wepayGateway = $account->getGatewayConfig(GATEWAY_WEPAY)) {
            $data['enableWePayACH'] = $wepayGateway->getAchEnabled();
        }
        if ($stripeGateway = $account->getGatewayConfig(GATEWAY_STRIPE)) {
            //$data['enableStripeSources'] = $stripeGateway->getAlipayEnabled();
            $data['enableStripeSources'] = true;
        }

        $showApprove = $bill->quote_bill_id ? false : true;
        if ($bill->bill_status_id >= INVOICE_STATUS_APPROVED) {
            $showApprove = false;
        }

        $data += [
            'account' => $account,
            'showApprove' => $showApprove,
            'showBreadcrumbs' => false,
            'bill' => $bill->hidePrivateFields(),
            'invitation' => $invitation,
            'billLabels' => $account->getInvoiceLabels(),
            'contact' => $contact,
            'paymentTypes' => $paymentTypes,
            'paymentURL' => $paymentURL,
            'phantomjs' => Input::has('phantomjs'),
            'gatewayTypeId' => count($paymentTypes) == 1 ? $paymentTypes[0]['gatewayTypeId'] : false,
        ];

        if ($bill->canBePaid()) {
            if ($paymentDriver = $account->paymentDriver($invitation, GATEWAY_TYPE_CREDIT_CARD)) {
                $data += [
                    'transactionToken' => $paymentDriver->createTransactionToken(),
                    'partialView' => $paymentDriver->partialView(),
                    'accountGateway' => $paymentDriver->accountGateway,
                ];
            }
        }
        if ($account->hasFeature(FEATURE_DOCUMENTS) && $this->canCreateZip()) {
            $zipDocs = $this->getInvoiceZipDocuments($bill, $size);

            if (count($zipDocs) > 1) {
                $data['documentsZipURL'] = URL::to("vendor/documents/{$invitation->invitation_key}");
                $data['documentsZipSize'] = $size;
            }
        }

        return View::make(request()->borderless ? 'bills.view_borderless' : 'bills.view', $data);
    }

    private function getPaymentTypes($account, $vendor, $invitation)
    {
        $links = [];

        foreach ($account->account_gateways as $accountGateway) {
            $paymentDriver = $accountGateway->paymentDriver($invitation);
            $links = array_merge($links, $paymentDriver->tokenLinks());
            $links = array_merge($links, $paymentDriver->paymentLinks());
        }

        return $links;
    }

    public function download($invitationKey)
    {
        if (!$invitation = $this->billRepo->findInvoiceByInvitation($invitationKey)) {
            return response()->view('error', [
                'error' => trans('texts.bill_not_found'),
                'hideHeader' => true,
            ]);
        }

        $bill = $invitation->bill;
        $decode = !request()->base64;
        $pdfString = $bill->getPDFString($invitation, $decode);

        header('Content-Type: application/pdf');
        header('Content-Length: ' . strlen($pdfString));
        header('Content-disposition: attachment; filename="' . $bill->getFileName() . '"');
        header('Cache-Control: public, must-revalidate, max-age=0');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');

        return $pdfString;
    }

    public function authorizeInvoice($invitationKey)
    {
        if (!$invitation = $this->billRepo->findInvoiceByInvitation($invitationKey)) {
            return RESULT_FAILURE;
        }

        if ($signature = Input::get('signature')) {
            $invitation->signature_base64 = $signature;
            $invitation->signature_date = date_create();
            $invitation->save();
        }

        session(['authorized:' . $invitation->invitation_key => true]);

        return RESULT_SUCCESS;
    }

    public function dashboard($contactKey = false)
    {
        if ($contactKey) {
            $contact = Contact::where('contact_key', '=', $contactKey)->first();
            if (!$contact) {
                return $this->returnError();
            }
            Session::put('contact_key', $contactKey); // track current contact
        } elseif (!$contact = $this->getContact()) {
            return $this->returnError();
        }

        $vendor = $contact->vendor;
        $account = $vendor->account;

        if (request()->silent) {
            session(['silent:' . $vendor->id => true]);

            return redirect(request()->url());
        }

        $color = $account->primary_color ? $account->primary_color : '#777';
        $customer = false;

        if (!$account->enable_vendor_portal) {
            return $this->returnError();
        } elseif (!$account->enable_vendor_portal_dashboard) {
            session()->reflash();
            return redirect()->to('/vendor/bills/');
        }

        if ($paymentDriver = $account->paymentDriver(false, GATEWAY_TYPE_TOKEN)) {
            $customer = $paymentDriver->customer($vendor->id);
        }

        $data = [
            'color' => $color,
            'contact' => $contact,
            'account' => $account,
            'vendor' => $vendor,
            'gateway' => $account->getTokenGateway(),
            'paymentMethods' => $customer ? $customer->payment_methods : false,
            'transactionToken' => $paymentDriver ? $paymentDriver->createTransactionToken() : false,
        ];

        return response()->view('invited.dashboard', $data);
    }

    public function activityDatatable()
    {
        if (!$contact = $this->getContact()) {
            return $this->returnError();
        }

        $vendor = $contact->vendor;

        $query = $this->activityRepo->findByVendorId($vendor->id);
        $query->where('activities.adjustment', '!=', 0);

        return Datatable::query($query)
            ->addColumn('activities.id', function ($model) {
                return Utils::timestampToDateTimeString(strtotime($model->created_at));
            })
            ->addColumn('activity_type_id', function ($model) {
                $data = [
                    'vendor' => Utils::getVendorDisplayName($model),
                    'user' => $model->is_system ? ('<i>' . trans('texts.system') . '</i>') : ($model->account_name),
                    'bill' => $model->bill,
                    'contact' => Utils::getVendorDisplayName($model),
                    'payment' => $model->payment ? ' ' . $model->payment : '',
                    'credit' => $model->payment_amount ? Utils::formatMoney($model->credit, $model->currency_id, $model->country_id) : '',
                    'payment_amount' => $model->payment_amount ? Utils::formatMoney($model->payment_amount, $model->currency_id, $model->country_id) : null,
                    'adjustment' => $model->adjustment ? Utils::formatMoney($model->adjustment, $model->currency_id, $model->country_id) : null,
                ];

                return trans("texts.activity_{$model->activity_type_id}", $data);
            })
            ->addColumn('balance', function ($model) {
                return Utils::formatMoney($model->balance, $model->currency_id, $model->country_id);
            })
            ->addColumn('adjustment', function ($model) {
                return $model->adjustment != 0 ? Utils::wrapAdjustment($model->adjustment, $model->currency_id, $model->country_id) : '';
            })
            ->make();
    }

    public function recurringInvoiceIndex()
    {
        if (!$contact = $this->getContact()) {
            return $this->returnError();
        }

        $account = $contact->account;

        if (!$account->enable_vendor_portal) {
            return $this->returnError();
        }

        $color = $account->primary_color ? $account->primary_color : '#777';
        $columns = ['frequency', 'start_date', 'end_date', 'bill_total'];
        $vendor = $contact->vendor;

        if ($vendor->hasAutoBillConfigurableInvoices()) {
            $columns[] = 'auto_bill';
        }

        $data = [
            'color' => $color,
            'account' => $account,
            'vendor' => $vendor,
            'title' => trans('texts.recurring_bills'),
            'entityType' => ENTITY_RECURRING_INVOICE,
            'columns' => Utils::trans($columns),
            'sortColumn' => 1,
        ];

        return response()->view('public_list', $data);
    }

    public function billIndex()
    {
        if (!$contact = $this->getContact()) {
            return $this->returnError();
        }

        $account = $contact->account;

        if (!$account->enable_vendor_portal) {
            return $this->returnError();
        }

        $color = $account->primary_color ? $account->primary_color : '#777';

        $data = [
            'color' => $color,
            'account' => $account,
            'vendor' => $contact->vendor,
            'title' => trans('texts.bills'),
            'entityType' => ENTITY_INVOICE,
            'columns' => Utils::trans(['bill_number', 'bill_date', 'bill_total', 'balance_due', 'due_date', 'status']),
            'sortColumn' => 1,
        ];

        return response()->view('public_list', $data);
    }

    public function billDatatable()
    {
        if (!$contact = $this->getContact()) {
            return '';
        }

        return $this->billRepo->getVendorDatatable($contact->id, ENTITY_INVOICE, Input::get('sSearch'));
    }

    public function recurringInvoiceDatatable()
    {
        if (!$contact = $this->getContact()) {
            return '';
        }

        return $this->billRepo->getVendorRecurringDatatable($contact->id);
    }

    public function paymentIndex()
    {
        if (!$contact = $this->getContact()) {
            return $this->returnError();
        }

        $account = $contact->account;

        if (!$account->enable_vendor_portal) {
            return $this->returnError();
        }

        $color = $account->primary_color ? $account->primary_color : '#777';

        $data = [
            'color' => $color,
            'account' => $account,
            'entityType' => ENTITY_PAYMENT,
            'title' => trans('texts.payments'),
            'columns' => Utils::trans(['bill', 'transaction_reference', 'method', 'payment_amount', 'payment_date', 'status']),
            'sortColumn' => 4,
        ];

        return response()->view('public_list', $data);
    }

    public function paymentDatatable()
    {
        if (!$contact = $this->getContact()) {
            return $this->returnError();
        }

        $payments = $this->paymentRepo->findForContact($contact->id, Input::get('sSearch'));

        return Datatable::query($payments)
            ->addColumn('bill_number', function ($model) {
                return $model->invitation_key ? link_to('/view/' . $model->invitation_key, $model->bill_number)->toHtml() : $model->bill_number;
            })
            ->addColumn('transaction_reference', function ($model) {
                return $model->transaction_reference ? e($model->transaction_reference) : '<i>' . trans('texts.manual_entry') . '</i>';
            })
            ->addColumn('payment_type', function ($model) {
                return ($model->payment_type && !$model->last4) ? $model->payment_type : ($model->account_gateway_id ? '<i>Online payment</i>' : '');
            })
            ->addColumn('amount', function ($model) {
                return Utils::formatMoney($model->amount, $model->currency_id, $model->country_id);
            })
            ->addColumn('payment_date', function ($model) {
                return Utils::dateToString($model->payment_date);
            })
            ->addColumn('status', function ($model) {
                return $this->getPaymentStatusLabel($model);
            })
            ->orderColumns('bill_number', 'transaction_reference', 'payment_type', 'amount', 'payment_date')
            ->make();
    }

    private function getPaymentStatusLabel($model)
    {
        $label = trans('texts.status_' . strtolower($model->payment_status_name));
        $class = 'default';
        switch ($model->payment_status_id) {
            case PAYMENT_STATUS_PENDING:
                $class = 'info';
                break;
            case PAYMENT_STATUS_COMPLETED:
                $class = 'success';
                break;
            case PAYMENT_STATUS_FAILED:
                $class = 'danger';
                break;
            case PAYMENT_STATUS_PARTIALLY_REFUNDED:
                $label = trans('texts.status_partially_refunded_amount', [
                    'amount' => Utils::formatMoney($model->refunded, $model->currency_id, $model->country_id),
                ]);
                $class = 'primary';
                break;
            case PAYMENT_STATUS_REFUNDED:
                $class = 'default';
                break;
        }

        return "<h4><div class=\"label label-{$class}\">$label</div></h4>";
    }

    public function quoteIndex()
    {
        if (!$contact = $this->getContact()) {
            return $this->returnError();
        }

        $account = $contact->account;

        if (!$account->enable_vendor_portal) {
            return $this->returnError();
        }

        $color = $account->primary_color ? $account->primary_color : '#777';

        $data = [
            'color' => $color,
            'account' => $account,
            'title' => trans('texts.quotes'),
            'entityType' => ENTITY_QUOTE,
            'columns' => Utils::trans(['quote_number', 'quote_date', 'quote_total', 'due_date', 'status']),
            'sortColumn' => 1,
        ];

        return response()->view('public_list', $data);
    }

    public function quoteDatatable()
    {
        if (!$contact = $this->getContact()) {
            return false;
        }

        return $this->billRepo->getVendorDatatable($contact->id, ENTITY_QUOTE, Input::get('sSearch'));
    }

    public function creditIndex()
    {
        if (!$contact = $this->getContact()) {
            return $this->returnError();
        }

        $account = $contact->account;

        if (!$account->enable_vendor_portal) {
            return $this->returnError();
        }

        $color = $account->primary_color ? $account->primary_color : '#777';

        $data = [
            'color' => $color,
            'account' => $account,
            'title' => trans('texts.credits'),
            'entityType' => ENTITY_CREDIT,
            'columns' => Utils::trans(['credit_date', 'credit_amount', 'credit_balance', 'notes']),
            'sortColumn' => 0,
        ];

        return response()->view('public_list', $data);
    }

    public function creditDatatable()
    {
        if (!$contact = $this->getContact()) {
            return false;
        }

        return $this->creditRepo->getVendorDatatable($contact->vendor_id);
    }

    public function taskIndex()
    {
        if (!$contact = $this->getContact()) {
            return $this->returnError();
        }

        $account = $contact->account;

        if (!$contact->vendor->show_tasks_in_portal) {
            return redirect()->to($account->enable_vendor_portal_dashboard ? '/vendor/dashboard' : '/vendor/payment_methods/');
        }

        if (!$account->enable_vendor_portal) {
            return $this->returnError();
        }

        $color = $account->primary_color ? $account->primary_color : '#777';

        $data = [
            'color' => $color,
            'account' => $account,
            'title' => trans('texts.tasks'),
            'entityType' => ENTITY_TASK,
            'columns' => Utils::trans(['project', 'date', 'duration', 'description']),
            'sortColumn' => 1,
        ];

        return response()->view('public_list', $data);
    }

    public function taskDatatable()
    {
        if (!$contact = $this->getContact()) {
            return false;
        }

        return $this->taskRepo->getVendorDatatable($contact->vendor_id);
    }

    public function documentIndex()
    {
        if (!$contact = $this->getContact()) {
            return $this->returnError();
        }

        $account = $contact->account;

        if (!$account->enable_vendor_portal) {
            return $this->returnError();
        }

        $color = $account->primary_color ? $account->primary_color : '#777';

        $data = [
            'color' => $color,
            'account' => $account,
            'title' => trans('texts.documents'),
            'entityType' => ENTITY_DOCUMENT,
            'columns' => Utils::trans(['bill_number', 'name', 'document_date', 'document_size']),
            'sortColumn' => 2,
        ];

        return response()->view('public_list', $data);
    }

    public function documentDatatable()
    {
        if (!$contact = $this->getContact()) {
            return false;
        }

        return $this->documentRepo->getVendorDatatable($contact->id, ENTITY_DOCUMENT, Input::get('sSearch'));
    }

    private function returnError($error = false)
    {
        if (request()->phantomjs) {
            abort(404);
        }

        return response()->view('error', [
            'error' => $error ?: trans('texts.bill_not_found'),
            'hideHeader' => true,
            'account' => $this->getContact() ? $this->getContact()->account : false,
        ]);
    }

    private function getContact()
    {
        $contactKey = session('contact_key');

        if (!$contactKey) {
            return false;
        }

        $contact = Contact::where('contact_key', '=', $contactKey)->first();

        if (!$contact || $contact->is_deleted) {
            return false;
        }

        return $contact;
    }

    /**
     * @param $publicId
     * @param $name
     * @return \Illuminate\Http\Response
     */
    public function getDocumentVFSJS($publicId, $name)
    {
        if (!$contact = $this->getContact()) {
            return $this->returnError();
        }

        $document = Document::scope($publicId, $contact->account_id)->first();

        if (!$document->isPDFEmbeddable()) {
            return Response::view('error', ['error' => 'Image does not exist!'], 404);
        }

        $authorized = false;
        if ($document->expense && $document->expense->vendor_id == $contact->vendor_id) {
            $authorized = true;
        } elseif ($document->bill && $document->bill->vendor_id == $contact->vendor_id) {
            $authorized = true;
        }

        if (!$authorized) {
            return Response::view('error', ['error' => 'Not authorized'], 403);
        }

        if (substr($name, -3) == '.js') {
            $name = substr($name, 0, -3);
        }

        $content = $document->preview ? $document->getRawPreview() : $document->getRaw();
        $content = 'ninjaAddVFSDoc(' . json_encode(intval($publicId) . '/' . strval($name)) . ',"' . base64_encode($content) . '")';
        $response = Response::make($content, 200);
        $response->header('content-type', 'text/javascript');
        $response->header('cache-control', 'max-age=31536000');

        return $response;
    }

    protected function canCreateZip()
    {
        return function_exists('gmp_init');
    }

    protected function getInvoiceZipDocuments($bill, &$size = 0)
    {
        $documents = $bill->documents;

        foreach ($bill->expenses as $expense) {
            if ($expense->bill_documents) {
                $documents = $documents->merge($expense->documents);
            }
        }

        $documents = $documents->sortBy('size');

        $size = 0;
        $maxSize = MAX_ZIP_DOCUMENTS_SIZE * 1000;
        $toZip = [];
        foreach ($documents as $document) {
            if ($size + $document->size > $maxSize) {
                break;
            }

            if (!empty($toZip[$document->name])) {
                // This name is taken
                if ($toZip[$document->name]->hash != $document->hash) {
                    // 2 different files with the same name
                    $nameInfo = pathinfo($document->name);

                    for ($i = 1; ; $i++) {
                        $name = $nameInfo['filename'] . ' (' . $i . ').' . $nameInfo['extension'];

                        if (empty($toZip[$name])) {
                            $toZip[$name] = $document;
                            $size += $document->size;
                            break;
                        } elseif ($toZip[$name]->hash == $document->hash) {
                            // We're not adding this after all
                            break;
                        }
                    }
                }
            } else {
                $toZip[$document->name] = $document;
                $size += $document->size;
            }
        }

        return $toZip;
    }

    public function getInvoiceDocumentsZip($invitationKey)
    {
        if (!$invitation = $this->billRepo->findInvoiceByInvitation($invitationKey)) {
            return $this->returnError();
        }

        Session::put('contact_key', $invitation->contact->contact_key); // track current contact

        $bill = $invitation->bill;

        $toZip = $this->getInvoiceZipDocuments($bill);

        if (!count($toZip)) {
            return Response::view('error', ['error' => 'No documents small enough'], 404);
        }

        $zip = new ZipArchive($invitation->account->name . ' Invoice ' . $bill->bill_number . '.zip');

        return Response::stream(function () use ($toZip, $zip) {
            foreach ($toZip as $name => $document) {
                $fileStream = $document->getStream();
                if ($fileStream) {
                    $zip->init_file_stream_transfer($name, $document->size, ['time' => $document->created_at->timestamp]);
                    while ($buffer = fread($fileStream, 256000)) {
                        $zip->stream_file_part($buffer);
                    }
                    fclose($fileStream);
                    $zip->complete_file_stream();
                } else {
                    $zip->add_file($name, $document->getRaw());
                }
            }
            $zip->finish();
        }, 200);
    }

    /**
     * @param $invitationKey
     * @param $publicId
     * @return RedirectResponse|\Illuminate\Http\Response|Redirector
     */
    public function getDocument($invitationKey, $publicId)
    {
        if (!$invitation = $this->billRepo->findInvoiceByInvitation($invitationKey)) {
            return $this->returnError();
        }

        Session::put('contact_key', $invitation->contact->contact_key); // track current contact

        $vendorId = $invitation->bill->vendor_id;
        $document = Document::scope($publicId, $invitation->account_id)->firstOrFail();

        $authorized = false;
        if ($document->is_default) {
            $authorized = true;
        } elseif ($document->expense && $document->expense->bill_documents && $document->expense->vendor_id == $invitation->bill->vendor_id) {
            $authorized = true;
        } elseif ($document->bill && $document->bill->vendor_id == $invitation->bill->vendor_id) {
            $authorized = true;
        }

        if (!$authorized) {
            return Response::view('error', ['error' => 'Not authorized'], 403);
        }

        return DocumentController::getDownloadResponse($document);
    }

    public function paymentMethods()
    {
        if (!$contact = $this->getContact()) {
            return $this->returnError();
        }

        $vendor = $contact->vendor;
        $account = $vendor->account;

        $paymentDriver = $account->paymentDriver(false, GATEWAY_TYPE_TOKEN);
        $customer = $paymentDriver->customer($vendor->id);

        $data = [
            'account' => $account,
            'contact' => $contact,
            'color' => $account->primary_color ? $account->primary_color : '#777',
            'vendor' => $vendor,
            'paymentMethods' => $customer ? $customer->payment_methods : false,
            'gateway' => $account->getTokenGateway(),
            'title' => trans('texts.payment_methods'),
            'transactionToken' => $paymentDriver->createTransactionToken(),
        ];

        return response()->view('payments.paymentmethods', $data);
    }

    public function verifyPaymentMethod()
    {
        $publicId = Input::get('source_id');
        $amount1 = Input::get('verification1');
        $amount2 = Input::get('verification2');

        if (!$contact = $this->getContact()) {
            return $this->returnError();
        }

        $vendor = $contact->vendor;
        $account = $vendor->account;

        $paymentDriver = $account->paymentDriver(null, GATEWAY_TYPE_BANK_TRANSFER);
        $result = $paymentDriver->verifyBankAccount($vendor, $publicId, $amount1, $amount2);

        if (is_string($result)) {
            Session::flash('error', $result);
        } else {
            Session::flash('message', trans('texts.payment_method_verified'));
        }

        return redirect()->to($account->enable_vendor_portal_dashboard ? '/vendor/dashboard' : '/vendor/payment_methods/');
    }

    public function removePaymentMethod($publicId)
    {
        if (!$contact = $this->getContact()) {
            return $this->returnError();
        }

        $vendor = $contact->vendor;
        $account = $contact->account;

        $paymentDriver = $account->paymentDriver(false, GATEWAY_TYPE_TOKEN);
        $paymentMethod = PaymentMethod::vendorId($vendor->id)
            ->wherePublicId($publicId)
            ->firstOrFail();

        try {
            $paymentDriver->removePaymentMethod($paymentMethod);
            Session::flash('message', trans('texts.payment_method_removed'));
        } catch (Exception $exception) {
            Session::flash('error', $exception->getMessage());
        }

        return redirect()->to($vendor->account->enable_vendor_portal_dashboard ? '/vendor/dashboard' : '/vendor/payment_methods/');
    }

    public function setDefaultPaymentMethod()
    {
        if (!$contact = $this->getContact()) {
            return $this->returnError();
        }

        $vendor = $contact->vendor;
        $account = $vendor->account;

        $validator = Validator::make(Input::all(), ['source' => 'required']);
        if ($validator->fails()) {
            return Redirect::to($vendor->account->enable_vendor_portal_dashboard ? '/vendor/dashboard' : '/vendor/payment_methods/');
        }

        $paymentDriver = $account->paymentDriver(false, GATEWAY_TYPE_TOKEN);
        $paymentMethod = PaymentMethod::vendorId($vendor->id)
            ->wherePublicId(Input::get('source'))
            ->firstOrFail();

        $customer = $paymentDriver->customer($vendor->id);
        $customer->default_payment_method_id = $paymentMethod->id;
        $customer->save();

        Session::flash('message', trans('texts.payment_method_set_as_default'));

        return redirect()->to($vendor->account->enable_vendor_portal_dashboard ? '/vendor/dashboard' : '/vendor/payment_methods/');
    }

    private function paymentMethodError($type, $error, $accountGateway = false, $exception = false)
    {
        $message = '';
        if ($accountGateway && $accountGateway->gateway) {
            $message = $accountGateway->gateway->name . ': ';
        }
        $message .= $error ?: trans('texts.payment_method_error');

        Session::flash('error', $message);
        Utils::logError("Payment Method Error [{$type}]: " . ($exception ? Utils::getErrorString($exception) : $message), 'PHP', true);
    }

    public function setAutoBill()
    {
        if (!$contact = $this->getContact()) {
            return $this->returnError();
        }

        $vendor = $contact->vendor;

        $validator = Validator::make(Input::all(), ['public_id' => 'required']);

        if ($validator->fails()) {
            return Redirect::to('vendor/bills/recurring');
        }

        $publicId = Input::get('public_id');
        $enable = Input::get('enable');
        $bill = $vendor->bills()->where('public_id', intval($publicId))->first();

        if ($bill && $bill->is_recurring && ($bill->auto_bill == AUTO_BILL_OPT_IN || $bill->auto_bill == AUTO_BILL_OPT_OUT)) {
            $bill->vendor_enable_auto_bill = $enable ? true : false;
            $bill->save();
        }

        return Redirect::to('vendor/bills/recurring');
    }

    public function showDetails()
    {
        if (!$contact = $this->getContact()) {
            return $this->returnError();
        }

        $data = [
            'contact' => $contact,
            'vendor' => $contact->vendor,
            'account' => $contact->account,
        ];

        return view('invited.details', $data);
    }

    public function updateDetails(\Illuminate\Http\Request $request)
    {
        if (!$contact = $this->getContact()) {
            return $this->returnError();
        }

        $vendor = $contact->vendor;
        $account = $contact->account;

        if (!$account->enable_vendor_portal) {
            return $this->returnError();
        }

        $rules = [
            'email' => 'required',
            'address1' => 'required',
            'city' => 'required',
            'state' => $account->requiresAddressState() ? 'required' : '',
            'postal_code' => 'required',
            'country_id' => 'required',
        ];

        if ($vendor->name) {
            $rules['name'] = 'required';
        } else {
            $rules['first_name'] = 'required';
            $rules['last_name'] = 'required';
        }
        if ($account->vat_number || $account->isNinjaAccount()) {
            $rules['vat_number'] = 'required';
        }

        $this->validate($request, $rules);

        $contact->fill(request()->all());
        $contact->save();

        $vendor->fill(request()->all());
        $vendor->save();

        event(new VendorWasUpdatedEvent($vendor));

        return redirect($account->enable_vendor_portal_dashboard ? '/vendor/dashboard' : '/vendor/payment_methods')
            ->withMessage(trans('texts.updated_vendor_details'));
    }

    public function statement()
    {
        if (!$contact = $this->getContact()) {
            return $this->returnError();
        }

        $vendor = $contact->vendor;
        $account = $contact->account;

        if (!$account->enable_vendor_portal || !$account->enable_vendor_portal_dashboard) {
            return $this->returnError();
        }

        $statusId = request()->status_id;

        $startDate = request()->start_date;
        $endDate = request()->end_date;

        if (!$startDate) {
            $startDate = Utils::today(false)->modify('-6 month')->format('Y-m-d');
            $endDate = Utils::today(false)->format('Y-m-d');
        }

        if (request()->json) {
            return dispatch(new GenerateBillStatementData($vendor, request()->all(), $contact));
        }

        $data = [
            'extends' => 'public.header',
            'vendor' => $vendor,
            'account' => $account,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ];

        return view('vendors.statement', $data);

    }
}
