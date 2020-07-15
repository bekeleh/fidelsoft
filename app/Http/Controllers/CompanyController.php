<?php

namespace App\Http\Controllers;

use App\Http\Requests\CompanyRequest;
use App\Http\Requests\CreateCompanyRequest;
use App\Http\Requests\UpdateCompanyRequest;
use App\Libraries\Utils;
use App\Models\Company;
use App\Ninja\Datatables\CompanyDatatable;
use App\Ninja\Repositories\CompanyRepository;
use App\Services\CompanyService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Redirect;

/**
 * Class CompanyController.
 */
class CompanyController extends BaseController
{

    protected $companyService;

    protected $companyRepo;

    /**
     * CompanyController constructor.
     *
     * @param CompanyService $companyService
     * @param CompanyRepository $companyRepo
     */
    public function __construct(CompanyService $companyService, CompanyRepository $companyRepo)
    {
        //parent::__construct();
        $this->companyService = $companyService;
        $this->companyRepo = $companyRepo;
    }

    public function index()
    {
        $this->authorize('view', ENTITY_COMPANY);
        return View::make('list_wrapper', [
            'entityType' => ENTITY_COMPANY,
            'datatable' => new CompanyDatatable(),
            'title' => trans('texts.companies'),
        ]);
    }

    public function show($publicId)
    {
        Session::reflash();

        return Redirect::to("companies/$publicId/edit");
    }

    public function getDatatable()
    {
        $accountId = Auth::user()->account_id;
        $search = Input::get('sSearch');

        return $this->companyService->getDatatable($accountId, $search);
    }

    public function create(CompanyRequest $request)
    {
        $this->authorize('create', ENTITY_COMPANY);

        $data = [
            'company' => null,
            'method' => 'POST',
            'url' => 'companies',
            'title' => trans('texts.create_company'),
        ];

        $data = array_merge($data, self::getViewModel());

        return View::make('companies.edit', $data);
    }

    public function store(CreateCompanyRequest $request)
    {
        $data = $request->input();

        $company = $this->companyService->save($data);

        return redirect()->to("companies/{$company->public_id}/edit")->with('success', trans('texts.created_company'));
    }

    public function edit(CompanyRequest $request, $publicId, $clone = false)
    {
        $this->authorize('edit', ENTITY_COMPANY);
        $company = Company::scope($publicId)->withTrashed()->firstOrFail();

        if ($clone) {
            $company->id = null;
            $company->public_id = null;
            $company->deleted_at = null;
            $method = 'POST';
            $url = 'companies';
        } else {
            $method = 'PUT';
            $url = 'companies/' . $company->public_id;
        }

        $data = [
            'company' => $company,
            'entity' => $company,
            'method' => $method,
            'url' => $url,
            'title' => trans('texts.edit_company'),
        ];

        $data = array_merge($data, self::getViewModel($company));

        return View::make('companies.edit', $data);
    }

    public function update(UpdateCompanyRequest $request, $publicId)
    {
        $data = $request->input();

        $company = $this->companyService->save($data, $request->entity());

        $action = Input::get('action');
        if (in_array($action, ['archive', 'delete', 'restore', 'invoice', 'add_to_invoice'])) {
            return self::bulk();
        }

        if ($action == 'clone') {
            return redirect()->to(sprintf('companies/%s/clone', $company->public_id))->with('success', trans('texts.clone_company'));
        } else {
            return redirect()->to("companies/{$company->public_id}/edit")->with('success', trans('texts.updated_company'));
        }
    }

    public function bulk()
    {
        $action = Input::get('action');
        $ids = Input::get('public_id') ? Input::get('public_id') : Input::get('ids');

        if ($action == 'invoice') {
            $companies = Company::scope($ids)->get();
            $data = [];
            foreach ($companies as $company) {
                $data[] = $company->company_key;
            }

            return redirect("invoices/create")->with('companies', $data);
        } else {
            $count = $this->companyService->bulk($ids, $action);
        }

        $message = Utils::pluralize($action . 'd_company', $count);

        return $this->returnBulk(ENTITY_COMPANY, $action, $ids)->with('success', $message);
    }

    public function cloneCompany(CompanyRequest $request, $publicId)
    {
        return self::edit($request, $publicId, true);
    }

    private static function getViewModel($company = false)
    {
        return [
            'data' => Input::old('data'),
            'account' => Auth::user()->account,
        ];
    }
}
