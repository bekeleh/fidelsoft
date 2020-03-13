<?php

namespace App\Http\Controllers;

use App\Http\Requests\ItemCategoryRequest;
use App\Models\ItemCategory;
use App\Ninja\Repositories\ItemCategoryRepository;

/**
 * Class ItemCategoryApiController.
 */
class ItemCategoryApiController extends BaseAPIController
{
    /**
     * @var string
     */
    protected $entityType = ENTITY_ITEM_CATEGORY;

    /**
     * @var ItemCategoryRepository
     */
    protected $itemCategoryRepo;

    /**
     * ItemCategoryApiController constructor.
     *
     * @param ItemCategoryRepository $itemCategoryRepo
     */
    public function __construct(ItemCategoryRepository $itemCategoryRepo)
    {
        parent::__construct();

        $this->itemCategoryRepo = $itemCategoryRepo;
    }

    /**
     * @SWG\Get(
     *   path="/item_categories",
     *   summary="List item_categories",
     *   operationId="listItemCategorys",
     *   tags={"item category"},
     *   @SWG\Response(
     *     response=200,
     *     description="A list of item_categories",
     *      @SWG\Schema(type="array", @SWG\Items(ref="#/definitions/ItemCategory"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     */
    public function index()
    {
        $itemCategories = ItemCategory::scope()->withTrashed()->orderBy('created_at', 'desc');

        return $this->listResponse($itemCategories);
    }

    /**
     * @SWG\Get(
     *   path="/item_categories/{item_category_id}",
     *   summary="Retrieve a item category",
     *   operationId="getItemCategory",
     *   tags={"item category"},
     *   @SWG\Parameter(
     *     in="path",
     *     name="item_category_id",
     *     type="integer",
     *     required=true
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="A single item category",
     *      @SWG\Schema(type="object", @SWG\Items(ref="#/definitions/ItemCategory"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     * @param ItemCategoryRequest $request
     * @return
     */
    public function show(ItemCategoryRequest $request)
    {
        return $this->itemResponse($request->entity());
    }

    /**
     * @SWG\Post(
     *   path="/item_categories",
     *   summary="Create a item category",
     *   operationId="createItemCategory",
     *   tags={"item category"},
     *   @SWG\Parameter(
     *     in="body",
     *     name="body",
     *     @SWG\Schema(ref="#/definitions/ItemCategory")
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="New item category",
     *      @SWG\Schema(type="object", @SWG\Items(ref="#/definitions/ItemCategory"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     * @param ItemCategoryRequest $request
     * @return
     */
    public function store(ItemCategoryRequest $request)
    {
        $itemCategory = $this->itemCategoryRepo->save($request->input());

        return $this->itemResponse($itemCategory);
    }

    /**
     * @SWG\Put(
     *   path="/item_categories/{item_category_id}",
     *   summary="Update a item category",
     *   operationId="updateItemCategory",
     *   tags={"item category"},
     *   @SWG\Parameter(
     *     in="path",
     *     name="item_category_id",
     *     type="integer",
     *     required=true
     *   ),
     *   @SWG\Parameter(
     *     in="body",
     *     name="item category",
     *     @SWG\Schema(ref="#/definitions/ItemCategory")
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="Updated item category",
     *      @SWG\Schema(type="object", @SWG\Items(ref="#/definitions/ItemCategory"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     *
     * @param ItemCategoryRequest $request
     * @param mixed $publicId
     * @return
     */
    public function update(ItemCategoryRequest $request, $publicId)
    {
        if ($request->action) {
            return $this->handleAction($request);
        }

        $data = $request->input();
        $data['public_id'] = $publicId;
        $itemCategory = $this->itemCategoryRepo->save($data, $request->entity());

        return $this->itemResponse($itemCategory);
    }

    /**
     * @SWG\Delete(
     *   path="/item_categories/{item_category_id}",
     *   summary="Delete a item category",
     *   operationId="deleteItemCategory",
     *   tags={"item category"},
     *   @SWG\Parameter(
     *     in="path",
     *     name="item_category_id",
     *     type="integer",
     *     required=true
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="Deleted item category",
     *      @SWG\Schema(type="object", @SWG\Items(ref="#/definitions/ItemCategory"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     * @param ItemCategoryRequest $request
     * @return
     */
    public function destroy(ItemCategoryRequest $request)
    {
        $itemCategory = $request->entity();

        $this->itemCategoryRepo->delete($itemCategory);

        return $this->itemResponse($itemCategory);
    }
}
