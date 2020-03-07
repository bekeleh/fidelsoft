<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaleTypeRequest;
use App\Models\SaleType;
use App\Ninja\Repositories\SaleTypeRepository;
use App\Services\SaleTypeService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Redirect;

class SaleTypeController extends BaseController
{
    protected $saleTypeService;
    protected $saleTypeRepo;

    public function __construct(SaleTypeService $saleTypeService, SaleTypeRepository $saleTypeRepo)
    {
        //parent::__construct();

        $this->saleTypeService = $saleTypeService;
        $this->saleTypeRepo = $saleTypeRepo;
    }

    public function index()
    {
        return Redirect::to('settings/' . ACCOUNT_SALE_TYPES);
    }

    public function getDatatable()
    {
        return $this->saleTypeService->getDatatable(Auth::user()->account_id);
    }

    public function edit($publicId)
    {
        $data = [
            'saleType' => SaleType::scope($publicId)->firstOrFail(),
            'method' => 'PUT',
            'url' => 'sale_types/' . $publicId,
            'title' => trans('texts.edit_sale_type'),
        ];

        return View::make('sale_types.sale_type', $data);
    }

    public function create()
    {
        $data = [
            'saleType' => null,
            'method' => 'POST',
            'url' => 'sale_types',
            'title' => trans('texts.create_sale_type'),
        ];

        return View::make('sale_types.sale_type', $data);
    }

    public function store(SaleTypeRequest $request)
    {
        $this->saleTypeRepo->save($request->input());

        Session::flash('message', trans('texts.created_sale_type'));

        return Redirect::to('settings/' . ACCOUNT_SALE_TYPES);
    }

    public function update(SaleTypeRequest $request, $publicId)
    {
        $this->saleTypeRepo->save($request->input(), $request->entity());

        Session::flash('message', trans('texts.updated_sale_type'));

        return Redirect::to('settings/' . ACCOUNT_SALE_TYPES);
    }

    public function bulk()
    {
        $action = Input::get('bulk_action');
        $ids = Input::get('bulk_public_id');
        $count = $this->saleTypeService->bulk($ids, $action);

        Session::flash('message', trans('texts.archived_sale_type'));

        return Redirect::to('settings/' . ACCOUNT_SALE_TYPES);
    }
}
