<?php
/**
 * Created by PhpStorm.
 * User: cuongnt
 * Year: 2023-07-10
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PolicyRequest;
use App\Http\Resources\BaseResource;
use App\Http\Resources\EmotionResource;
use App\Repositories\Contracts\PolicyRepositoryInterface;
use Http\Client\Exception;
use Illuminate\Http\Request;

class PolicyController extends Controller
{

     /**
     * var Repository
     */
    protected $repository;

    public function __construct(PolicyRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @OA\Get(
     *   path="/api/policy",
     *   tags={"Policy"},
     *   summary="List policy",
     *   operationId="policy_index",
     *   @OA\Response(
     *     response=200,
     *     description="Send request success",
     *     @OA\MediaType(
     *      mediaType="application/json",
     *      example={"code":200,"data":{{"id": 1,"name": "..........."}}}
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="page",
     *     in="query",
     *     @OA\Schema(
     *      type="integer",
     *     ),
     *   ),
     *   @OA\Parameter(
     *     name="per_page",
     *     in="query",
     *     @OA\Schema(
     *      type="integer",
     *     ),
     *   ),
     *   @OA\Response(
     *     response=401,
     *     description="Login false",
     *     @OA\MediaType(
     *      mediaType="application/json",
     *      example={"code":401,"message":"Username or password invalid"}
     *     )
     *   ),
     *   security={{"auth": {}}},
     * )
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(PolicyRequest $request)
    {
        $data = $this->repository->list($request->all());
        return $this->responseJson(CODE_SUCCESS, BaseResource::collection($data));
    }

    /**
     * @OA\Post(
     *   path="/api/policy",
     *   tags={"Policy"},
     *   summary="Add new policy",
     *   operationId="policy_create",
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\MediaType(
     *        mediaType="multipart/form-data",
     *        @OA\Schema(
     *          required={"name", "type"},
     *            @OA\Property(
     *                property="name",
     *                description="name",
     *                type="string",
     *             ),
     *            @OA\Property(
     *                property="type",
     *                type = "integer",
     *                enum = {1,2,3,4,5},
     *                description="1:V_FACE, 2:AWS, 3:EC2_admin, 4:EC2_deploy, 5:Git"
     *            ),
     *            @OA\Property(
     *                property="instance_id",
     *                type = "string",
     *                description="required with type is EC2",
     *            ),
     *            @OA\Property(
     *                property="project_name",
     *                type = "string",
     *                description="required with type is EC2",
     *            ),
     *         ),
     *       ),
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Send request success",
     *     @OA\MediaType(
     *      mediaType="application/json",
     *      example={"code":200,"data":{"id": 1,"name": "......"}}
     *     )
     *   ),
     *   security={},
     * )
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function store(PolicyRequest $request)
    {
        try {
            return $this->repository->create($request->all());
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * @OA\Get(
     *   path="/api/policy/{id}",
     *   tags={"Policy"},
     *   summary="Detail policy",
     *   operationId="policy_show",
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     @OA\Schema(
     *      type="string",
     *     ),
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Send request success",
     *     @OA\MediaType(
     *      mediaType="application/json",
     *      example={"code":200,"data":{"id": 1,"name":"......"}}
     *     )
     *   ),
     *   @OA\Response(
     *     response=401,
     *     description="Login false",
     *     @OA\MediaType(
     *      mediaType="application/json",
     *      example={"code":401,"message":"Username or password invalid"}
     *     )
     *   ),
     *   security={{"auth": {}}},
     * )
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $department = $this->repository->find($id);
            return $this->responseJson(CODE_SUCCESS, new BaseResource($department));
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * @OA\Put(
     *   path="/api/policy/{id}",
     *   tags={"Policy"},
     *   summary="Update policy",
     *   operationId="policy_update",
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     @OA\Schema(
     *      type="string",
     *     ),
     *   ),
     *    @OA\RequestBody(
     *     required=true,
     *     @OA\MediaType(
     *        mediaType="multipart/form-data",
     *        @OA\Schema(
     *          required={"name", "type"},
     *            @OA\Property(
     *                property="name",
     *                description="name",
     *                type="string",
     *             ),
     *            @OA\Property(
     *                property="type",
     *                type = "integer",
     *                enum = {1,2,3,4,5},
     *                description="1:V_FACE, 2:AWS, 3:EC2_admin, 4:EC2_deploy, 5:Git"
     *            ),
     *            @OA\Property(
     *                property="instance_id",
     *                type = "string",
     *                description="required with type is EC2",
     *            ),
     *            @OA\Property(
     *                property="project_name",
     *                type = "string",
     *                description="required with type is EC2",
     *            ),
     *         ),
     *       ),
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Send request success",
     *     @OA\MediaType(
     *      mediaType="application/json",
     *      example={"code":200,"data":{"id": 1,"name":  "............."}}
     *     ),
     *   ),
     *   @OA\Response(
     *     response=403,
     *     description="Access Deny permission",
     *     @OA\MediaType(
     *      mediaType="application/json",
     *      example={"code":403,"message":"Access Deny permission"}
     *     ),
     *   ),
     *   security={{"auth": {}}},
     * )
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(PolicyRequest $request, $id)
    {
        return $this->repository->update($request->except(['policy_arn']), $id);
    }

    /**
     * @OA\Delete(
     *   path="/api/policy/{id}",
     *   tags={"Policy"},
     *   summary="Delete policy",
     *   operationId="policy_delete",
     *   @OA\Parameter(
     *      name="id",
     *      in="path",
     *      required=true,
     *     @OA\Schema(
     *      type="string",
     *     ),
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Send request success",
     *     @OA\MediaType(
     *      mediaType="application/json",
     *      example={"code":200,"data":"Send request success"}
     *     )
     *   ),
     *   security={{"auth": {}}},
     * )
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy($id)
    {
        return $this->repository->delete($id);
    }

    /**
     * @OA\Get(
     *   path="/api/policy/project_name",
     *   tags={"Policy"},
     *   summary="List project in EC2",
     *   operationId="policy_project",
     *   @OA\Parameter(
     *     name="instance_id",
     *     in="query",
     *     required=true,
     *     @OA\Schema(
     *      type="string",
     *     ),
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Send request success",
     *     @OA\MediaType(
     *      mediaType="application/json",
     *      example={"code":200,"data":{"id": 1,"name":"......"}}
     *     )
     *   ),
     *   @OA\Response(
     *     response=401,
     *     description="Login false",
     *     @OA\MediaType(
     *      mediaType="application/json",
     *      example={"code":401,"message":"Username or password invalid"}
     *     )
     *   ),
     *   security={{"auth": {}}},
     * )
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProject(PolicyRequest $request)
    {
        try {
            $data = $this->repository->getListData($request->input('instance_id'), 'project');
            return $this->responseJson(CODE_SUCCESS, new BaseResource($data));
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
