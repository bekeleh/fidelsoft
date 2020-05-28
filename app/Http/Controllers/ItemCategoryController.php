<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateItemCategoryRequest;
use App\Http\Requests\ItemCategoryRequest;
use App\Http\Requests\UpdateItemCategoryRequest;
use App\Libraries\Utils;
use App\Models\ItemCategory;
use App\Ninja\Datatables\ItemCategoryDatatable;
use App\Ninja\Repositories\ItemCategoryRepository;
use App\Services\ItemCategoryService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Redirect;

/**
 * Class ItemCategoryController.
 */
class ItemCategoryController extends BaseController
{

    protected $itemCategoryService;

    protected $itemCategoryRepo;

    /**
     * ItemCategoryController constructor.
     *
     * @param ItemCategoryService $itemCategoryService
     * @param ItemCategoryRepository $itemCategoryRepo
     */
    public function __construct(ItemCategoryService $itemCategoryService, ItemCategoryRepository $itemCategoryRepo)
    {
        //parent::__construct();
        $this->itemCategoryService = $itemCategoryService;
        $this->itemCategoryRepo = $itemCategoryRepo;
    }

    public function index()
    {
        return View::make('list_wrapper', [
            'entityType' => ENTITY_ITEM_CATEGORY,
            'datatable' => new ItemCategoryDatatable(),
            'title' => trans('texts.item_categories'),
            'statuses' => ItemCategory::getStatuses(),
        ]);
    }

    public function show($publicId)
    {
        Session::reflash();

        return Redirect::to("item_categories/$publicId/edit");
    }

    public function getDatatable()
    {
        return $this->itemCategoryService->getDatatable(Auth::user()->account_id, Input::get('sSearch'));
    }

    public function edit(ItemCategoryRequest $request, $publicId, $clone = false)
    {
        Auth::user()->can('view', [ENTITY_ITEM_CATEGORY, $request->entity()]);

        $account = Auth::user()->account;

        $itemCategory = ItemCategory::scope($publicId)->withTrashed()->firstOrFail();

        if ($clone) {
            $itemCategory->id = null;
            $itemCategory->public_id = null;
            $itemCategory->deleted_at = null;
            $url = 'item_categories';
            $method = 'POST';
        } else {
            $url = 'item_categories/' . $publicId;
            $method = 'PUT';
        }

        $data = [
            'account' => $account,
            'itemCategory' => $itemCategory,
            'entity' => $itemCategory,
            'method' => $method,
            'url' => $url,
            'title' => trans('texts.edit_item_category'),
        ];

        return View::make('item_categories.edit', $data);
    }

    public function create(ItemCategoryRequest $request)
    {

        $account = Auth::user()->account;

        $data = [
            'account' => $account,
            'itemCategory' => null,
            'method' => 'POST',
            'url' => 'item_categories',
            'title' => trans('texts.create_item_category'),
        ];

        return View::make('item_categories.edit', $data);
    }

    public function store(CreateItemCategoryRequest $request)
    {
        return $this->save();
    }

    public function update(UpdateItemCategoryRequest $request, $publicId)
    {
        return $this->save($publicId);
    }

    private function save($itemCategoryPublicId = false)
    {
        if ($itemCategoryPublicId) {
            $itemCategory = ItemCategory::scope($itemCategoryPublicId)->withTrashed()->firstOrFail();
        } else {
            $itemCategory = ItemCategory::createNew();
        }
        $this->itemCategoryRepo->save(Input::all(), $itemCategory);

        $action = request('action');
        if (in_array($action, ['archive', 'delete', 'relocation', 'invoice'])) {
            return self::bulk();
        }

        if ($action == 'clone') {
            return redirect()->to(sprintf('item_categories/%s/clone', $itemCategory->public_id))->with('success', trans('texts.clone_item_category'));
        } else {
            return redirect()->to("item_categories/{$itemCategory->public_id}/edit")->with('success', trans('texts.updated_item_category'));
        }
    }

    public function cloneItemCategory(ItemCategoryRequest $request, $publicId)
    {
        return self::edit($request, $publicId, true);
    }

    public function bulk()
    {
        $action = Input::get('action');
        $ids = Input::get('public_id') ? Input::get('public_id') : Input::get('ids');
        $count = $this->itemCategoryService->bulk($ids, $action);

        $message = Utils::pluralize($action . 'd_item_categories', $count);

        return $this->returnBulk(ENTITY_ITEM_CATEGORY, $action, $ids)->with('message', $message);
    }
}
