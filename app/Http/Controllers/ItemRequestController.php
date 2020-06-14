<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateItemRequestRequest;
use App\Http\Requests\ItemRequestRequest;
use App\Http\Requests\UpdateItemRequestRequest;
use App\Libraries\Utils;
use App\Models\Department;
use App\Models\ItemRequest;
use App\Models\Product;
use App\Models\Status;
use App\Models\Store;
use App\Ninja\Datatables\ItemRequestDatatable;
use App\Ninja\Repositories\ItemRequestRepository;
use App\Services\ItemRequestService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;

/**
 * Class ItemRequestController.
 */
class ItemRequestController extends BaseController
{
    protected $itemRequestRepo;
    protected $itemRequestService;
    protected $entityType = ENTITY_ITEM_REQUEST;

    public function __construct(ItemRequestRepository $itemRequestRepo, ItemRequestService $itemRequestService)
    {
        // parent::__construct();

        $this->itemRequestRepo = $itemRequestRepo;
        $this->itemRequestService = $itemRequestService;
    }


    public function index()
    {
        return View::make('list_wrapper', [
            'entityType' => ENTITY_ITEM_REQUEST,
            'datatable' => new ItemRequestDatatable(),
            'title' => trans('texts.item_requests'),
        ]);
    }

    public function getDatatable($itemRequestPublicId = null)
    {
        return $this->itemRequestService->getDatatable(Auth::user()->account_id, Input::get('sSearch'));
    }

    public function getDatatableProduct($productPublicId = null)
    {
        return $this->itemRequestService->getDatatableProduct($productPublicId);
    }

    public function getDatatableDepartment($productPublicId = null)
    {
        return $this->itemRequestService->getDatatableProduct($productPublicId);
    }

    public function getDatatableStore($storePublicId = null)
    {
        return $this->itemRequestService->getDatatableStore($storePublicId);
    }

    public function getDatatableStatus($statusPublicId = null)
    {
        return $this->itemRequestService->getDatatableStatus($statusPublicId);
    }

    public function setDefaultStore()
    {
        $storeId = isset(auth::user()->store->id) ? auth::user()->store->id : null;
        if (!$storeId) {
            $errorMessage = trans('texts.default_store_required', ['link' => link_to('/users', trans('texts.click_here'))]);
            Session::flash('error', $errorMessage);
        }
    }

    public function create(ItemRequestRequest $request)
    {
        $this->setDefaultStore();
        if ($request->status_id != 0) {
            $status = Status::scope($request->status_id)->firstOrFail();
        } else {
            $status = null;
        }
        if ($request->product_id != 0) {
            $product = Product::scope($request->product_id)->firstOrFail();
        } else {
            $product = null;
        }
        if ($request->store_id != 0) {
            $store = Store::scope($request->store_id)->firstOrFail();
        } else {
            $store = null;
        }
        if ($request->department_id != 0) {
            $department = Department::scope($request->department_id)->firstOrFail();
        } else {
            $department = null;
        }

        $data = [
            'status' => $status,
            'product' => $product,
            'store' => $store,
            'department' => $department,
            'itemRequest' => null,
            'method' => 'POST',
            'url' => 'item_requests',
            'title' => trans('texts.new_item_request'),
            'statusPublicId' => Input::old('status') ? Input::old('status') : $request->status_id,
            'productPublicId' => Input::old('product') ? Input::old('product') : $request->product_id,
            'storePublicId' => Input::old('store') ? Input::old('store') : $request->store_id,
            'departmentPublicId' => Input::old('department') ? Input::old('department') : $request->department_id,
        ];

        $data = array_merge($data, self::getViewModel());

        return View::make('item_requests.edit', $data);
    }

    public function store(CreateItemRequestRequest $request)
    {
        $data = $request->input();

        $itemRequest = $this->itemRequestService->save($data);

        return redirect()->to("item_requests")->with('success', trans('texts.created_item_request'));
    }

    public function edit(ItemRequestRequest $request, $publicId = false, $clone = false)
    {
        $this->setDefaultStore();

        $itemRequest = ItemRequest::scope($publicId)->withTrashed()->firstOrFail();

        if ($clone) {
            $itemRequest->id = null;
            $itemRequest->public_id = null;
            $itemRequest->deleted_at = null;
            $method = 'POST';
            $url = 'item_requests';
        } else {
            $method = 'PUT';
            $url = 'item_requests/' . $itemRequest->public_id;
        }

        $data = [
            'status' => null,
            'product' => null,
            'store' => null,
            'department' => null,
            'itemRequest' => $itemRequest,
            'entity' => $itemRequest,
            'method' => $method,
            'url' => $url,
            'title' => trans('texts.edit_item_request'),
            'statusPublicId' => $itemRequest->status ? $itemRequest->status->public_id : null,
            'productPublicId' => $itemRequest->product ? $itemRequest->product->public_id : null,
            'storePublicId' => $itemRequest->store ? $itemRequest->store->public_id : null,
            'departmentPublicId' => $itemRequest->department ? $itemRequest->department->public_id : null,
        ];

        $data = array_merge($data, self::getViewModel($itemRequest));

        return View::make('item_requests.edit', $data);
    }

    public function update(UpdateItemRequestRequest $request)
    {
        $data = $request->input();

        $itemRequest = $this->itemRequestService->save($data, $request->entity());

        $action = Input::get('action');
        if (in_array($action, ['archive', 'delete', 'restore', 'invoice', 'add_to_invoice'])) {
            return self::bulk();
        }

        if ($action == 'clone') {
            return redirect()->to(sprintf('item_requests/%s/clone', $itemRequest->public_id))->with('success', trans('texts.clone_item_request'));
        } else {
            return redirect()->to("item_requests/{$itemRequest->public_id}/edit")->with('success', trans('texts.updated_item_request'));
        }
    }

    public function cloneItemRequest(ItemRequestRequest $request, $publicId)
    {
        return self::edit($request, $publicId, true);
    }

    public function bulk()
    {
        $action = Input::get('action');
        $ids = Input::get('public_id') ? Input::get('public_id') : Input::get('ids');

        $count = $this->itemRequestService->bulk($ids, $action);

        $message = Utils::pluralize($action . 'd_item_request', $count);

        return $this->returnBulk(ENTITY_ITEM_REQUEST, $action, $ids)->with('message', $message);
    }

    private static function getViewModel($itemRequest = false)
    {
        $storeId = isset(auth::user()->store->id) ? auth::user()->store->id : null;
        if (!$storeId) {
            return [];
        }
        return [
            'data' => Input::old('data'),
            'account' => Auth::user()->account,
            'statuses' => Status::Scope()->withActiveOrSelected($itemRequest ? $itemRequest->status_id : false)->orderBy('name')->get(),
            'products' => Product::withCategory('itemBrand.itemCategory'),
            'stores' => Store::Scope()->withActiveOrSelected($itemRequest ? $itemRequest->store_id : false)->where('id', '!=', $storeId)->orderBy('name')->get(),
            'departments' => Department::Scope()->withActiveOrSelected($itemRequest ? $itemRequest->department_id : false)->orderBy('name')->get(),
        ];
    }

    public function show($publicId)
    {
        $itemRequest = ItemRequest::scope($publicId)->withTrashed()->firstOrFail();

        if ($publicId) {
            $method = 'PUT';
            $url = 'item_requests/' . $itemRequest->public_id;
        }

        $data = [
            'status' => null,
            'product' => null,
            'store' => null,
            'department' => null,
            'itemRequest' => $itemRequest,
            'entity' => $itemRequest,
            'method' => $method,
            'url' => $url,
            'title' => trans('texts.edit_item_request'),
            'statusPublicId' => $itemRequest->status ? $itemRequest->status->public_id : null,
            'productPublicId' => $itemRequest->product ? $itemRequest->product->public_id : null,
            'storePublicId' => $itemRequest->store ? $itemRequest->store->public_id : null,
            'departmentPublicId' => $itemRequest->department ? $itemRequest->department->public_id : null,
        ];

        $data = array_merge($data, self::getViewModel($itemRequest));

        return View::make('item_requests.show', $data);
    }

    public function approve(ItemRequest $request)
    {
        $AccountId = Input::get('account_id');
        $PublicId = Input::get('public_id');
        $statusId = Input::get('status_id');
        $productId = Input::get('product_id');
        $requestedStoreId = Input::get('store_id');
        $deliveredQty = Input::get('delivered_qty');
        $dispatchDate = Input::get('dispatch_date');

        $itemRequest = ItemRequest::where('account_id', '=', $AccountId)->where('public_id', '=', $PublicId)->firstOrFail();

        if ($itemRequest) {
            $itemRequest->status_id = !empty($statusId) ? Status::getPrivateId($statusId) : Utils::getStatusId('pending');
            $itemRequest->delivered_qty = $deliveredQty;
            $itemRequest->dispatch_date = !empty($dispatchDate) ? Utils::toSqlDate($dispatchDate) : Carbon::now();

            if ($itemRequest->save()) {
                ItemRequest::quantityAdjustment($productId, $requestedStoreId, $itemRequest->qty, auth::user()->store->id, $deliveredQty);
            }

            return response()->json(['success' => true, 'data' => RESULT_SUCCESS], 200);

        }

        return RESULT_FAILURE;
    }

}
