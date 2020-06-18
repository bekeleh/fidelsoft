<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateItemBrandRequest;
use App\Http\Requests\ItemBrandRequest;
use App\Http\Requests\UpdateItemBrandRequest;
use App\Libraries\Utils;
use App\Models\ItemBrand;
use App\Models\ItemCategory;
use App\Ninja\Datatables\ItemBrandDatatable;
use App\Ninja\Repositories\ItemBrandRepository;
use App\Services\ItemBrandService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Redirect;

/**
 * Class ItemBrandController.
 */
class ItemBrandController extends BaseController
{
    protected $itemBrandRepo;
    protected $itemBrandService;
    protected $entityType = ENTITY_ITEM_BRAND;

    public function __construct(ItemBrandRepository $itemBrandRepo, ItemBrandService $itemBrandService)
    {
        // parent::__construct();

        $this->itemBrandRepo = $itemBrandRepo;
        $this->itemBrandService = $itemBrandService;
    }


    public function index()
    {
        $this->authorize('view', ENTITY_ITEM_BRAND);
        return View::make('list_wrapper', [
            'entityType' => ENTITY_ITEM_BRAND,
            'datatable' => new ItemBrandDatatable(),
            'title' => trans('texts.item_brands'),
        ]);
    }

    public function getDatatable($itemBrandPublicId = null)
    {
        $account = Auth::user()->account_id;
        $search = Input::get('sSearch');
        return $this->itemBrandService->getDatatable($account, $search);
    }

    public function getDatatableCategory($categoryPublicId = null)
    {
        return $this->itemBrandService->getDatatableItemCategory($categoryPublicId);
    }

    public function create(ItemBrandRequest $request)
    {
        $this->authorize('create', ENTITY_ITEM_BRAND);
        if ($request->item_category_id != 0) {
            $itemCategory = ItemCategory::scope($request->item_category_id)->firstOrFail();
        } else {
            $itemCategory = null;
        }

        $data = [
            'itemCategory' => $itemCategory,
            'itemBrand' => null,
            'method' => 'POST',
            'url' => 'item_brands',
            'title' => trans('texts.new_item_brand'),
            'itemCategoryPublicId' => Input::old('itemCategory') ? Input::old('itemCategory') : $request->item_category_id,
            'storePublicId' => Input::old('store') ? Input::old('store') : $request->store_id,
        ];

        $data = array_merge($data, self::getViewModel());

        return View::make('item_brands.edit', $data);
    }

    public function store(CreateItemBrandRequest $request)
    {
        $data = $request->input();

        $itemBrand = $this->itemBrandService->save($data);

        return redirect()->to("item_brands/{$itemBrand->public_id}/edit")->with('success', trans('texts.created_item_brand'));
    }

    public function edit(ItemBrandRequest $request, $publicId = false, $clone = false)
    {
        $this->authorize('edit', ENTITY_ITEM_BRAND);
        $itemBrand = ItemBrand::scope($publicId)->withTrashed()->firstOrFail();
        if ($clone) {
            $itemBrand->id = null;
            $itemBrand->public_id = null;
            $itemBrand->deleted_at = null;
            $method = 'POST';
            $url = 'item_brands';
        } else {
            $method = 'PUT';
            $url = 'item_brands/' . $itemBrand->public_id;
        }

        $data = [
            'itemCategory' => null,
            'store' => null,
            'itemBrand' => $itemBrand,
            'entity' => $itemBrand,
            'method' => $method,
            'url' => $url,
            'title' => trans('texts.edit_item_brand'),
            'itemCategoryPublicId' => $itemBrand->itemCategory ? $itemBrand->itemCategory->public_id : null,
        ];

        $data = array_merge($data, self::getViewModel($itemBrand));

        return View::make('item_brands.edit', $data);
    }

    public function update(UpdateItemBrandRequest $request)
    {
        $data = $request->input();

        $itemBrand = $this->itemBrandService->save($data, $request->entity());

        $action = Input::get('action');
        if (in_array($action, ['archive', 'delete', 'restore'])) {
            return self::bulk();
        }

        if ($action == 'clone') {
            return redirect()->to(sprintf('item_brands/%s/clone', $itemBrand->public_id))->with('success', trans('texts.clone_item_brand'));
        } else {
            return redirect()->to("item_brands/{$itemBrand->public_id}/edit")->with('success', trans('texts.updated_item_brand'));
        }
    }

    public function cloneItemBrand(ItemBrandRequest $request, $publicId)
    {
        return self::edit($request, $publicId, true);
    }

    public function bulk()
    {
        $action = Input::get('action');
        $ids = Input::get('public_id') ? Input::get('public_id') : Input::get('ids');

        $count = $this->itemBrandService->bulk($ids, $action);

        $message = Utils::pluralize($action . 'd_item_brand', $count);

        return $this->returnBulk(ENTITY_ITEM_BRAND, $action, $ids)->with('message', $message);
    }

    private static function getViewModel($itemBrand = false)
    {
        return [
            'data' => Input::old('data'),
            'account' => Auth::user()->account,
            'itemCategories' => ItemCategory::scope()->withActiveOrSelected($itemBrand ? $itemBrand->item_category_id : false)->orderBy('name')->get(),
        ];
    }

    public function show($publicId)
    {
        Session::reflash();

        return Redirect::to("item_brands/{$publicId}/edit");
    }
}
