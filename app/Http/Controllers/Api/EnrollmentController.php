<?php
/**
 * Created by PhpStorm.
 * User: cuongnt
 * Year: 2021-06-22
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\EnrollmentRequest;
use App\Models\ConfigRange;
use App\Models\DataManagement;
use App\Models\Enrollment;
use App\Repositories\Contracts\EnrollmentRepositoryInterface;
use App\Http\Resources\BaseResource;
use App\Http\Resources\EnrollmentResource;

//use App\Repositories\EnrollmentRepository;
use Illuminate\Support\Facades\Auth;
use Repository\ConfigRangeRepository;
use Repository\EnrollmentRepository;
use Illuminate\Http\Request;
use App\ConfigQuery\ConfigQuery;
use App\Models\Setting;
use Carbon\Carbon;
use const Grpc\STATUS_FAILED_PRECONDITION;


class EnrollmentController extends Controller
{

  /**
   * var Repository
   */
  protected $repository;

  public function __construct(EnrollmentRepository $repository)
  {
    $this->repository = $repository;
  }

  /**
   * @OA\Get(
   *   path="/api/enrollment",
   *   tags={"Enrollment"},
   *   summary="Danh sách ................",
   *   operationId="enrollment_index",
   *   @OA\Parameter(
   *     name="column_name",
   *     in="query",
   *     @OA\Schema(
   *      type="string",
   *     ),
   *   ),
   *   @OA\Parameter(
   *     name="sort",
   *     in="query",
   *     @OA\Schema(
   *      type="boolean",
   *     ),
   *   ),
   *   @OA\Parameter(
   *     name="candidate_name",
   *     in="query",
   *     @OA\Schema(
   *      type="string",
   *     ),
   *   ),
   *   @OA\Parameter(
   *     name="company_branch_id",
   *     in="query",
   *     @OA\Schema(
   *      type="string",
   *     ),
   *   ),
   *   @OA\Parameter(
   *     name="start_date",
   *     in="query",
   *     @OA\Schema(
   *      type="string",
   *      format="array",
   *     ),
   *   ),
   *   @OA\Parameter(
   *     name="end_date",
   *     in="query",
   *     @OA\Schema(
   *      type="string",
   *      format="array",
   *     ),
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
   *     response=200,
   *     description="Gửi yêu cầu thành công",
   *     @OA\MediaType(
   *      mediaType="application/json",
   *      example={"code":200,"data":{"id": 1,"interview_date":  ".............","candidate_name":  ".............","joining_age":  ".............","spouse":  ".............","dependents":  ".............","worked_years":  ".............","final_education":  ".............","shortest_service":  ".............","company_branch_id":  "............."}}
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
  public function index(Request $request)
  {
    $enrollment = $this->repository->findByEnrollment($request);
    return $this->responseJson(CODE_SUCCESS, BaseResource::collection($enrollment));
  }

  /**
   * @OA\Post(
   *   path="/api/enrollment",
   *   tags={"Enrollment"},
   *   summary="Thêm mới ..................",
   *   operationId="enrollment_create",
   *   @OA\RequestBody(
   *       @OA\MediaType(
   *          mediaType="application/json",
   *          example={"interview_date":"dateTime","candidate_name":"string","joining_age":"tinyint","spouse":{0, 1},"dependents":"tinyint","worked_years":"tinyint","final_education":{0, 1, 2, 3 ,4 ,5, 6},"shortest_service":"tinyint","company_branch_id":"int"},
   *          @OA\Schema(
   *            @OA\Property(
   *              property="interview_date",
   *              format="dateTime",
   *            ),
   *            @OA\Property(
   *              property="candidate_name",
   *              format="string",
   *            ),
   *           @OA\Property(
   *              property="joining_age",
   *              format="tinyInteger",
   *            ),
   *           @OA\Property(
   *              property="spouse",
   *              format="array",
   *              description="Mảng phối ngẫu"
   *            ),
   *           @OA\Property(
   *              property="dependents",
   *              format="tinyInteger",
   *            ),
   *           @OA\Property(
   *              property="worked_years",
   *              format="tinyInteger",
   *            ),
   *           @OA\Property(
   *              property="final_education",
   *              format="array",
   *              description="Mảng học vấn"
   *            ),
   *           @OA\Property(
   *              property="shortest_service",
   *              format="tinyInteger",
   *            ),
   *           @OA\Property(
   *              property="company_branch_id",
   *              format="Integer",
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
   *      example={"code":200,"data":{"id": 1,"interview_date":  ".............","candidate_name":  ".............","joining_age":  ".............","spouse":  ".............","dependents":  ".............","worked_years":  ".............","final_education":  ".............","shortest_service":  ".............","company_branch_id":  ".............","created_by":  ".............","updated_by":  "............."}}
   *     )
   *   ),
   *   security={{"auth": {}}},
   * )
   * @return \Illuminate\Http\JsonResponse
   * @throws \Exception
   */
  public function store(EnrollmentRequest $request)
  {
    try {
      $enrollment = $this->repository->create($request->all());
    } catch (\Exception $exception) {
      return $this->responseJsonError(CODE_ERROR_SERVER, trans('errors.something_error'));
    }
    return $this->responseJson(CODE_SUCCESS, new EnrollmentResource($enrollment));
  }


  /**
   * @OA\Get(
   *   path="/api/enrollment/{id}",
   *   tags={"Enrollment"},
   *   summary="Chi tiết ............",
   *   operationId="enrollment_show",
   *   @OA\Parameter(
   *     name="id",
   *     in="path",
   *     required=true,
   *     @OA\Schema(
   *      type="string",
   *     ),
   *   ),
   *   @OA\Parameter(
   *     name="company",
   *     in="query",
   *     @OA\Schema(
   *      type="string",
   *     ),
   *   ),
   *    @OA\Parameter(
   *     name="user_code",
   *     in="query",
   *     @OA\Schema(
   *      type="string",
   *     ),
   *   ),
   *    @OA\Parameter(
   *     name="user_name",
   *     in="query",
   *     @OA\Schema(
   *      type="string",
   *     ),
   *   ),
   *        *   @OA\Parameter(
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
   *     response=200,
   *     description="Gửi yêu cầu thành công",
   *     @OA\MediaType(
   *      mediaType="application/json",
   *       example={"code":200,"data":{"id": 1,"interview_date":  ".............","candidate_name":  ".............","joining_age":  ".............","spouse":  ".............","dependents":  ".............","worked_years":  ".............","final_education":  ".............","shortest_service":  ".............","company_branch_id":  "............."}}
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
  public function show(Request $request, $id)
  {
    $data = $this->repository->detail($id, $request);
    if (!$data) {
      return $this->responseJsonError(CODE_CREATE_FAILED, trans('Enrollment not found'));
    }
    return $this->responseJson(200, $data[0]);
  }


  /**
   * @OA\Put(
   *   path="/api/enrollment/{id}",
   *   tags={"Enrollment"},
   *   summary="Cập nhật thông tin ",
   *   operationId="enrollment_update",
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
   *          example={"interview_date":"dateTime","candidate_name":"string","joining_age":"tinyint","spouse":{0, 1},"dependents":"tinyint","worked_years":"tinyint","final_education":{0, 1, 2, 3, 4, 5, 6},"shortest_service":"tinyint","company_branch_id":"int"},
   *          @OA\Schema(
   *            required={"key"},
   *            @OA\Property(
   *              property="key",
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
   *      example={"code":200,"data":{"id": 1,"interview_date":  ".............","candidate_name":  ".............","joining_age":  ".............","spouse":  ".............","dependents":  ".............","worked_years":  ".............","final_education":  ".............","shortest_service":  ".............","company_branch_id":  "............."}}
   *     )
   *   ),
   *   @OA\Response(
   *     response=403,
   *     description="Từ chối quyền truy cập",
   *     @OA\MediaType(
   *      mediaType="application/json",
   *      example={"code":403,"message":"Từ chối quyền truy cập"}
   *     ),
   *   ),
   *   security={{"auth": {}}},
   * )
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function update(EnrollmentRequest $request, $id)
  {
    try {
      $attributes = $request->except([]);
      $enrollment = $this->repository->update($attributes, $id);
    } catch (\Exception $exception) {
      return $this->responseJsonError(CODE_ERROR_SERVER, trans('errors.something_error'));
    }
    return $this->responseJson(CODE_SUCCESS, new BaseResource($enrollment));
  }

  /**
   * @OA\Delete(
   *   path="/api/enrollment/{id}",
   *   tags={"Enrollment"},
   *   summary="Xóa ..............",
   *   operationId="enrollment_delete",
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
   *     description="Gửi yêu cầu thành công",
   *     @OA\MediaType(
   *      mediaType="application/json",
   *      example={"code":200,"data":"Gửi yêu cầu thành công"}
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
    $enrollment = $this->repository->delete($id);
    if ($enrollment) {
      return $this->responseJson(CODE_SUCCESS, null, trans('messages.mes.delete_success'));
    }
    return $this->responseJsonError(CODE_DELETE_FAILED, null, trans('messages.mes.delete_error'));
  }
}
