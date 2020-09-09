<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateBillRequest;
use App\Http\Requests\BillRequest;
use App\Http\Requests\UpdateBillRequest;
use App\Jobs\SendBillEmail;
use App\Libraries\Utils;
use App\Models\Activity;
use App\Models\Vendor;
use App\Models\Expense;
use App\Models\Frequency;
use App\Models\Bill;
use App\Models\InvoiceDesign;
use App\Models\BillPayment;
use App\Models\Product;
use App\Models\Warehouse;
use App\Ninja\Datatables\BillDatatable;
use App\Ninja\Repositories\DocumentRepository;
use App\Ninja\Repositories\BillRepository;
use App\Ninja\Repositories\VendorRepository;
use App\Services\BillPaymentService;
use App\Services\BillService;
use App\Services\RecurringBillService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Log;
use Redirect;
use Illuminate\Support\Facades\Request;

class BillController extends BaseController
{
    protected $billRepo;
    protected $vendorRepo;
    protected $documentRepo;
    protected $billService;
    protected $paymentService;
    protected $recurringBillService;
    protected $entityType = ENTITY_BILL;

    /**
     * BillController constructor.
     * @param BillRepository $billRepo
     * @param VendorRepository $vendorRepo
     * @param BillService $billService
     * @param DocumentRepository $documentRepo
     * @param RecurringBillService $recurringBillService
     * @param BillPaymentService $paymentService
     */
    public function __construct(
        BillRepository $billRepo, VendorRepository $vendorRepo, BillService $billService,
        DocumentRepository $documentRepo, RecurringBillService $recurringBillService,
        BillPaymentService $paymentService)
    {
        // parent::__construct();
        $this->billRepo = $billRepo;
        $this->vendorRepo = $vendorRepo;
        $this->billService = $billService;
        $this->recurringBillService = $recurringBillService;
        $this->paymentService = $paymentService;
    }

    public function index()
    {
        $this->authorize('view', ENTITY_BILL);
        $data = [
            'title' => trans('texts.bills'),
            'entityType' => ENTITY_BILL,
            'datatable' => new BillDatatable(),
            'statuses' => Bill::getStatuses(),
        ];

        return response()->view('list_wrapper', $data);
    }

    public function show($publicId)
    {
        Session::reflash();

        return Redirect::to("bills/{$publicId}/edit");
    }

    public function getDatatable($vendorPublicId = null)
    {
        $accountId = Auth::user()->account_id;
        $search = Input::get('sSearch');

        return $this->billService->getDatatable($accountId, $vendorPublicId, ENTITY_BILL, $search);
    }

    public function getRecurringDatatable($vendorPublicId = null)
    {
        $accountId = Auth::user()->account_id;
        $search = Input::get('sSearch');

        return $this->recurringBillService
            ->getDatatable($accountId, $vendorPublicId, ENTITY_RECURRING_BILL, $search);
    }

    public function getBillReceivedDatatable($vendorPublicId = null)
    {
        $accountId = Auth::user()->account_id;
        $search = Input::get('sSearch');

        return $this->recurringBillService
            ->getReceivedDatatable($accountId, $vendorPublicId, ENTITY_BILL_RECEIVE, $search);
    }

    public function create(BillRequest $request, $vendorPublicId = 0, $isRecurring = false)
    {
        $this->authorize('create', ENTITY_BILL);
        $account = Auth::user()->account;

        $entityType = $isRecurring ? ENTITY_RECURRING_BILL : ENTITY_BILL;
        $vendorId = null;

        if ($request->vendor_id) {
            $vendorId = Vendor::getPrivateId($request->vendor_id);
        }

        $bill = $account->createBill($entityType, $vendorId);

        $bill->public_id = 0;
        $bill->loadFromRequest();

        $vendors = Vendor::scope()->with('contacts', 'country')->orderBy('name');
        if ($request->warehouse_id != 0) {
            $warehouse = Warehouse::scope($request->warehouse_id)->firstOrFail();
        } else {
            $warehouse = null;
        }
        if (!Utils::hasPermission('view_vendor')) {
            $vendors = $vendors->where('vendors.user_id', Auth::user()->id);
        }

        $data = [
            'clients' => $vendors->get(),
            'vendors' => $vendors->get(),
            'entityType' => $bill->getEntityType(),
            'invoice' => $bill,
            'warehouses' => $warehouse,
            'method' => 'POST',
            'url' => 'bills',
            'title' => trans('texts.new_bill'),
//            'warehousePublicId' => Input::old('warehouse') ? Input::old('warehouse') : $request->warehouse_id,
        ];

        $data = array_merge($data, self::getViewModel($bill));

        return View::make('bills.edit', $data);
    }

    public function store(CreateBillRequest $request)
    {

        $data = $request->input();
        $data['documents'] = $request->file('documents');

        $action = Input::get('action');

        $entityType = Input::get('entityType');

        $bill = $this->billService->save($data);

        $entityType = $bill->getEntityType();

        $message = trans("texts.created_{$entityType}");

        $input = $request->input();
        $vendorPublicId = isset($input['client']['public_id']) ? $input['client']['public_id'] : false;
        if ($vendorPublicId == '-1') {
            $message = $message . ' ' . trans('texts.and_created_vendor');
        }

        Session::flash('message', $message);

        if ($action == 'email') {
            $this->emailBill($bill);
        }

        $url = $bill->getRoute();

        return url($url);
    }

    public function edit(BillRequest $request, $publicId, $clone = false, $isReceived = false)
    {
        $this->authorize('edit', ENTITY_BILL);
        $account = Auth::user()->account;
        /**
         * TODO: bill payment documents and expense should be revised
         */
        $bill = $request->entity()->load('bill_invitations', 'account.country', 'client.contacts', 'vendor.country', 'invoice_items', 'documents', 'expenses', 'expenses.documents', 'bill_payments');

        $entityType = $bill->getEntityType();

        $contactIds = DB::table('bill_invitations')
            ->leftJoin('vendor_contacts', 'vendor_contacts.id', 'bill_invitations.contact_id')
            ->where('bill_invitations.bill_id', $bill->id)
            ->where('bill_invitations.account_id', Auth::user()->account_id)
            ->select('vendor_contacts.public_id')->pluck('public_id')
            ->where('bill_invitations.deleted_at', null);

        $vendors = Vendor::scope()->withTrashed()->with('contacts', 'country');

        if ($clone) {
            $entityType = $clone == BILL_TYPE_STANDARD ? ENTITY_BILL : ENTITY_BILL_QUOTE;
            $bill->id = $bill->public_id = null;
            $bill->is_public = false;
            $bill->is_recurring = $bill->is_recurring && $clone == BILL_TYPE_STANDARD;
            $bill->bill_type_id = $clone;
            $bill->invoice_number = $account->getNextBillNumber($bill);
            $bill->due_date = null;
            $bill->partial_due_date = null;
            $bill->balance = $bill->amount;
            $bill->bill_status_id = 0;
            $bill->bill_date = date_create()->format('Y-m-d');
            $bill->deleted_at = null;
            while ($bill->documents->count()) {
                $bill->documents->pop();
            }
            while ($bill->expenses->count()) {
                $bill->expenses->pop();
            }
            $method = 'POST';
            $url = "{$entityType}s";
        } else {
            $method = 'PUT';
            $url = "{$entityType}s/{$bill->public_id}";
            $vendors->where('id', $bill->vendor_id);
        }

        $bill->bill_date = Utils::fromSqlDate($bill->bill_date);
        $bill->recurring_due_date = $bill->due_date; // Keep in SQL form
        $bill->due_date = Utils::fromSqlDate($bill->due_date);
        $bill->start_date = Utils::fromSqlDate($bill->start_date);
        $bill->end_date = Utils::fromSqlDate($bill->end_date);
        $bill->last_sent_date = Utils::fromSqlDate($bill->last_sent_date);
        $bill->partial_due_date = Utils::fromSqlDate($bill->partial_due_date);

        $bill->features = [
            'customize_invoice_design' => Auth::user()->hasFeature(FEATURE_CUSTOMIZE_INVOICE_DESIGN),
            'remove_created_by' => Auth::user()->hasFeature(FEATURE_REMOVE_CREATED_BY),
            'invoice_settings' => Auth::user()->hasFeature(FEATURE_INVOICE_SETTINGS),
        ];

        $lastSent = ($bill->is_recurring && $bill->last_sent_date) ? $bill->recurring_bills->last() : null;

        if (!Auth::user()->hasPermission('view_vendor')) {
            $vendors = $vendors->where('vendors.user_id', Auth::user()->id);
        }

        $data = [
            'clients' => $vendors->get(),
            'warehouse' => null,
            'entityType' => $entityType,
            'showBreadcrumbs' => $clone,
            'invoice' => $bill,
            'method' => $method,
            'invitationContactIds' => $contactIds,
            'url' => $url,
            'title' => trans("texts.edit_{$entityType}"),
            'vendor' => $bill->vendor,
            'isRecurring' => $bill->is_recurring,
            'lastSent' => $lastSent,
//            'warehousePublicId' => $bill->warehouse ? $bill->warehouse->public_id : null,
        ];

        $data = array_merge($data, self::getViewModel($bill));

        if ($bill->isSent() && $bill->getAutoBillEnabled() && !$bill->isPaid()) {
            $data['autoBillChangeWarning'] = $bill->vendor->autoBillLater();
        }

        if ($clone) {
            $data['formIsChanged'] = true;
        }

        // Set the invitation data on the vendor's contacts
        if (!$clone) {
            $vendors = $data['clients'];
            foreach ($vendors as $vendor) {

                if ($vendor->id != $bill->vendor->id) {
                    continue;
                }

                foreach ($bill->bill_invitations as $billInvitation) {
                    foreach ($vendor->contacts as $contact) {
                        if ($billInvitation->contact_id == $contact->id) {
                            $hasPassword = $account->isVendorPortalPasswordEnabled() && $contact->password;
                            $contact->email_error = $billInvitation->email_error;
                            $contact->invitation_link = $billInvitation->getLink('view', $hasPassword, $hasPassword);
                            $contact->invitation_viewed = $billInvitation->viewed_date && $billInvitation->viewed_date != '0000-00-00 00:00:00' ? $billInvitation->viewed_date : false;
                            $contact->invitation_opened = $billInvitation->opened_date && $billInvitation->opened_date != '0000-00-00 00:00:00' ? $billInvitation->opened_date : false;
                            $contact->invitation_status = $contact->email_error ? false : $billInvitation->getStatus();
                            $contact->invitation_signature_svg = $billInvitation->signatureDiv();
                        }
                    }
                }

                break;
            }
        }

        if (Auth::user()->registered && !Auth::user()->confirmed) {
            session()->flash('warning', trans('texts.confirmation_required',
                ['link' => link_to('/resend_confirmation', trans('texts.click_here'))
                ]));
        }

        return View::make('bills.edit', $data);
    }

    public function update(UpdateBillRequest $request)
    {

        $data = $request->input();
        $data['documents'] = $request->file('documents');

        $action = Input::get('action');
        $entityType = Input::get('entityType');

        $bill = $this->billService->save($data, $request->entity());

        $entityType = $bill->getEntityType();
        $message = trans("texts.updated_{$entityType}");
        Session::flash('message', $message);

        if ($action == 'clone_bill') {
            return url(sprintf('bills/%s/clone', $bill->public_id));
        } else if ($action == 'clone_bill_quote') {
            return url(sprintf('bill_quotes/%s/clone', $bill->public_id));
        } elseif ($action == 'convert') {
            return $this->convertQuote($request);
        } elseif ($action == 'email') {
            $this->emailBill($bill);
        }

        return url($bill->getRoute());
    }

    public function createRecurring(BillRequest $request, $vendorPublicId = 0)
    {
        return self::create($request, $vendorPublicId, true);
    }

    public function createReceived(BillRequest $request, $publicId = 0)
    {
        return self::edit($request, $publicId, false, true);
    }

    private static function getViewModel($bill)
    {
        $account = Auth::user()->account;
        $recurringHelp = '';
        $recurringDueDateHelp = '';
        $recurringDueDates = [];

        foreach (preg_split("/((\r?\n)|(\r\n?))/", trans('texts.recurring_help')) as $line) {
            $parts = explode('=>', $line);
            if (count($parts) > 1) {
                $line = $parts[0] . ' => ' . Utils::processVariables($parts[0]);
                $recurringHelp .= '<li>' . strip_tags($line) . '</li>';
            } else {
                $recurringHelp .= $line;
            }
        }

        foreach (preg_split("/((\r?\n)|(\r\n?))/", trans('texts.recurring_due_date_help')) as $line) {
            $parts = explode('=>', $line);
            if (count($parts) > 1) {
                $line = $parts[0] . ' => ' . Utils::processVariables($parts[0]);
                $recurringDueDateHelp .= '<li>' . strip_tags($line) . '</li>';
            } else {
                $recurringDueDateHelp .= $line;
            }
        }

        // Create due date options
        $recurringDueDates = [
            trans('texts.use_vendor_terms') => ['value' => '', 'class' => 'monthly weekly'],
        ];

        $ends = ['th', 'st', 'nd', 'rd', 'th', 'th', 'th', 'th', 'th', 'th'];
        for ($i = 1; $i < 31; $i++) {
            if ($i >= 11 && $i <= 13) {
                $ordinal = $i . 'th';
            } else {
                $ordinal = $i . $ends[$i % 10];
            }

            $dayStr = str_pad($i, 2, '0', STR_PAD_LEFT);
            $str = trans('texts.day_of_month', ['ordinal' => $ordinal]);

            $recurringDueDates[$str] = ['value' => "1998-01-$dayStr", 'data-num' => $i, 'class' => 'monthly'];
        }
        $recurringDueDates[trans('texts.last_day_of_month')] = ['value' => '1998-01-31', 'data-num' => 31, 'class' => 'monthly'];

        $daysOfWeek = [
            trans('texts.sunday'),
            trans('texts.monday'),
            trans('texts.tuesday'),
            trans('texts.wednesday'),
            trans('texts.thursday'),
            trans('texts.friday'),
            trans('texts.saturday'),
        ];
        foreach (['1st', '2nd', '3rd', '4th'] as $i => $ordinal) {
            foreach ($daysOfWeek as $j => $dayOfWeek) {
                $str = trans('texts.day_of_week_after', ['ordinal' => $ordinal, 'day' => $dayOfWeek]);

                $day = $i * 7 + $j + 1;
                $dayStr = str_pad($day, 2, '0', STR_PAD_LEFT);
                $recurringDueDates[$str] = ['value' => "1998-02-$dayStr", 'data-num' => $day, 'class' => 'weekly'];
            }
        }

        // Check for any taxes which have been deleted
        $taxRateOptions = $account->present()->taxRateOptions;
        if ($bill->exists) {
            foreach ($bill->getTaxes() as $key => $rate) {
                $key = '0 ' . $key; // mark it as a standard exclusive rate option
                if (isset($taxRateOptions[$key])) {
                    continue;
                }
                $taxRateOptions[$key] = $rate['name'] . ' ' . $rate['rate'] . '%';
            }
        }

        return [
            'data' => Input::old('data'),
            'account' => Auth::user()->account->load('country'),
            'products' => Product::stock(),
            'clients' => Vendor::scope()->with('contacts', 'country')->orderBy('name')->get(),
            'taxRateOptions' => $taxRateOptions,
            'sizes' => Cache::get('sizes'),
            'invoiceDesigns' => InvoiceDesign::getDesigns(),
            'invoiceFonts' => Cache::get('fonts'),
            'frequencies' => Frequency::selectOptions(),
            'recurringDueDates' => $recurringDueDates,
            'recurringHelp' => $recurringHelp,
            'recurringDueDateHelp' => $recurringDueDateHelp,
            'invoiceLabels' => Auth::user()->account->getInvoiceLabels(),
            'tasks' => Session::get('tasks') ? Session::get('tasks') : null,
            'expenseCurrencyId' => Session::get('expenseCurrencyId') ?: null,
            'expenses' => Expense::scope(Session::get('expenses'))->with('documents', 'expense_category')->get(),
//            'warehouses' => Warehouse::scope()->withActiveOrSelected(isset($bill) ? $bill->warehouse_id : false)->orderBy('name')->get(),
        ];
    }

    private function emailBill($bill)
    {
        $reminder = Input::get('reminder');
        $template = Input::get('template');
        $pdfUpload = Utils::decodePDF(Input::get('pdfupload'));
        $entityType = $bill->getEntityType();

        if (filter_var(Input::get('save_as_default'), FILTER_VALIDATE_BOOLEAN)) {
            $account = Auth::user()->account;
            $account->setTemplateDefaults(Input::get('template_type'),
                $template['subject'], $template['body']);
        }

        if (!Auth::user()->confirmed) {
            if (Auth::user()->registered) {
                $errorMessage = trans('texts.confirmation_required', ['link' => link_to('/resend_confirmation', trans('texts.click_here'))]);
            } else {
                $errorMessage = trans('texts.registration_required');
            }

            Session::flash('error', $errorMessage);

            return Redirect::to('bills/' . $bill->public_id . '/edit');
        }

        if ($bill->is_recurring) {
            $response = $this->emailRecurringInvoice($bill);
        } else {
            $userId = Auth::user()->id;
            $this->dispatch(new SendBillEmail($bill, $userId, $reminder, $template));
            $response = true;
        }

        if ($response === true) {
            $message = trans("texts.emailed_{$entityType}");
            Session::flash('message', $message);
        } else {
            Session::flash('error', $response);
        }
    }

    private function emailRecurringInvoice(&$bill)
    {
        if (!$bill->shouldSendToday()) {
            if ($date = $bill->getNextSendDate()) {
                $date = $bill->account->formatDate($date);
                $date .= ' ' . DEFAULT_SEND_RECURRING_HOUR . ':00 am ' . $bill->account->getTimezone();

                return trans('texts.recurring_too_soon', ['date' => $date]);
            } else {
                return trans('texts.no_longer_running');
            }
        }

        // switch from the recurring invoice to the generated invoice
        $bill = $this->billRepo->createRecurringBill($bill);

        // in case auto-bill is enabled then a receipt has been sent
        if ($bill->isPaid()) {

            return true;
        } else {
            $userId = Auth::user()->id;
            $this->dispatch(new SendBillEmail($bill, $userId));

            return true;
        }
    }

    public function bulk($entityType = ENTITY_BILL)
    {
        $action = Input::get('bulk_action') ?: Input::get('action');
        $ids = Input::get('bulk_public_id') ?: (Input::get('public_id') ?: Input::get('ids'));

        $count = $this->billService->bulk($ids, $action);

        if ($count > 0) {
            if ($action == 'markSent') {
                $key = 'marked_sent_bill';
            } elseif ($action == 'emailBill') {
                $key = 'emailed_' . $entityType;
            } elseif ($action == 'markPaid') {
                $key = 'created_payment';
            } elseif ($action == 'download') {
                $key = 'downloaded_bill';
            } else {
                $key = "{$action}d_{$entityType}";
            }
            $message = Utils::pluralize($key, $count);
            Session::flash('message', $message);
        }

        if (strpos(Request::server('HTTP_REFERER'), 'recurring_bills')) {
            $entityType = ENTITY_RECURRING_BILL;
        }

        return $this->returnBulk($entityType, $action, $ids);
    }

    public function convertQuote(UpdateBillRequest $request)
    {
        $clone = $this->billService->convertQuote($request->entity());

        Session::flash('message', trans('texts.converted_to_bill'));

        return url('bills/' . $clone->public_id);
    }

    public function cloneBill(BillRequest $request, $publicId)
    {
        return self::edit($request, $publicId, BILL_TYPE_STANDARD);
    }

    public function cloneQuote(BillRequest $request, $publicId)
    {
        return self::edit($request, $publicId, BILL_TYPE_QUOTE);
    }

    public function billHistory(BillRequest $request, $publicId)
    {
        $bill = $request->entity();

        $paymentId = $request->payment_id ? BillPayment::getPrivateId($request->payment_id) : false;

        $bill->load('user', 'invoice_items', 'documents', 'account.country', 'client.contacts', 'vendor.country');
        $bill->bill_date = Utils::fromSqlDate($bill->bill_date);
        $bill->due_date = Utils::fromSqlDate($bill->due_date);
        $bill->features = [
            'customize_invoice_design' => Auth::user()->hasFeature(FEATURE_CUSTOMIZE_INVOICE_DESIGN),
            'remove_created_by' => Auth::user()->hasFeature(FEATURE_REMOVE_CREATED_BY),
            'invoice_settings' => Auth::user()->hasFeature(FEATURE_INVOICE_SETTINGS),
        ];
        $bill->invoice_type_id = intval($bill->bill_type_id);

        $activities = Activity::scope(false, $bill->account_id);
        if ($paymentId) {
            $activities->whereIn('activity_type_id', [ACTIVITY_TYPE_CREATE_BILL_PAYMENT])
                ->where('payment_id', $paymentId);
        } else {
            $activities->whereIn('activity_type_id', [ACTIVITY_TYPE_UPDATE_BILL, ACTIVITY_TYPE_UPDATE_BILL_QUOTE])
                ->where('bill_id', $bill->id);
        }
        $activities = $activities->orderBy('id', 'desc')
            ->get(['id', 'created_at', 'user_id', 'json_backup', 'activity_type_id', 'payment_id']);

        $versionsJson = [];
        $versionsSelect = [];
        $lastId = false;

        foreach ($activities as $activity) {
            if ($backup = json_decode($activity->json_backup)) {
                $backup->invoice_date = Utils::fromSqlDate($backup->bill_date);
                $backup->due_date = Utils::fromSqlDate($backup->due_date);
                $backup->features = [
                    'customize_invoice_design' => Auth::user()->hasFeature(FEATURE_CUSTOMIZE_INVOICE_DESIGN),
                    'remove_created_by' => Auth::user()->hasFeature(FEATURE_REMOVE_CREATED_BY),
                    'invoice_settings' => Auth::user()->hasFeature(FEATURE_INVOICE_SETTINGS),
                ];
                $backup->invoice_type_id = isset($backup->bill_type_id) && intval($backup->bill_type_id) == INVOICE_TYPE_QUOTE;
                $backup->account = $bill->account->toArray();

                $versionsJson[$paymentId ? 0 : $activity->id] = $backup;
                $key = Utils::timestampToDateTimeString(strtotime($activity->created_at)) . ' - ' . $activity->user->getDisplayName();
                $versionsSelect[$lastId ?: 0] = $key;
                $lastId = $activity->id;
            } else {
                Utils::logError('Failed to parse invoice backup');
            }
        }

        // Show the current version as the last in the history
        if (!$paymentId) {
            $versionsSelect[$lastId] = Utils::timestampToDateTimeString(strtotime($bill->created_at)) . ' - ' . $bill->user->getDisplayName();
        }

        $data = [
            'invoice' => $bill,
            'versionsJson' => json_encode($versionsJson),
            'versionsSelect' => $versionsSelect,
            'invoiceDesigns' => InvoiceDesign::getDesigns(),
            'invoiceFonts' => Cache::get('fonts'),
            'paymentId' => $paymentId,
        ];

        return View::make('bills.history', $data);
    }

    public function receiveNote(BillRequest $request)
    {
        $bill = $request->entity();
        $bill->load('user', 'invoice_items', 'documents', 'expenses', 'expenses.documents', 'account.country', 'client.contacts', 'vendor.country', 'vendor.shipping_country');
        $bill->invoice_date = Utils::fromSqlDate($bill->bill_date);
        $bill->invoice_date = Utils::fromSqlDate($bill->due_date);
        $bill->features = [
            'customize_invoice_design' => Auth::user()->hasFeature(FEATURE_CUSTOMIZE_INVOICE_DESIGN),
            'remove_created_by' => Auth::user()->hasFeature(FEATURE_REMOVE_CREATED_BY),
            'invoice_settings' => Auth::user()->hasFeature(FEATURE_INVOICE_SETTINGS),
        ];
        $bill->invoice_type_id = intval($bill->bill_type_id);

        if ($bill->client->shipping_address1) {
            foreach (['address1', 'address2', 'city', 'state', 'postal_code', 'country_id'] as $field) {
                $bill->client->$field = $bill->client->{'shipping_' . $field};
            }
        }

        $data = [
            'invoice' => $bill,
            'invoiceDesigns' => InvoiceDesign::getDesigns(),
            'invoiceFonts' => Cache::get('fonts'),
        ];

        return View::make('bills.receive_note', $data);
    }

    public function checkBillNumber($billPublicId = false)
    {
        $billNumber = request()->invoice_number;

        $query = Bill::scope()->where('invoice_number', $billNumber)->withTrashed();

        if ($billPublicId) {
            $query->where('public_id', $billPublicId);
        }

        $count = $query->count();

        return $count ? RESULT_FAILURE : RESULT_SUCCESS;
    }
}
