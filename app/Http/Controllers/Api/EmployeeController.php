<?php


namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Http\Requests\DataManagementRequest;
use App\Http\Resources\BaseResource;
use App\Models\RiskScore;
use App\Repositories\Contracts\EmployeeRepositoryInterface;
//use App\Repositories\Contracts\RiskScoreRepositoryInterface;
use Carbon\Carbon;
use Helper\Common;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use PharIo\Manifest\Application;
use Repository\RiskScoreRepository;

class EmployeeController extends Controller
{
  protected $repository;

  public function __construct(EmployeeRepositoryInterface $employeeRepository)
  {
    $this->repository = $employeeRepository;
  }



  /**
   * @OA\Get(
   *   path="/api/employee/all",
   *   tags={"Employee"},
   *   summary="Danh sách ................",
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
   *     name="company_branch",
   *     in="query",
   *     @OA\Schema(
   *      type="string",
   *     ),
   *   ),
   *   @OA\Parameter(
   *     name="employee_code",
   *     in="query",
   *     @OA\Schema(
   *      type="string",
   *     ),
   *   ),
   *   @OA\Parameter(
   *     name="employee_name",
   *     in="query",
   *     @OA\Schema(
   *      type="string",
   *     ),
   *   ),
   *   @OA\Parameter(
   *     name="threshold_value",
   *     in="query",
   *     @OA\Schema(
   *      type="boolean",
   *     ),
   *   ),
   *
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
  public function getAll(Request $request)
  {
    $data = $this->repository->getAll($request);
    return $this->responseJson(CODE_SUCCESS,['data'=>BaseResource::collection($data['employees'])] );
  }
  /**
   * @OA\Get(
   *   path="/api/employee",
   *   tags={"Employee"},
   *   summary="Danh sách ................",
   *   operationId="employee_index",
   *   @OA\Response(
   *     response=200,
   *     description="Gửi yêu cầu thành công",
   *     @OA\MediaType(
   *      mediaType="application/json",
   *      example={"code":200,"data":{"employee_code":  ".............","employee_name":  ".............","joining_age_company":  ".............","date_joining_company":  ".............","date_out_company":  ".............","spouse":  ".............","dependents":  ".............","worked_year":  ".............","final_education":  ".............","shortest_service":  ".............","total_worked":  ".............","company_branch":  "............."}}
   *     )
   *   ),
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
  public function index(DataManagementRequest $request)
  {
    $data = $this->repository->index($request);
    return $this->responseJson(CODE_SUCCESS, BaseResource::collection($data));
  }

  /**
   * @OA\Post(
   *   path="/api/employee",
   *   tags={"Employee"},
   *   summary="Thêm mới ..................",
   *   operationId="employee_create",
   *   @OA\RequestBody(
   *       @OA\MediaType(
   *          mediaType="application/json",
   *          example={"data": {}},
   *          @OA\Schema(
   *            required={"data"},
   *           @OA\Property(
   *              property="data",
   *              format="array",
   *              description="Mảng data"
   *            ),
   *         )
   *      )
   *   ),
   *
   *
   *   @OA\Response(
   *     response=200,
   *     description="Send request success",
   *     @OA\MediaType(
   *      mediaType="application/json",
   *      example={"code":200,"data":{"employee_code":  ".............","employee_name":  ".............","joining_age_company":  ".............","date_joining_company":  ".............","date_out_company":  ".............","spouse":  ".............","dependents":  ".............","worked_year":  ".............","final_education":  ".............","shortest_service":  ".............","total_worked":  ".............","company_branch":  "............."}}
   *     )
   *   ),
   *   security={{"auth": {}}},
   * )
   * @return \Illuminate\Http\JsonResponse
   * @throws \Exception
   */
  public function store(DataManagementRequest $request)
  {
    $datamanagement =  $this->repository->create($request->data);
    if (is_int($datamanagement)){
      if ($datamanagement==1){
        return $this->responseJsonError(CODE_CREATE_FAILED, trans('employee_code not null'));
      }else{
        return $this->responseJsonError(CODE_CREATE_FAILED, trans('company_branch not exist'));
      }
    }
    elseif (is_object($datamanagement)){
      return $this->responseJsonError(CODE_CREATE_FAILED, trans('employee_code '.$datamanagement["employee_code"].' must be unique'));
    }
    elseif (!$datamanagement) {
      return $this->responseJsonError(CODE_CREATE_FAILED, trans('errors.something_error'));
    }
    return $this->responseJson(CODE_SUCCESS, $datamanagement);
  }
  /**
   * @OA\Get (
   *   path="/api/employee/detail",
   *   tags={"Employee"},
   *   summary="Chi tiết ............",
   *   operationId="employee_show",
   *   @OA\Parameter(
   *     name="employee_id",
   *     in="query",
   *     required=true,
   *     @OA\Schema(
   *      type="string",
   *     ),
   *   ),
   *   @OA\Parameter(
   *     name="year",
   *     in="query",
   *     @OA\Schema(
   *      type="string",
   *     ),
   *   ),
   *
   *   @OA\Response(
   *     response=200,
   *     description="Gửi yêu cầu thành công",
   *     @OA\MediaType(
   *      mediaType="application/json",
   *      example={"code":200,"data":{"employee_code":  ".............","employee_name":  ".............","joining_age_company":  ".............","date_joining_company":  ".............","date_out_company":  ".............","spouse":  ".............","dependents":  ".............","worked_year":  ".............","final_education":  ".............","shortest_service":  ".............","total_worked":  ".............","company_branch":  "............."}}
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
  public function detail(Request $request)
  {
    try {
      $data = $this->repository->getByEmployeeId($request);
      return $this->responseJson(200, new BaseResource($data));
    } catch (\Exception $exception) {
      return $this->responseJsonError(CODE_CREATE_FAILED, trans('Employee point not found'));
    }
  }
}
