<?php
/**
 * Created by PhpStorm.
 * User: cuongnt
 * Year: 2021-06-22
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CompanyBranchRequest;
use App\Repositories\Contracts\CompanyBranchRepositoryInterface;
use App\Http\Resources\BaseResource;
use App\Http\Resources\CompanyBranchResource;
use Illuminate\Http\Request;

class CompanyBranchController extends Controller
{

  /**
   * var Repository
   */
  protected $repository;

  public function __construct(CompanyBranchRepositoryInterface $repository)
  {
    $this->repository = $repository;
  }

  /**
   * @OA\Get(
   *   path="/api/company_branch",
   *   tags={"Company_Branch"},
   *   summary="List company branch ................",
   *   operationId="company_branch_index",
   *   @OA\Response(
   *     response=200,
   *     description="Submit request successfully",
   *     @OA\MediaType(
   *      mediaType="application/json",
   *      example={"code":200,"data":{"id": 1,"name":  ".............","address": "...........","description": ".........."}}
   *     )
   *   ),
   *   @OA\Response(
   *     response=401,
   *     description="Login failed",
   *     @OA\MediaType(
   *      mediaType="application/json",
   *      example={"code":401,"message":"Wrong account or password"}
   *     )
   *   ),
   *   security={{"auth": {}}},
   * )
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function index(CompanyBranchRequest $request)
  {
    $data = $this->repository->orderByRole()->paginate(100);
    return $this->responseJson(CODE_SUCCESS, BaseResource::collection($data));
  }

  /**
   * @OA\Post(
   *   path="/api/company_branch",
   *   tags={"Company_Branch"},
   *   summary="Create company branch ..................",
   *   operationId="company_branch_create",
   *   @OA\RequestBody(
   *       @OA\MediaType(
   *          mediaType="application/json",
   *          example={"name":"string","address": "string", "description": "string"},
   *          @OA\Schema(
   *           @OA\Property(
   *              property="name",
   *              format="string",
   *            ),
   *           @OA\Property(
   *              property="address",
   *              format="string",
   *            ),
   *           @OA\Property(
   *              property="description",
   *              format="string",
   *            ),
   *         )
   *      )
   *   ),
   *
   *   @OA\Response(
   *     response=200,
   *     description="Send request success",
   *     @OA\MediaType(
   *      mediaType="application/json",
   *      example={"code":200,"data":{"id": 1,"name":  ".............","address": "........","description": "......"}}
   *     )
   *   ),
   *   security={{"auth": {}}},
   * )
   * @return \Illuminate\Http\JsonResponse
   * @throws \Exception
   */
  public function store(CompanyBranchRequest $request)
  {
    try {
      $data = $this->repository->create($request->all());
      return $this->responseJson(CODE_SUCCESS, new CompanyBranchResource($data));
    } catch (\Exception $e) {
      throw $e;
    }
  }

  /**
   * @OA\Get(
   *   path="/api/company_branch/{id}",
   *   tags={"Company_Branch"},
   *   summary="Detail CompanyBranch",
   *   operationId="company_branch_show",
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
   *   path="/api/company_branch/{id}",
   *   tags={"Company_Branch"},
   *   summary="Update CompanyBranch",
   *   operationId="company_branch_update",
   *   @OA\Parameter(
   *     name="id",
   *     in="path",
   *     required=true,
   *     @OA\Schema(
   *      type="string",
   *     ),
   *   ),
   *   @OA\RequestBody(
   *       @OA\MediaType(
   *          mediaType="application/json",
   *          example={"name":"string","address": "string", "description": "string"},
   *          @OA\Schema(
   *           @OA\Property(
   *              property="name",
   *              format="string",
   *            ),
   *           @OA\Property(
   *              property="address",
   *              format="string",
   *            ),
   *           @OA\Property(
   *              property="description",
   *              format="string",
   *            ),
   *         )
   *      )
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
  public function update(Request $request, $id)
  {
    $attributes = $request->except([]);
    $data = $this->repository->update($attributes, $id);
    return $this->responseJson(CODE_SUCCESS, new BaseResource($data));
  }

  /**
   * @OA\Delete(
   *   path="/api/company_branch/{id}",
   *   tags={"Company_Branch"},
   *   summary="Delete ..............",
   *   operationId="company_branch_delete",
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
    $this->repository->delete($id);
    return $this->responseJson(200, null, trans('mes.delete_success'));
  }

  /**
   * @OA\Get(
   *   path="/api/company_branch/role",
   *   tags={"Company_Branch"},
   *   summary="Danh sách company_branch theo role................",
   *   operationId="company_branch_index",
   *   @OA\Response(
   *     response=200,
   *     description="Gửi yêu cầu thành công",
   *     @OA\MediaType(
   *      mediaType="application/json",
   *      example={"code":200,"data":{"id": 1,"name":  ".............","address": "...........","description": ".........."}}
   *     )
   *   ),
   *   @OA\Response(
   *     response=401,
   *     description="Đăng nhập thất bại",
   *     @OA\MediaType(
   *      mediaType="application/json",
   *      example={"code":401,"message":"Sai tài khoản hoặc mật khẩu"}
   *     )
   *   ),
   *   security={{"auth": {}}},
   * )
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function getByRole()
  {
    $company_branchs = $this->repository->getByRole();
    return $this->responseJson(CODE_SUCCESS, $company_branchs);
  }
  /**
   * @OA\Get(
   *   path="/api/company_branch/user",
   *   tags={"Company_Branch"},
   *   summary="Danh sách company_branch theo user đăng nhập................",
   *   operationId="company_branch_index",
   *   @OA\Response(
   *     response=200,
   *     description="Gửi yêu cầu thành công",
   *     @OA\MediaType(
   *      mediaType="application/json",
   *      example={"code":200,"data":{"id": 1,"name":  ".............","address": "...........","description": ".........."}}
   *     )
   *   ),
   *   @OA\Response(
   *     response=401,
   *     description="Đăng nhập thất bại",
   *     @OA\MediaType(
   *      mediaType="application/json",
   *      example={"code":401,"message":"Sai tài khoản hoặc mật khẩu"}
   *     )
   *   ),
   *   security={{"auth": {}}},
   * )
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function getByUser()
  {
    $company_branch = $this->repository->getByUser();
    return $this->responseJson(CODE_SUCCESS, $company_branch);
  }

}
