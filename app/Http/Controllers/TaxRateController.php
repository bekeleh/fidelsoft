<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateTaxRateRequest;
use App\Http\Requests\TaxRateRequest;
use App\Http\Requests\UpdateTaxRateRequest;
use App\Ninja\Datatables\TaxRateDatatable;
use App\Ninja\Repositories\TaxRateRepository;
use App\Services\TaxRateService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Redirect;

class TaxRateController extends BaseController
{
    protected $taxRateService;
    protected $taxRateRepo;

    public function __construct(TaxRateRepository $taxRateRepo, TaxRateService $taxRateService)
    {
        //parent::__construct();

        $this->taxRateService = $taxRateService;
        $this->taxRateRepo = $taxRateRepo;
    }

    public function index()
    {
        $this->authorize('view', ENTITY_TAX_RATE);
        return View::make('list_wrapper', [
            'entityType' => ENTITY_TAX_RATE,
            'datatable' => new TaxRateDatatable(),
            'title' => trans('texts.tax_rates'),
        ]);
    }

    public function getDatatable()
    {
        $account = Auth::user()->account_id;
        $search = Input::get('sSearch');
        return $this->taxRateService->getDatatable($account, $search);
    }

    public function create(TaxRateRequest $request)
    {
        $this->authorize('create', ENTITY_TAX_RATE);
        $data = [
            'taxRate' => null,
            'method' => 'POST',
            'url' => 'tax_rates',
            'title' => trans('texts.create_tax_rate'),
        ];

        return View::make('tax_rates.edit', $data);
    }

    public function store(CreateTaxRateRequest $request)
    {
        $data = $request->input();

        $taxRate = $this->taxRateService->save($data);

        return redirect()->to("tax_rates/{$taxRate->public_id}/edit")->with('message', trans('texts.created_tax_rate'));
    }

    public function edit(TaxRateRequest $request, $publicId = false, $clone = false)
    {
        $this->authorize('edit', ENTITY_TAX_RATE);
        $taxRate = $request->entity();
        if ($clone) {
            $taxRate->id = null;
            $taxRate->public_id = null;
            $taxRate->deleted_at = null;
            $method = 'POST';
            $url = 'tax_rates';
        } else {
            $method = 'PUT';
            $url = 'tax_rates/' . $taxRate->public_id;
        }

        $data = [
            'taxRate' => $taxRate,
            'entity' => $taxRate,
            'method' => $method,
            'url' => $url,
            'title' => trans('texts.edit_tax_rate'),
        ];

        $data = array_merge($data, self::getViewModel($taxRate));

        return View::make('tax_rates.edit', $data);
    }

    public function update(UpdateTaxRateRequest $request)
    {
        $data = $request->input();

        $taxRate = $this->taxRateService->save($data, $request->entity());

        $action = Input::get('action');
        if (in_array($action, ['archive', 'delete', 'restore', 'invoice', 'add_to_invoice'])) {
            return self::bulk();
        }

        if ($action == 'clone') {
            return redirect()->to(sprintf('tax_rates/%s/clone', $taxRate->public_id))->with('message', trans('texts.clone_tax_rate'));
        } else {
            return redirect()->to("tax_rates/{$taxRate->public_id}/edit")->with('message', trans('texts.updated_tax_rate'));
        }
    }

    public function show($publicId)
    {
        Session::reflash();

        return Redirect::to("tax_rates/{$publicId}/edit");
    }

    public function bulk()
    {
        $action = Input::get('bulk_action');
        $ids = Input::get('bulk_public_id');
        $count = $this->taxRateService->bulk($ids, $action);

        Session::flash('message', trans('texts.archived_tax_rate'));

        return Redirect::to('tax_rates/' . ACCOUNT_TAX_RATES);
    }

    public function cloneTaxRate(TaxRateRequest $request, $publicId)
    {
        return self::edit($request, $publicId, true);
    }

    private static function getViewModel($taxRate = false)
    {
        return [
            'data' => Input::old('data'),
            'account' => Auth::user()->account,
        ];
    }
}
