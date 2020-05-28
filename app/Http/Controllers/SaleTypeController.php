<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateSaleTypeRequest;
use App\Http\Requests\SaleTypeRequest;
use App\Http\Requests\UpdateSaleTypeRequest;
use App\Libraries\Utils;
use App\Ninja\Datatables\SaleTypeDatatable;
use App\Ninja\Repositories\SaleTypeRepository;
use App\Services\SaleTypeService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Redirect;

class SaleTypeController extends BaseController
{
    // Stores
    protected $saleTypeRepo;
    protected $saleTypeService;
    protected $entityType = ENTITY_SALE_TYPE;

    public function __construct(SaleTypeRepository $saleTypeRepo, SaleTypeService $saleTypeService)
    {
        // parent::__construct();

        $this->saleTypeRepo = $saleTypeRepo;
        $this->saleTypeService = $saleTypeService;
    }

    public function index()
    {
        return View::make('list_wrapper', [
            'entityType' => ENTITY_SALE_TYPE,
            'datatable' => new SaleTypeDatatable(),
            'title' => trans('texts.sale_types'),
        ]);
    }

    public function show($publicId)
    {
        Session::reflash();

        return Redirect::to("sale_types/{$publicId}/edit");
    }

    public function getDatatable($saleTypePublicId = null)
    {
        return $this->saleTypeService->getDatatable(Auth::user()->account_id, Input::get('sSearch'));
    }

    public function create(SaleTypeRequest $request)
    {
        $data = [
            'saleType' => null,
            'method' => 'POST',
            'url' => 'sale_types',
            'title' => trans('texts.new_store'),
        ];

        $data = array_merge($data, self::getViewModel());
        return View::make('sale_types.edit', $data);
    }

    public function cloneSaleType(SaleTypeRequest $request, $publicId)
    {
        return self::edit($request, $publicId, true);
    }

    public function edit(SaleTypeRequest $request, $publicId = false, $clone = false)
    {
        $saleType = $request->entity();
        if ($clone) {
            $saleType->id = null;
            $saleType->public_id = null;
            $saleType->deleted_at = null;
            $method = 'POST';
            $url = 'sale_types';
        } else {
            $method = 'PUT';
            $url = 'sale_types/' . $saleType->public_id;
        }

        $data = [
            'saleType' => $saleType,
            'entity' => $saleType,
            'method' => $method,
            'url' => $url,
            'title' => trans('texts.edit_sale_type'),
        ];

        $data = array_merge($data, self::getViewModel($saleType));

        return View::make('sale_types.edit', $data);
    }

    public function store(CreateSaleTypeRequest $request)
    {
        $data = $request->input();

        $saleType = $this->saleTypeService->save($data);

        return redirect()->to("sale_types/{$saleType->public_id}/edit")->with('success', trans('texts.created_sale_type'));
    }

    public function update(UpdateSaleTypeRequest $request)
    {
        $data = $request->input();

        $saleType = $this->saleTypeService->save($data, $request->entity());

        $action = Input::get('action');
        if (in_array($action, ['archive', 'delete', 'restore', 'invoice', 'add_to_invoice'])) {
            return self::bulk();
        }

        if ($action == 'clone') {
            return redirect()->to(sprintf('sale_types/%s/clone', $saleType->public_id))->with('success', trans('texts.clone_sale_type'));
        } else {
            return redirect()->to("sale_types/{$saleType->public_id}/edit")->with('success', trans('texts.updated_sale_type'));
        }
    }

    public function bulk()
    {
        $action = Input::get('action');
        $ids = Input::get('public_id') ? Input::get('public_id') : Input::get('ids');

        $count = $this->saleTypeService->bulk($ids, $action);

        $message = Utils::pluralize($action . 'd_sale_type', $count);

        return $this->returnBulk(ENTITY_SALE_TYPE, $action, $ids)->with('message', $message);
    }

    private static function getViewModel($saleType = false)
    {
        return [
            'data' => Input::old('data'),
            'account' => Auth::user()->account,
        ];
    }

}
