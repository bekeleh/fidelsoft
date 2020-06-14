<?php

namespace App\Http\Controllers;

use App\Http\Requests\ItemBrandRequest;
use App\Models\ItemBrand;
use App\Ninja\Repositories\ItemBrandRepository;

/**
 * Class ItemBrandApiController.
 */
class ItemBrandApiController extends BaseAPIController
{
    /**
     * @var string
     */
    protected $entityType = ENTITY_ITEM_BRAND;

    /**
     * @var ItemBrandRepository
     */
    protected $itemBrandRepo;

    /**
     * itemBrandApiController constructor.
     *
     * @param ItemBrandRepository $itemBrandRepo
     */
    public function __construct(ItemBrandRepository $itemBrandRepo)
    {
        parent::__construct();

        $this->itemBrandRepo = $itemBrandRepo;
    }

    /**
     * @SWG\Get(
     *   path="/item_brands",
     *   summary="List item_brands",
     *   operationId="listitemBrands",
     *   tags={"item_brand"},
     *   @SWG\Response(
     *     response=200,
     *     description="A list of item_brands",
     *      @SWG\Schema(type="array", @SWG\Items(ref="#/definitions/itemBrand"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     */
    public function index()
    {
        $itemBrands = ItemBrand::scope()->withTrashed()->orderBy('created_at', 'desc');

        return $this->listResponse($itemBrands);
    }

    /**
     * @SWG\Get(
     *   path="/item_brands/{item_brand_id}",
     *   summary="Retrieve a item_brand",
     *   operationId="getitemBrand",
     *   tags={"item_brand"},
     *   @SWG\Parameter(
     *     in="path",
     *     name="item_brand_id",
     *     type="integer",
     *     required=true
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="A single item_brand",
     *      @SWG\Schema(type="object", @SWG\Items(ref="#/definitions/itemBrand"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     * @param ItemBrandRequest $request
     * @return
     */
    public function show(ItemBrandRequest $request)
    {
        return $this->itemResponse($request->entity());
    }

    /**
     * @SWG\Post(
     *   path="/item_brands",
     *   summary="Create a item_brand",
     *   operationId="createitemBrand",
     *   tags={"item_brand"},
     *   @SWG\Parameter(
     *     in="body",
     *     name="body",
     *     @SWG\Schema(ref="#/definitions/itemBrand")
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="New item_brand",
     *      @SWG\Schema(type="object", @SWG\Items(ref="#/definitions/itemBrand"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     * @param ItemBrandRequest $request
     * @return
     */
    public function item_brand(ItemBrandRequest $request)
    {
        $itemBrand = $this->itemBrandRepo->save($request->input());

        return $this->itemResponse($itemBrand);
    }

    /**
     * @SWG\Put(
     *   path="/item_brands/{item_brand_id}",
     *   summary="Update a item_brand",
     *   operationId="updateitemBrand",
     *   tags={"item_brand"},
     *   @SWG\Parameter(
     *     in="path",
     *     name="item_brand_id",
     *     type="integer",
     *     required=true
     *   ),
     *   @SWG\Parameter(
     *     in="body",
     *     name="item_brand",
     *     @SWG\Schema(ref="#/definitions/itemBrand")
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="Updated item item_brand",
     *      @SWG\Schema(type="object", @SWG\Items(ref="#/definitions/itemBrand"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     *
     * @param ItemBrandRequest $request
     * @param mixed $publicId
     * @return
     */
    public function update(ItemBrandRequest $request, $publicId)
    {
        if ($request->action) {
            return $this->handleAction($request);
        }

        $data = $request->input();
        $data['public_id'] = $publicId;
        $itemBrand = $this->itemBrandRepo->save($data, $request->entity());

        return $this->itemResponse($itemBrand);
    }

    /**
     * @SWG\Delete(
     *   path="/item_brands/{item_brand_id}",
     *   summary="Delete a item_brand",
     *   operationId="deleteitemBrand",
     *   tags={"item_brand"},
     *   @SWG\Parameter(
     *     in="path",
     *     name="item_brand_id",
     *     type="integer",
     *     required=true
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="Deleted item_brand",
     *      @SWG\Schema(type="object", @SWG\Items(ref="#/definitions/itemBrand"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     * @param ItemBrandRequest $request
     * @return
     */
    public function destroy(ItemBrandRequest $request)
    {
        $itemBrand = $request->entity();

        $this->itemBrandRepo->delete($itemBrand);

        return $this->itemResponse($itemBrand);
    }
}
