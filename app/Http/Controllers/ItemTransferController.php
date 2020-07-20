<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateItemTransferRequest;
use App\Http\Requests\ItemTransferRequest;
use App\Http\Requests\UpdateItemTransferRequest;
use App\Libraries\Utils;
use App\Models\ItemTransfer;
use App\Models\Product;
use App\Models\Status;
use App\Models\Store;
use App\Ninja\Datatables\ItemTransferDatatable;
use App\Ninja\Repositories\ItemTransferRepository;
use App\Services\ItemTransferService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Redirect;

/**
 * Class ItemTransferController.
 */
class ItemTransferController extends BaseController
{
    protected $itemTransferRepo;
    protected $itemTransferService;
    protected $entityType = ENTITY_ITEM_TRANSFER;

    public function __construct(ItemTransferRepository $itemTransferRepo, ItemTransferService $itemTransferService)
    {
        // parent::__construct();

        $this->itemTransferRepo = $itemTransferRepo;
        $this->itemTransferService = $itemTransferService;
    }


    public function index()
    {
        $this->authorize('view', ENTITY_ITEM_TRANSFER);
        return View::make('list_wrapper', [
            'entityType' => ENTITY_ITEM_TRANSFER,
            'datatable' => new ItemTransferDatatable(),
            'title' => trans('texts.item_transfers'),
        ]);
    }

    public function getDatatable($itemTransferPublicId = null)
    {
        $accountId = Auth::user()->account_id;
        $search = Input::get('sSearch');
        return $this->itemTransferService->getDatatable($accountId, $search);
    }

    public function getDatatableProduct($productPublicId = null)
    {
        return $this->itemTransferService->getDatatableProduct($productPublicId);
    }

    public function getDatatableStore($warehousePublicId = null)
    {
        return $this->itemTransferService->getDatatableStore($warehousePublicId);
    }

    public function getDatatableStatus($statusPublicId = null)
    {
        return $this->itemTransferService->getDatatableStatus($statusPublicId);
    }

    public function create(ItemTransferRequest $request)
    {
        $this->authorize('create', ENTITY_ITEM_TRANSFER);
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
        if ($request->previous_warehouse_id != 0) {
            $previousWarehouse = Store::scope($request->previous_warehouse_id)->firstOrFail();
        } else {
            $previousWarehouse = null;
        }
        if ($request->current_warehouse_id != 0) {
            $currentWarehouse = Store::scope($request->current_warehouse_id)->firstOrFail();
        } else {
            $currentWarehouse = null;
        }

        $data = [
            'status' => $status,
            'product' => $product,
            'previousWarehouse' => $previousWarehouse,
            'currentWarehouse' => $currentWarehouse,
            'itemTransfer' => null,
            'method' => 'POST',
            'url' => 'item_transfers',
            'title' => trans('texts.new_item_transfer'),
            'statusPublicId' => Input::old('status') ? Input::old('status') : $request->status_id,
            'productPublicId' => Input::old('product') ? Input::old('product') : $request->product_id,
            'previousWarehousePublicId' => Input::old('previousWarehouse') ? Input::old('previousWarehouse') : $request->previous_warehouse_id,
            'currentWarehousePublicId' => Input::old('currentWarehouse') ? Input::old('currentWarehouse') : $request->current_warehouse_id,
        ];

        $data = array_merge($data, self::getViewModel());

        return View::make('item_transfers.edit', $data);
    }

    public function store(CreateItemTransferRequest $request)
    {
        $data = $request->input();

        $itemTransfer = $this->itemTransferService->save($data);

        return redirect()->to("item_transfers")->with('success', trans('texts.created_item_transfer'));
    }

    public function edit(ItemTransferRequest $request, $publicId = false, $clone = false)
    {
        $this->authorize('edit', ENTITY_ITEM_TRANSFER);
        $itemTransfer = ItemTransfer::scope($publicId)->withTrashed()->firstOrFail();

        if ($clone) {
            $itemTransfer->id = null;
            $itemTransfer->public_id = null;
            $itemTransfer->deleted_at = null;
            $method = 'POST';
            $url = 'item_transfers';
        } else {
            $method = 'PUT';
            $url = 'item_transfers/' . $itemTransfer->public_id;
        }

        $data = [
            'status' => null,
            'product' => null,
            'previousWarehouse' => null,
            'currentWarehouse' => null,
            'itemTransfer' => $itemTransfer,
            'entity' => $itemTransfer,
            'method' => $method,
            'url' => $url,
            'title' => trans('texts.edit_item_transfer'),
            'statusPublicId' => $itemTransfer->status ? $itemTransfer->status->public_id : null,
            'productPublicId' => $itemTransfer->product ? $itemTransfer->product->public_id : null,
            'previousWarehousePublicId' => $itemTransfer->previousWarehouse ? $itemTransfer->previousWarehouse->public_id : null,
            'currentWarehousePublicId' => $itemTransfer->currentWarehouse ? $itemTransfer->currentWarehouse->public_id : null,
        ];

        $data = array_merge($data, self::getViewModel($itemTransfer));

        return View::make('item_transfers.edit', $data);
    }

    public function update(UpdateItemTransferRequest $request)
    {
        $data = $request->input();

        $itemTransfer = $this->itemTransferService->save($data, $request->entity());

        $action = Input::get('action');
        if (in_array($action, ['archive', 'delete', 'restore', 'invoice', 'add_to_invoice'])) {
            return self::bulk();
        }

        if ($action == 'clone') {
            return redirect()->to(sprintf('item_transfers/%s/clone', $itemTransfer->public_id))->with('success', trans('texts.clone_item_transfer'));
        } else {
            return redirect()->to("item_transfers/{$itemTransfer->public_id}/edit")->with('success', trans('texts.updated_item_transfer'));
        }
    }

    public function cloneItemTransfer(ItemTransferRequest $request, $publicId)
    {
        return self::edit($request, $publicId, true);
    }

    public function bulk()
    {
        $action = Input::get('action');
        $ids = Input::get('public_id') ? Input::get('public_id') : Input::get('ids');

        $count = $this->itemTransferService->bulk($ids, $action);

        $message = Utils::pluralize($action . 'd_item_transfer', $count);

        return $this->returnBulk(ENTITY_ITEM_TRANSFER, $action, $ids)->with('message', $message);
    }

    private static function getViewModel($itemTransfer = false)
    {
        return [
            'data' => Input::old('data'),
            'account' => Auth::user()->account,
            'statuses' => Status::scope()->withActiveOrSelected($itemTransfer ? $itemTransfer->status_id : false)->orderBy('name')->get(),
            'products' => Product::scope()->withActiveOrSelected(false)->products(),
            'previousWarehouses' => Store::scope()->withActiveOrSelected($itemTransfer ? $itemTransfer->previous_warehouse_id : false)->hasQuantity()->orderBy('name')->get(),
            'currentWarehouses' => Store::scope()->withActiveOrSelected($itemTransfer ? $itemTransfer->current_warehouse_id : false)->orderBy('name')->get(),
        ];
    }

    public function show($publicId)
    {
        Session::reflash();

        return Redirect::to("item_transfers/{$publicId}/edit");
    }
}
