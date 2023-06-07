<?php
/**
 * Created by PhpStorm.
 * User: cuongnt
 * Year: 2021-06-23
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\DataManagementRequest;
use App\Models\DataManagement;
use App\Repositories\Contracts\DataManagementRepositoryInterface;
use App\Http\Resources\BaseResource;
use App\Http\Resources\DataManagementResource;
//use App\Repositories\DataManagementRepository;
use Illuminate\Http\Request;
use Repository\DataManagementRepository;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\Console\Input\Input;

class DataManagementController extends Controller
{

     /**
     * var Repository
     */
    protected $repository;

    public function __construct(DataManagementRepository $repository)
    {
        $this->repository = $repository;
    }

  /**
   * @OA\Get(
   *   path="/api/data_management",
   *   tags={"Data_Management"},
   *   summary="Danh sách ................",
   *   operationId="data_management_index",
   *   @OA\Response(
   *     response=200,
   *     description="Gửi yêu cầu thành công",
   *     @OA\MediaType(
   *      mediaType="application/json",
   *      example={"code":200,"data":{"employee_code":  ".............","staffs_name":  ".............","joining_age_company":  ".............","date_joining_company":  ".............","date_out_company":  ".............","spouse":  ".............","dependents":  ".............","worked_year":  ".............","final_education":  ".............","shortest_service":  ".............","total_worked":  ".............","company_branch":  "............."}}
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
   *   path="/api/data_management",
   *   tags={"Data_Management"},
   *   summary="Thêm mới ..................",
   *   operationId="data_management_create",
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
   *      example={"code":200,"data":{"employee_code":  ".............","staffs_name":  ".............","joining_age_company":  ".............","date_joining_company":  ".............","date_out_company":  ".............","spouse":  ".............","dependents":  ".............","worked_year":  ".............","final_education":  ".............","shortest_service":  ".............","total_worked":  ".............","company_branch":  "............."}}
   *     )
   *   ),
   *   security={{"auth": {}}},
   * )
   * @return \Illuminate\Http\JsonResponse
   * @throws \Exception
   */
  public function store(DataManagementRequest $request)
  {
//    $data = json_decode($request->getContent());
//    $request->get("data");
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
}




