<?php

namespace App\Http\Controllers;

use App\Http\Requests\BranchRequest;
use App\Http\Requests\CreateBranchRequest;
use App\Http\Requests\UpdateBranchRequest;
use App\Models\Branch;
use App\Ninja\Repositories\BranchRepository;

class BranchApiController extends BaseAPIController
{
    protected $branchRepo;

    protected $entityType = ENTITY_BRANCH;

    public function __construct(BranchRepository $branchRepo)
    {
        parent::__construct();

        $this->branchRepo = $branchRepo;
    }

    /**
     * @SWG\Get(
     *   path="/branchs",
     *   summary="List branchs",
     *   operationId="listBranchs",
     *   tags={"credit"},
     *   @SWG\Response(
     *     response=200,
     *     description="A list of branchs",
     *      @SWG\Schema(type="array", @SWG\Branches(ref="#/definitions/Branch"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     */
    public function index()
    {
        $branchs = Branch::scope()
            ->withTrashed()
            ->orderBy('updated_at', 'desc');

        return $this->listResponse($branchs);
    }

    /**
     * @SWG\Get(
     *   path="/branchs/{branch_id}",
     *   summary="Retrieve a credit",
     *   operationId="getBranch",
     *   tags={"credit"},
     *   @SWG\Parameter(
     *     in="path",
     *     name="branch_id",
     *     type="integer",
     *     required=true
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="A single credit",
     *      @SWG\Schema(type="object", @SWG\Branches(ref="#/definitions/Branch"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     * @param BranchRequest $request
     * @return
     */
    public function show(BranchRequest $request)
    {
        return $this->itemResponse($request->entity());
    }

    /**
     * @SWG\Post(
     *   path="/branchs",
     *   summary="Create a credit",
     *   operationId="createBranch",
     *   tags={"credit"},
     *   @SWG\Parameter(
     *     in="body",
     *     name="credit",
     *     @SWG\Schema(ref="#/definitions/Branch")
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="New credit",
     *      @SWG\Schema(type="object", @SWG\Branches(ref="#/definitions/Branch"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     * @param CreateBranchRequest $request
     * @return
     */
    public function store(CreateBranchRequest $request)
    {
        $branch = $this->branchRepo->save($request->input());

        return $this->itemResponse($branch);
    }

    /**
     * @SWG\Put(
     *   path="/branchs/{branch_id}",
     *   summary="Update a credit",
     *   operationId="updateBranch",
     *   tags={"credit"},
     *   @SWG\Parameter(
     *     in="path",
     *     name="branch_id",
     *     type="integer",
     *     required=true
     *   ),
     *   @SWG\Parameter(
     *     in="body",
     *     name="credit",
     *     @SWG\Schema(ref="#/definitions/Branch")
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="Updated credit",
     *      @SWG\Schema(type="object", @SWG\Branches(ref="#/definitions/Branch"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     *
     * @param UpdateBranchRequest $request
     * @param mixed $publicId
     * @return
     */
    public function update(UpdateBranchRequest $request, $publicId)
    {
        if ($request->action) {
            return $this->handleAction($request);
        }

        $data = $request->input();
        $data['public_id'] = $publicId;
        $branch = $this->branchRepo->save($data, $request->entity());

        return $this->itemResponse($branch);
    }

    /**
     * @SWG\Delete(
     *   path="/branchs/{branch_id}",
     *   summary="Delete a credit",
     *   operationId="deleteBranch",
     *   tags={"credit"},
     *   @SWG\Parameter(
     *     in="path",
     *     name="branch_id",
     *     type="integer",
     *     required=true
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="Deleted credit",
     *      @SWG\Schema(type="object", @SWG\Branches(ref="#/definitions/Branch"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     * @param UpdateBranchRequest $request
     * @return
     */
    public function destroy(UpdateBranchRequest $request)
    {
        $branch = $request->entity();

        $this->branchRepo->delete($branch);

        return $this->itemResponse($branch);
    }
}
