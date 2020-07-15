<?php

namespace App\Http\Controllers;

use App\Http\Requests\CompanyRequest;
use App\Models\Company;
use App\Ninja\Repositories\CompanyRepository;

/**
 * Class CompanyApiController.
 */
class CompanyApiController extends BaseAPIController
{
    protected $entityType = ENTITY_COMPANY;


    protected $CompanyRepo;


    public function __construct(CompanyRepository $CompanyRepo)
    {
        parent::__construct();

        $this->CompanyRepo = $CompanyRepo;
    }

    /**
     * @SWG\Get(
     *   path="/companies",
     *   summary="List companies",
     *   operationId="listCompanies",
     *   tags={"product"},
     *   @SWG\Response(
     *     response=200,
     *     description="A list of companies",
     *      @SWG\Schema(type="array", @SWG\Items(ref="#/definitions/Company"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     */
    public function index()
    {
        $companies = Company::scope()
        ->withTrashed()
        ->orderBy('updated_at', 'desc');

        return $this->listResponse($companies);
    }

    /**
     * @SWG\Get(
     *   path="/companies/{companies_id}",
     *   summary="Retrieve a client type",
     *   operationId="getCompany",
     *   tags={"product"},
     *   @SWG\Parameter(
     *     in="path",
     *     name="companies_id",
     *     type="integer",
     *     required=true
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="A single client type",
     *      @SWG\Schema(type="object", @SWG\Items(ref="#/definitions/Company"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     * @param CompanyRequest $request
     * @return
     */
    public function show(CompanyRequest $request)
    {
        return $this->itemResponse($request->entity());
    }

    /**
     * @SWG\Post(
     *   path="/companies",
     *   summary="Create a client type",
     *   operationId="createCompany",
     *   tags={"product"},
     *   @SWG\Parameter(
     *     in="body",
     *     name="body",
     *     @SWG\Schema(ref="#/definitions/Company")
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="New client type",
     *      @SWG\Schema(type="object", @SWG\Items(ref="#/definitions/Company"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     * @param CompanyRequest $request
     * @return
     */
    public function store(CompanyRequest $request)
    {
        $company = $this->CompanyRepo->save($request->input());

        return $this->itemResponse($company);
    }

    /**
     * @SWG\Put(
     *   path="/companies/{companies_id}",
     *   summary="Update a client type",
     *   operationId="updateCompany",
     *   tags={"product"},
     *   @SWG\Parameter(
     *     in="path",
     *     name="companies_id",
     *     type="integer",
     *     required=true
     *   ),
     *   @SWG\Parameter(
     *     in="body",
     *     name="product",
     *     @SWG\Schema(ref="#/definitions/Company")
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="Updated client type",
     *      @SWG\Schema(type="object", @SWG\Items(ref="#/definitions/Company"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     *
     * @param CompanyRequest $request
     * @param mixed $publicId
     * @return
     */
    public function update(CompanyRequest $request, $publicId)
    {
        if ($request->action) {
            return $this->handleAction($request);
        }

        $data = $request->input();
        $data['public_id'] = $publicId;
        $company = $this->CompanyRepo->save($data, $request->entity());

        return $this->itemResponse($company);
    }

    /**
     * @SWG\Delete(
     *   path="/companies/{companies_id}",
     *   summary="Delete a client type",
     *   operationId="deleteCompany",
     *   tags={"product"},
     *   @SWG\Parameter(
     *     in="path",
     *     name="companies_id",
     *     type="integer",
     *     required=true
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="Deleted client type",
     *      @SWG\Schema(type="object", @SWG\Items(ref="#/definitions/Company"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     * @param CompanyRequest $request
     * @return
     */
    public function destroy(CompanyRequest $request)
    {
        $company = $request->entity();

        $this->CompanyRepo->delete($company);

        return $this->itemResponse($company);
    }
}
