<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BaseResource;
use App\Models\Role;
use Illuminate\Http\Request;
use Repository\RoleRepository;

class RoleController extends BaseController
{
    protected $roleRepository;

    public function __construct(RoleRepository $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }

    /**
     * @OA\Get(
     *   path="/api/roles",
     *   tags={"Roles"},
     *   summary="List Roles",
     *   operationId="role_index",
     *   @OA\Response(
     *     response=200,
     *     description="Gửi yêu cầu thành công",
     *     @OA\MediaType(
     *       mediaType="application/json",
     *       example={"code":200,"data":{{"id":2,"name":"1_Admin","display_name":"hội sở chính","description":"","created_at":1604982690,"updated_at":1604982690},{"id":3,"name":"1_warehouse_manager","display_name":"department","description":"","created_at":1604982691,"updated_at":1604982691}}}
     *     )
     *   ),
     *   @OA\Response(
     *     response=401,
     *     description="Unauthorized",
     *     @OA\MediaType(
     *      mediaType="application/json",
     *      example={"code":401,"message":"Chưa đăng nhập"}
     *     )
     *   ),
     *   @OA\Response(
     *     response=403,
     *     description="Từ chối quyền truy cập",
     *     @OA\MediaType(
     *      mediaType="application/json",
     *      example={"code":403,"message":"Từ chối quyền truy cập"}
     *     )
     *   ),
     *   security={{"auth": {}}},
     * )
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $roles = $this->roleRepository->all();
        return $this->responseJson(200, BaseResource::collection($roles));
    }
    /**
     * @OA\Post(
     *   path="/api/roles",
     *   tags={"Roles"},
     *   summary="Create Role",
     *   operationId="role_store",
     *   @OA\RequestBody(
     *       @OA\MediaType(
     *          mediaType="application/json",
     *          example={"name": "string","display_name":"string","description":"string"},
     *          @OA\Schema(
     *            required={"display_name"},
     *            @OA\Property(
     *              property="name",
     *              format="string",
     *            ),
     *            @OA\Property(
     *              property="display_name",
     *              format="string",
     *            ),
     *            @OA\Property(
     *              property="description",
     *              format="string",
     *            ),
     *         )
     *      )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Gửi yêu cầu thành công",
     *     @OA\MediaType(
     *      mediaType="application/json",
     *      example={"code":200,"data":{"name":"nhan vien cong ty","display_name":"nhan_vien_cong_ty","description":"abc123","updated_at":1604979156,"created_at":1604979156,"id":5}}
     *     )
     *   ),
     *   @OA\Response(
     *     response=401,
     *     description="Unauthorized",
     *     @OA\MediaType(
     *      mediaType="application/json",
     *      example={"code":401,"message":"Chưa đăng nhập"}
     *     )
     *   ),
     *   @OA\Response(
     *     response=403,
     *     description="Từ chối quyền truy cập",
     *     @OA\MediaType(
     *      mediaType="application/json",
     *      example={"code":403,"message":"Từ chối quyền truy cập"}
     *     )
     *   ),
     *   security={{"auth": {}}},
     * )
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(Request $request)
    {
        $role = $this->roleRepository->create($request->all());
        return $this->responseJson(200, new BaseResource($role));
    }

    /**
     * @OA\Get(
     *   path="/api/roles/{id}",
     *   tags={"Roles"},
     *   summary="Role detail",
     *   operationId="role_show",
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
     *     description="Gửi yêu cầu thành công",
     *     @OA\MediaType(
     *       mediaType="application/json",
     *       example={"code":200,"data":{"result":{{"id":1,"title":"string","code":"NCC1","description":null,"phone":null,"fax":null,"website":null,"email":"example@domain.com","tax_code":"Mã số thuế","address_label":"Nơi giao hàng","address_1":"Dia chi 1","address_2":null,"province_id":null,"district_id":null,"ward_id":null,"data":null,"is_active":1,"created_at":1604910110,"updated_at":1604910680}},"pagination":{"display":1,"total_records":1,"per_page":15,"current_page":1,"total_pages":1}}}
     *     )
     *   ),
     *   @OA\Response(
     *     response=401,
     *     description="Unauthorized",
     *     @OA\MediaType(
     *      mediaType="application/json",
     *      example={"code":401,"message":"Chưa đăng nhập"}
     *     )
     *   ),
     *   @OA\Response(
     *     response=403,
     *     description="Từ chối quyền truy cập",
     *     @OA\MediaType(
     *      mediaType="application/json",
     *      example={"code":403,"message":"Từ chối quyền truy cập"}
     *     )
     *   ),
     *   security={{"auth": {}}},
     * )
     * Display a listing of the resource.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $role = $this->roleRepository->find($id);
        return $this->responseJson(200, new BaseResource($role));
    }

    /**
     * @OA\Put(
     *   path="/api/roles/{id}",
     *   tags={"Roles"},
     *   summary="Update Role",
     *   operationId="role_update",
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
     *          example={"name": "string","display_name":"string","description":"string"},
     *          @OA\Schema(
     *            required={"display_name"},
     *            @OA\Property(
     *              property="name",
     *              format="string",
     *            ),
     *            @OA\Property(
     *              property="display_name",
     *              format="string",
     *            ),
     *            @OA\Property(
     *              property="description",
     *              format="string",
     *            ),
     *         )
     *      )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Gửi yêu cầu thành công",
     *     @OA\MediaType(
     *      mediaType="application/json",
     *      example={"code":200,"data":{"name":"Hoi so chinh","display_name":"hoi_so_chinh","description":"Admin","updated_at":1604979156,"created_at":1604979156,"id":5}}
     *     )
     *   ),
     *   @OA\Response(
     *     response=401,
     *     description="Unauthorized",
     *     @OA\MediaType(
     *      mediaType="application/json",
     *      example={"code":401,"message":"Chưa đăng nhập"}
     *     )
     *   ),
     *   @OA\Response(
     *     response=403,
     *     description="Từ chối quyền truy cập",
     *     @OA\MediaType(
     *      mediaType="application/json",
     *      example={"code":403,"message":"Từ chối quyền truy cập"}
     *     )
     *   ),
     *   security={{"auth": {}}},
     * )
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(Request $request, $id)
    {
        $role = $this->roleRepository->update($request->all(), $id);
        return $this->responseJson(200, new BaseResource($role));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
